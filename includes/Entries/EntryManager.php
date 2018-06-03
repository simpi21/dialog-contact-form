<?php

namespace DialogContactForm\Entries;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EntryManager {

	/**
	 * Instance of the class
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Entry database table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Entry meta table name
	 *
	 * @var string
	 */
	private $meta_table_name;

	/**
	 * WordPress Database class
	 *
	 * @var \wpdb
	 */
	private $db;

	/**
	 * @return EntryManager
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		global $wpdb;

		$this->db              = $wpdb;
		$this->table_name      = $wpdb->prefix . 'dcf_entries';
		$this->meta_table_name = $wpdb->prefix . 'dcf_entry_meta';

		add_action( 'admin_menu', array( $this, 'add_entry_menu' ) );
		add_filter( 'set-screen-option', array( $this, 'set_screen' ), 10, 3 );
		add_action( 'admin_init', array( $this, 'handle_action' ) );
	}

	/**
	 * Add entries admin menu
	 */
	public function add_entry_menu() {
		$hook = add_submenu_page(
			'edit.php?post_type=dialog-contact-form',
			__( 'Entries', 'dialog-contact-form' ),
			__( 'Entries', 'dialog-contact-form' ),
			'manage_options',
			'dcf-entries',
			array( $this, 'entries_page' )
		);

		add_action( "load-$hook", array( $this, 'screen_option' ) );
	}

	/**
	 * Entries menu page callback
	 */
	public function entries_page() {
		$tab     = isset( $_GET['tab'] ) ? $_GET['tab'] : 'list';
		$form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;
		$id      = isset( $_GET['entry_id'] ) ? intval( $_GET['entry_id'] ) : 0;

		$template = DIALOG_CONTACT_FORM_TEMPLATES . '/admin/entry/form-list.php';

		if ( 'view' === $tab && $id ) {
			$template = DIALOG_CONTACT_FORM_TEMPLATES . '/admin/entry/view.php';
		}

		if ( 'list' === $tab && $form_id ) {
			$template = DIALOG_CONTACT_FORM_TEMPLATES . '/admin/entry/list.php';
		}

		if ( file_exists( $template ) ) {
			include $template;
		}
	}

	/**
	 * Register and configure an admin screen option
	 */
	public function screen_option() {
		$option = 'per_page';
		$args   = array(
			'label'   => __( 'Number of items per page:', 'dialog-contact-form' ),
			'default' => 50,
			'option'  => 'entries_per_page'
		);
		add_screen_option( $option, $args );
	}


	/**
	 * Set screen option value
	 *
	 * @param bool|int $status Screen option value. Default false to skip.
	 * @param string $option The option name.
	 * @param int $value The number of rows to use.
	 *
	 * @return bool
	 */
	public function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function handle_action() {
		$nonce = isset( $_REQUEST['_dcf_nonce'] ) && wp_verify_nonce( $_REQUEST['_dcf_nonce'], 'dcf_entries_list' );
		if ( ! $nonce ) {
			return;
		}

		$redirect_to = ! empty( $_REQUEST['redirect_to'] ) ? rawurldecode( $_REQUEST['redirect_to'] ) : null;
		$referer     = isset( $_REQUEST['_wp_http_referer'] ) ? $_REQUEST['_wp_http_referer'] : null;
		$entry_ids   = isset( $_REQUEST['entry_id'] ) ? $_REQUEST['entry_id'] : 0;
		$action      = $this->current_action();

		if ( 'trash' == $action ) {
			$this->handle_trash_action( $entry_ids );
		}

		if ( 'untrash' == $action ) {
			$this->handle_untrash_action( $entry_ids );
		}

		if ( 'delete' == $action ) {
			$this->handle_delete_action( $entry_ids );
		}

		// Redirect to Referer URL if available
		if ( ! empty( $referer ) ) {
			wp_safe_redirect( $referer );
			exit();
		}

		// Redirect to URL if available
		if ( filter_var( $redirect_to, FILTER_VALIDATE_URL ) !== false ) {
			wp_safe_redirect( $redirect_to );
			exit();
		}
	}

	/**
	 * Get the current action selected from the bulk actions dropdown.
	 *
	 * @return string|false The action name or False if no action was selected
	 */
	private function current_action() {
		if ( isset( $_REQUEST['filter_action'] ) && ! empty( $_REQUEST['filter_action'] ) ) {
			return false;
		}

		if ( isset( $_REQUEST['action'] ) && - 1 != $_REQUEST['action'] ) {
			return $_REQUEST['action'];
		}

		if ( isset( $_REQUEST['action2'] ) && - 1 != $_REQUEST['action2'] ) {
			return $_REQUEST['action2'];
		}

		return false;
	}

	/**
	 * Handle Move to Trash Action
	 *
	 * @param $entry_ids
	 */
	private function handle_trash_action( $entry_ids ) {
		// Check user can run the action
		if ( ! current_user_can( 'delete_pages' ) ) {
			return;
		}

		if ( is_array( $entry_ids ) ) {
			$entry_ids = array_map( 'intval', $entry_ids );
			foreach ( $entry_ids as $id ) {
				$this->db->update( $this->table_name, array( 'status' => 'trash' ), array( 'id' => $id ),
					'%s', '%d' );
			}
		}

		if ( is_numeric( $entry_ids ) ) {
			$this->db->update( $this->table_name, array( 'status' => 'trash' ), array( 'id' => intval( $entry_ids ) ),
				'%s', '%d' );
		}
	}

	/**
	 * Handle Restore Action
	 *
	 * @param $entry_ids
	 */
	private function handle_untrash_action( $entry_ids ) {
		// Check user can run the action
		if ( ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( is_array( $entry_ids ) ) {
			$entry_ids = array_map( 'intval', $entry_ids );
			foreach ( $entry_ids as $id ) {
				$this->db->update( $this->table_name, array( 'status' => 'read' ), array( 'id' => $id ),
					'%s', '%d' );
			}
		}

		if ( is_numeric( $entry_ids ) ) {
			$this->db->update( $this->table_name, array( 'status' => 'read' ), array( 'id' => intval( $entry_ids ) ),
				'%s', '%d' );
		}
	}

	/**
	 * Handle Delete Permanently action
	 *
	 * @param int|array $entry_ids entry id or array of entries ids
	 */
	private function handle_delete_action( $entry_ids ) {
		// Check user can run the action
		if ( ! current_user_can( 'delete_pages' ) ) {
			return;
		}

		if ( is_array( $entry_ids ) ) {
			$entry_ids = array_map( 'intval', $entry_ids );
			foreach ( $entry_ids as $id ) {
				$this->db->delete( $this->table_name, array( 'id' => $id ), '%d' );
				$this->db->delete( $this->meta_table_name, array( 'entry_id' => $id ), '%d' );
			}
		}

		if ( is_numeric( $entry_ids ) ) {
			$id = intval( $entry_ids );
			$this->db->delete( $this->table_name, array( 'id' => $id ), '%d' );
			$this->db->delete( $this->meta_table_name, array( 'entry_id' => $id ), '%d' );
		}
	}
}
