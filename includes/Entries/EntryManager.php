<?php

namespace DialogContactForm\Entries;

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
	private $table_name = 'dcf_entries';

	/**
	 * Entry meta table name
	 *
	 * @var string
	 */
	private $meta_table_name = 'dcf_entry_meta';

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
		$id      = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

		switch ( $tab ) {
			case 'view':
				$template = DIALOG_CONTACT_FORM_TEMPLATES . '/admin/entry/view.php';
				break;
			case 'trash':
				$template = DIALOG_CONTACT_FORM_TEMPLATES . '/admin/entry/trash-list.php';
				break;
			case 'list':
			default:
				$template = DIALOG_CONTACT_FORM_TEMPLATES . '/admin/entry/list.php';
				break;
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

		global $wpdb;
		$referer   = isset( $_REQUEST['_wp_http_referer'] ) ? $_REQUEST['_wp_http_referer'] : null;
		$entry_ids = isset( $_REQUEST['entry_id'] ) ? $_REQUEST['entry_id'] : 0;
		$action    = $this->current_action();

		if ( 'trash' == $action ) {
			if ( is_array( $entry_ids ) ) {
				$entry_ids = array_map( 'intval', $entry_ids );
				foreach ( $entry_ids as $id ) {
					$wpdb->update(
						$wpdb->prefix . $this->table_name,
						array( 'status' => 'trash' ),
						array( 'id' => $id ),
						'%s', '%d'
					);
				}
			} else {
				$wpdb->update(
					$wpdb->prefix . $this->table_name,
					array( 'status' => 'trash' ),
					array( 'id' => intval( $entry_ids ) ),
					'%s', '%d'
				);
			}
		}

		// Redirect to Referer URL if available
		if ( ! empty( $referer ) ) {
			wp_safe_redirect( $referer );
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
}
