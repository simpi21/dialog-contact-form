<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if( ! class_exists('Dialog_Contact_Form_Admin') ):

class Dialog_Contact_Form_Admin
{
	private $options;
	private $plugin_path;

	public function __construct( $options, $plugin_path )
	{
		$this->options = $options;
		$this->plugin_path = $plugin_path;

		add_action( 'admin_init', array( $this, 'settings_init') );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function settings_init(){
	    register_setting( 'dialogcf_options', 'dialogcf_options' );
	}

	public function admin_menu () {
		add_options_page(
			__('Dialog Contact Form', 'dialog-contact-form'),
			__('Dialog Contact Form', 'dialog-contact-form'),
			'manage_options',
			'dialogcf_options_page',
			array( $this, 'settings_page' )
		);
	}
	
	public function  settings_page () {
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'dialog-contact-form' ) );
		}

		include_once $this->plugin_path . '/templates/options.php';
	}

}

endif;