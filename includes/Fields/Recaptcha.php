<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

class Recaptcha extends Abstract_Field {

	/**
	 * Render field html for frontend display
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	public function render( $field = array() ) {
		// TODO: Implement render() method.
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool true on success, false on failure
	 */
	public function validate( $value = '' ) {
		return self::_validate();
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 */
	public function sanitize( $value ) {
		// TODO: Implement sanitize() method.
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function get_value() {
		// TODO: Implement get_value() method.
	}

	public static function _validate() {
		$secret_key   = get_dialog_contact_form_option( 'recaptcha_secret_key' );
		$captcha_code = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : null;
		if ( empty( $captcha_code ) || empty( $secret_key ) ) {
			return false;
		}


		$_response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
			'body' => array(
				'secret'   => $secret_key,
				'response' => $captcha_code,
				'remoteip' => self::get_remote_ip_addr(),
			)
		) );
		$body      = json_decode( wp_remote_retrieve_body( $_response ), true );

		if ( isset( $body['success'] ) && true === $body['success'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Get user IP address
	 *
	 * @return string
	 */
	private static function get_remote_ip_addr() {
		if ( empty( $_SERVER['REMOTE_ADDR'] ) ) {
			return '';
		}

		if ( filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP ) !== false ) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return '';
	}
}