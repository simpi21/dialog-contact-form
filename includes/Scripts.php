<?php

namespace DialogContactForm;

use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Scripts {

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * Form validation messages
	 *
	 * @var array
	 */
	private $validation_messages = array();

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return self
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			add_action( 'admin_head', [ self::$instance, 'localize_data' ], 9 );
			add_action( 'wp_head', [ self::$instance, 'localize_data' ], 9 );

			add_action( 'admin_enqueue_scripts', array( self::$instance, 'admin_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( self::$instance, 'frontend_scripts' ), 30 );
		}

		return self::$instance;
	}

	/**
	 * Global localize data both for admin and frontend
	 */
	public static function localize_data() {
		$is_user_logged_in = is_user_logged_in();

		$data = [
			'homeUrl'        => home_url(),
			'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
			'isUserLoggedIn' => $is_user_logged_in,
			'restRoot'       => esc_url_raw( rest_url( 'dialog-contact-form/v1' ) ),
		];

		if ( $is_user_logged_in ) {
			$data['restNonce'] = wp_create_nonce( 'wp_rest' );
		}

		echo '<script>window.dcfSettings = ' . wp_json_encode( $data ) . '</script>';
	}

	/**
	 * Load admin scripts
	 *
	 * @param $hook
	 */
	public function admin_scripts( $hook ) {
		global $post_type;
		if ( ( $post_type != DIALOG_CONTACT_FORM_POST_TYPE )
		     && ( 'dialog-contact-form_page_dcf-settings' != $hook )
		     && ( 'dialog-contact-form_page_dcf-forms' != $hook )
		     && ( 'dialog-contact-form_page_dcf-entries' != $hook ) ) {
			return;
		}

		$suffix = ( defined( "SCRIPT_DEBUG" ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style( 'dialog-contact-form-admin',
			DIALOG_CONTACT_FORM_ASSETS . '/css/admin.css',
			array( 'wp-color-picker' ), DIALOG_CONTACT_FORM_VERSION, 'all' );

		wp_enqueue_script( 'wp-color-picker-alpha',
			DIALOG_CONTACT_FORM_ASSETS . '/lib/wp-color-picker-alpha/wp-color-picker-alpha' . $suffix . '.js',
			array( 'wp-color-picker' ), '2.1.3', true );

		wp_enqueue_script( 'select2', DIALOG_CONTACT_FORM_ASSETS . '/lib/select2/select2' . $suffix . '.js',
			array( 'jquery' ), '4.0.5', true );

		wp_enqueue_style( 'select2', DIALOG_CONTACT_FORM_ASSETS . '/css/select2.css',
			array(), DIALOG_CONTACT_FORM_VERSION, 'all' );

		wp_enqueue_script( 'dialog-contact-form-admin', DIALOG_CONTACT_FORM_ASSETS . '/js/admin.js',
			array(
				'jquery',
				'select2',
				'jquery-ui-tabs',
				'jquery-ui-sortable',
				'jquery-ui-draggable',
				'jquery-ui-accordion',
				'wp-color-picker-alpha'
			),
			dialog_contact_form()->get_version(), true );
	}

	/**
	 * Load plugin front-end scripts
	 */
	public function frontend_scripts() {
		global $is_IE;

		$enabled_style = Utils::get_option( 'default_style', 'enable' );
		$hl            = Utils::get_option( 'recaptcha_lang', 'en' );
		$captcha_url   = add_query_arg( array( 'hl' => $hl ), 'https://www.google.com/recaptcha/api.js' );

		if ( 'disable' != $enabled_style ) {
			wp_enqueue_style( 'dialog-contact-form', DIALOG_CONTACT_FORM_ASSETS . '/css/frontend.css',
				array(), DIALOG_CONTACT_FORM_VERSION, 'all' );
		}

		// Polyfill for IE
		if ( $is_IE ) {
			wp_enqueue_script( 'dialog-contact-form-polyfill', DIALOG_CONTACT_FORM_ASSETS . '/js/polyfill.js',
				array(), null, false );
		}

		wp_enqueue_script( 'dialog-contact-form', DIALOG_CONTACT_FORM_ASSETS . '/js/frontend.js',
			array(), DIALOG_CONTACT_FORM_VERSION, true );

		wp_register_script( 'dialog-contact-form-recaptcha', $captcha_url, '', null, true );

		wp_localize_script( 'dialog-contact-form', 'DialogContactForm', $this->localize_script() );
	}

	/**
	 * Get dynamic variables that will pass to javaScript variables
	 *
	 * @return array
	 */
	private function localize_script() {
		$variables = array(
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'nonce'          => wp_create_nonce( 'dialog_contact_form_nonce' ),
			'selector'       => '.dcf-form',
			'fieldClass'     => '.dcf-has-error',
			'errorClass'     => '.dcf-error-message',
			'loadingClass'   => '.is-loading',
			'submitBtnClass' => '.dcf-submit',
		);

		return array_merge( $variables, $this->get_validation_messages() );
	}

	/**
	 * Get validation messages
	 *
	 * @return array
	 */
	public function get_validation_messages() {
		if ( empty( $this->validation_messages ) ) {
			$messages  = Utils::validation_messages();
			$options   = Utils::get_option();
			$_messages = array();
			foreach ( $messages as $key => $message ) {
				$_messages[ $key ] = ! empty( $options[ $key ] ) ? $options[ $key ] : $message;
			}

			$this->validation_messages = $_messages;
		}

		return $this->validation_messages;
	}
}