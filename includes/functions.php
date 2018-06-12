<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_dialog_contact_form_option' ) ) {
	/**
	 * Get contact form option
	 *
	 * @param null $option
	 * @param bool $default
	 *
	 * @return mixed
	 */
	function get_dialog_contact_form_option( $option = null, $default = false ) {
		return \DialogContactForm\Utils::get_option( $option, $default );
	}
}

if ( ! function_exists( 'dcf_validation_rules' ) ) {
	/**
	 * All available validation rules
	 *
	 * @return array
	 */
	function dcf_validation_rules() {
		$validationField = array(
			'user_login' => esc_html__( 'User Login', 'dialog-contact-form' ),
			'username'   => esc_html__( 'Username', 'dialog-contact-form' ),
			'user_email' => esc_html__( 'User Email', 'dialog-contact-form' ),
		);

		return apply_filters( 'dialog_contact_form/validation_rules', $validationField );
	}
}

if ( ! function_exists( 'dcf_google_recaptcha_lang' ) ) {
	/**
	 * Google reCAPTCHA languages
	 *
	 * @return array
	 */
	function dcf_google_recaptcha_lang() {
		return \DialogContactForm\Fields\Recaptcha2::lang();
	}
}

if ( ! function_exists( 'dcf_default_fields' ) ) {
	/**
	 * Dialog contact form default fields
	 *
	 * @return array
	 */
	function dcf_default_fields() {
		return array(
			array(
				'field_title'   => esc_html__( 'Your Name', 'dialog-contact-form' ),
				'field_name'    => 'your_name',
				'field_id'      => 'your_name',
				'field_type'    => 'text',
				'options'       => '',
				'number_min'    => '',
				'number_max'    => '',
				'number_step'   => '',
				'field_value'   => '',
				'field_class'   => '',
				'field_width'   => 'is-6',
				'validation'    => array( 'required' ),
				'placeholder'   => '',
				'error_message' => '',
			),
			array(
				'field_title'   => esc_html__( 'Your Email', 'dialog-contact-form' ),
				'field_name'    => 'your_email',
				'field_id'      => 'your_email',
				'field_type'    => 'email',
				'options'       => '',
				'number_min'    => '',
				'number_max'    => '',
				'number_step'   => '',
				'field_value'   => '',
				'field_class'   => '',
				'field_width'   => 'is-6',
				'validation'    => array( 'required', 'email' ),
				'placeholder'   => '',
				'error_message' => '',
			),
			array(
				'field_title'   => esc_html__( 'Subject', 'dialog-contact-form' ),
				'field_name'    => 'subject',
				'field_id'      => 'subject',
				'field_type'    => 'text',
				'options'       => '',
				'number_min'    => '',
				'number_max'    => '',
				'number_step'   => '',
				'field_value'   => '',
				'field_class'   => '',
				'field_width'   => 'is-12',
				'validation'    => array( 'required' ),
				'placeholder'   => '',
				'error_message' => '',
			),
			array(
				'field_title'   => esc_html__( 'Your Message', 'dialog-contact-form' ),
				'field_name'    => 'your_message',
				'field_id'      => 'your_message',
				'field_type'    => 'textarea',
				'options'       => '',
				'number_min'    => '',
				'number_max'    => '',
				'number_step'   => '',
				'field_value'   => '',
				'field_class'   => '',
				'field_width'   => 'is-12',
				'validation'    => array( 'required' ),
				'placeholder'   => '',
				'error_message' => '',
			),
		);
	}
}
