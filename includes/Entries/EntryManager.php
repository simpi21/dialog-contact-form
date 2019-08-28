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
	 * @return EntryManager
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			add_action( 'admin_menu', array( self::$instance, 'add_entry_menu' ) );
		}

		return self::$instance;
	}

	/**
	 * Add entries admin menu
	 */
	public function add_entry_menu() {
		add_submenu_page(
			'edit.php?post_type=dialog-contact-form',
			__( 'Entries', 'dialog-contact-form' ),
			__( 'Entries', 'dialog-contact-form' ),
			'manage_options',
			'dcf-entries',
			array( $this, 'entry_page_callback' )
		);
	}

	/**
	 * Entry page callback
	 */
	public function entry_page_callback() {
		echo '<div id="dialog-contact-form-admin"></div>';
	}
}
