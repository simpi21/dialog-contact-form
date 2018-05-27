<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

class Recaptcha extends Abstract_Field {

	/**
	 * Google reCAPTCHA site verify API endpoint
	 */
	const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_id',
	);

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

	/**
	 * Validate Google reCAPTCHA
	 *
	 * @return bool
	 */
	public static function _validate() {
		$secret_key   = get_dialog_contact_form_option( 'recaptcha_secret_key' );
		$captcha_code = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : null;
		if ( empty( $captcha_code ) || empty( $secret_key ) ) {
			return false;
		}

		$_response = wp_remote_post( self::SITE_VERIFY_URL, array(
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
	 * Google reCAPTCHA languages
	 *
	 * @return array
	 */
	public static function lang() {
		return array(
			"ar"     => esc_html__( "Arabic", 'dialog-contact-form' ),
			"af"     => esc_html__( "Afrikaans", 'dialog-contact-form' ),
			"am"     => esc_html__( "Amharic", 'dialog-contact-form' ),
			"hy"     => esc_html__( "Armenian", 'dialog-contact-form' ),
			"az"     => esc_html__( "Azerbaijani", 'dialog-contact-form' ),
			"eu"     => esc_html__( "Basque", 'dialog-contact-form' ),
			"bn"     => esc_html__( "Bengali", 'dialog-contact-form' ),
			"bg"     => esc_html__( "Bulgarian", 'dialog-contact-form' ),
			"ca"     => esc_html__( "Catalan", 'dialog-contact-form' ),
			"zh-HK"  => esc_html__( "Chinese (Hong Kong)", 'dialog-contact-form' ),
			"zh-CN"  => esc_html__( "Chinese (Simplified)", 'dialog-contact-form' ),
			"zh-TW"  => esc_html__( "Chinese (Traditional)", 'dialog-contact-form' ),
			"hr"     => esc_html__( "Croatian", 'dialog-contact-form' ),
			"cs"     => esc_html__( "Czech", 'dialog-contact-form' ),
			"da"     => esc_html__( "Danish", 'dialog-contact-form' ),
			"nl"     => esc_html__( "Dutch", 'dialog-contact-form' ),
			"en-GB"  => esc_html__( "English (UK)", 'dialog-contact-form' ),
			"en"     => esc_html__( "English (US)", 'dialog-contact-form' ),
			"et"     => esc_html__( "Estonian", 'dialog-contact-form' ),
			"fil"    => esc_html__( "Filipino", 'dialog-contact-form' ),
			"fi"     => esc_html__( "Finnish", 'dialog-contact-form' ),
			"fr"     => esc_html__( "French", 'dialog-contact-form' ),
			"fr-CA"  => esc_html__( "French (Canadian)", 'dialog-contact-form' ),
			"gl"     => esc_html__( "Galician", 'dialog-contact-form' ),
			"ka"     => esc_html__( "Georgian", 'dialog-contact-form' ),
			"de"     => esc_html__( "German", 'dialog-contact-form' ),
			"de-AT"  => esc_html__( "German (Austria)", 'dialog-contact-form' ),
			"de-CH"  => esc_html__( "German (Switzerland)", 'dialog-contact-form' ),
			"el"     => esc_html__( "Greek", 'dialog-contact-form' ),
			"gu"     => esc_html__( "Gujarati", 'dialog-contact-form' ),
			"iw"     => esc_html__( "Hebrew", 'dialog-contact-form' ),
			"hi"     => esc_html__( "Hindi", 'dialog-contact-form' ),
			"hu"     => esc_html__( "Hungarain", 'dialog-contact-form' ),
			"is"     => esc_html__( "Icelandic", 'dialog-contact-form' ),
			"id"     => esc_html__( "Indonesian", 'dialog-contact-form' ),
			"it"     => esc_html__( "Italian", 'dialog-contact-form' ),
			"ja"     => esc_html__( "Japanese", 'dialog-contact-form' ),
			"kn"     => esc_html__( "Kannada", 'dialog-contact-form' ),
			"ko"     => esc_html__( "Korean", 'dialog-contact-form' ),
			"lo"     => esc_html__( "Laothian", 'dialog-contact-form' ),
			"lv"     => esc_html__( "Latvian", 'dialog-contact-form' ),
			"lt"     => esc_html__( "Lithuanian", 'dialog-contact-form' ),
			"ms"     => esc_html__( "Malay", 'dialog-contact-form' ),
			"ml"     => esc_html__( "Malayalam", 'dialog-contact-form' ),
			"mr"     => esc_html__( "Marathi", 'dialog-contact-form' ),
			"mn"     => esc_html__( "Mongolian", 'dialog-contact-form' ),
			"no"     => esc_html__( "Norwegian", 'dialog-contact-form' ),
			"fa"     => esc_html__( "Persian", 'dialog-contact-form' ),
			"pl"     => esc_html__( "Polish", 'dialog-contact-form' ),
			"pt"     => esc_html__( "Portuguese", 'dialog-contact-form' ),
			"pt-BR"  => esc_html__( "Portuguese (Brazil)", 'dialog-contact-form' ),
			"pt-PT"  => esc_html__( "Portuguese (Portugal)", 'dialog-contact-form' ),
			"ro"     => esc_html__( "Romanian", 'dialog-contact-form' ),
			"ru"     => esc_html__( "Russian", 'dialog-contact-form' ),
			"sr"     => esc_html__( "Serbian", 'dialog-contact-form' ),
			"si"     => esc_html__( "Sinhalese", 'dialog-contact-form' ),
			"sk"     => esc_html__( "Slovak", 'dialog-contact-form' ),
			"sl"     => esc_html__( "Slovenian", 'dialog-contact-form' ),
			"es"     => esc_html__( "Spanish", 'dialog-contact-form' ),
			"es-419" => esc_html__( "Spanish (Latin America)", 'dialog-contact-form' ),
			"sw"     => esc_html__( "Swahili", 'dialog-contact-form' ),
			"sv"     => esc_html__( "Swedish", 'dialog-contact-form' ),
			"ta"     => esc_html__( "Tamil", 'dialog-contact-form' ),
			"te"     => esc_html__( "Telugu", 'dialog-contact-form' ),
			"th"     => esc_html__( "Thai", 'dialog-contact-form' ),
			"tr"     => esc_html__( "Turkish", 'dialog-contact-form' ),
			"uk"     => esc_html__( "Ukrainian", 'dialog-contact-form' ),
			"ur"     => esc_html__( "Urdu", 'dialog-contact-form' ),
			"vi"     => esc_html__( "Vietnamese", 'dialog-contact-form' ),
			"zu"     => esc_html__( "Zulu", 'dialog-contact-form' ),
		);
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