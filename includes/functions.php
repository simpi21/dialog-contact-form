<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

		return apply_filters( 'dialog_contact_form_validation_rules', $validationField );
	}
}

if ( ! function_exists( 'dcf_validation_messages' ) ) {
	/**
	 * Dialog contact form default messages
	 *
	 * @return array
	 */
	function dcf_validation_messages() {
		$messages = array(
			'spam_message'          => esc_html__( 'One or more fields have an error. Please check and try again.',
				'dialog-contact-form' ),
			'mail_sent_ok'          => esc_html__( 'Thank you for your message. It has been sent successfully.',
				'dialog-contact-form' ),
			'mail_sent_ng'          => esc_html__( 'There was an error trying to send your message. Please try again later.',
				'dialog-contact-form' ),
			'validation_error'      => esc_html__( 'One or more fields have an error. Please check and try again.',
				'dialog-contact-form' ),

			// Field validation message
			'generic_error'         => esc_html__( 'The value you entered for this field is invalid.',
				'dialog-contact-form' ),
			'invalid_required'      => esc_html__( 'Please fill out this field.', 'dialog-contact-form' ),
			'required_select'       => esc_html__( 'Please choose a value.', 'dialog-contact-form' ),
			'required_select_multi' => esc_html__( 'Please choose at least one value.', 'dialog-contact-form' ),
			'required_checkbox'     => esc_html__( 'Please check this field.', 'dialog-contact-form' ),
			'invalid_too_long'      => esc_html__( 'Please shorten this text to no more than {maxLength} characters. You are currently using {length} characters.',
				'dialog-contact-form' ),
			'invalid_too_short'     => esc_html__( 'Please lengthen this text to {minLength} characters or more. You are currently using {length} characters.',
				'dialog-contact-form' ),
			'number_too_small'      => esc_html__( 'Please select a value that is no less than {min}.',
				'dialog-contact-form' ),
			'number_too_large'      => esc_html__( 'Please select a value that is no more than {max}.',
				'dialog-contact-form' ),
			'invalid_email'         => esc_html__( 'Please enter an email address.', 'dialog-contact-form' ),
			'invalid_url'           => esc_html__( 'Please enter an URL.', 'dialog-contact-form' ),
			'invalid_number'        => esc_html__( 'Please enter a number.', 'dialog-contact-form' ),
			'invalid_date'          => esc_html__( 'Please enter a date.', 'dialog-contact-form' ),
			'invalid_ip'            => esc_html__( 'Please enter an IP address.', 'dialog-contact-form' ),
			'invalid_recaptcha'     => esc_html__( 'Please check the checkbox.', 'dialog-contact-form' ),
			'step_mismatch'         => esc_html__( 'Please select a valid value.', 'dialog-contact-form' ),
			'pattern_mismatch'      => esc_html__( 'Please match the requested format.', 'dialog-contact-form' ),
			'bad_input'             => esc_html__( 'Please enter a number.', 'dialog-contact-form' ),
			// File related errors
			'file_generic_error'    => esc_html__( 'Unknown upload error.', 'dialog-contact-form' ),
			'required_file'         => esc_html__( 'Please upload a file.', 'dialog-contact-form' ),
			'required_file_multi'   => esc_html__( 'Please upload at least one file.', 'dialog-contact-form' ),
			'file_too_large'        => esc_html__( 'Please upload a file no more than {maxSize}.',
				'dialog-contact-form' ),
			'invalid_file_format'   => esc_html__( 'File format is invalid.', 'dialog-contact-form' ),
		);

		return apply_filters( 'dialog_contact_form_validation_messages', $messages );
	}
}

if ( ! function_exists( 'dcf_default_configuration' ) ) {
	/**
	 * Dialog Contact Form default configuration
	 *
	 * @return array
	 */
	function dcf_default_configuration() {
		$defaults = array(
			'labelPosition' => 'both',
			'btnLabel'      => esc_html__( 'Send', 'dialog-contact-form' ),
			'btnAlign'      => 'left',
			'reset_form'    => 'yes',
			'recaptcha'     => 'no',
		);

		return $defaults;
	}
}

if ( ! function_exists( 'dcf_default_options' ) ) {
	/**
	 * Dialog contact form default options
	 *
	 * @return array
	 */
	function dcf_default_options() {
		$options = array(

			// SMTP Settings
			'mailer'                   => 0,
			'smpt_host'                => '',
			'smpt_username'            => '',
			'smpt_password'            => '',
			'smpt_port'                => '',
			'encryption'               => '',

			// Dialog Form Style
			'dialog_form_id'           => '',
			'dialog_button_background' => '#f44336',
			'dialog_button_color'      => '#f5f5f5',
			'default_style'            => 'enable',
			'dialog_button_text'       => esc_html__( 'Leave a message', 'dialog-contact-form' ),

			// MailChimp Settings
			'mailchimp_api_key'        => '',

			// reCAPTCHA Settings
			'recaptcha_site_key'       => '',
			'recaptcha_secret_key'     => '',
			'recaptcha_theme'          => 'light',
			'recaptcha_lang'           => 'en',
		);

		$messages = dcf_validation_messages();

		return array_merge( $options, $messages );
	}
}

if ( ! function_exists( 'dcf_default_mail_template' ) ) {
	/**
	 * Dialog Contact Form default email template
	 *
	 * @return array
	 */
	function dcf_default_mail_template() {
		$defaults = array(
			'receiver'    => '[system:admin_email]',
			'senderEmail' => '[your_email]',
			'senderName'  => '[your_name]',
			'subject'     => '[system:blogname] : [subject]',
			'body'        => '[all_fields_table]',
		);

		return $defaults;
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
		$default_options = dcf_default_options();
		$options         = get_option( 'dialog_contact_form', $default_options );

		$option = trim( $option );
		if ( empty( $option ) ) {
			return $options;
		}

		$value = null;

		// Distinguish between `false` as a default, and not passing one.
		if ( func_num_args() > 1 ) {
			$value = $default;
		}

		if ( isset( $options[ $option ] ) ) {
			$value = $options[ $option ];
		}

		return $value;
	}
}

if ( ! function_exists( 'dcf_autocomplete_attribute_values' ) ) {
	/**
	 * List of possible value for input autocomplete attribute
	 *
	 * @return array
	 */
	function dcf_autocomplete_attribute_values() {
		return array(
			'on'                 => __( 'On', 'dialog-contact-form' ),
			'off'                => __( 'Off', 'dialog-contact-form' ),
			// Name
			'name'               => __( 'Full name', 'dialog-contact-form' ),
			'honorific-prefix'   => __( 'Prefix or title (e.g. "Mr.", "Ms.", "Dr.")', 'dialog-contact-form' ),
			'given-name'         => __( 'First name', 'dialog-contact-form' ),
			'additional-name'    => __( 'Middle name', 'dialog-contact-form' ),
			'family-name'        => __( 'Last name', 'dialog-contact-form' ),
			'honorific-suffix'   => __( 'Suffix (e.g. "Jr.", "B.Sc.")', 'dialog-contact-form' ),
			'nickname'           => __( 'Nickname', 'dialog-contact-form' ),
			// Address
			'street-address'     => __( 'Street address (multiple lines)', 'dialog-contact-form' ),
			'address-line1'      => __( 'Address Line 1', 'dialog-contact-form' ),
			'address-line2'      => __( 'Address Line 2', 'dialog-contact-form' ),
			'address-line3'      => __( 'Address Line 3', 'dialog-contact-form' ),
			'address-level2'     => __( 'City', 'dialog-contact-form' ),
			'address-level1'     => __( 'State or Province', 'dialog-contact-form' ),
			'country'            => __( 'Country Codes - ISO 3166', 'dialog-contact-form' ),
			'country-name'       => __( 'Country Name', 'dialog-contact-form' ),
			'postal-code'        => __( 'Postal/ZIP Code', 'dialog-contact-form' ),
			// Others
			'email'              => __( 'Email', 'dialog-contact-form' ),
			'tel'                => __( 'Full telephone number, including country code', 'dialog-contact-form' ),
			'url'                => __( 'Website URL', 'dialog-contact-form' ),
			'username'           => __( 'Username', 'dialog-contact-form' ),
			'current-password'   => __( 'Current Password', 'dialog-contact-form' ),
			'new-password'       => __( 'New Password', 'dialog-contact-form' ),
			'organization-title' => __( 'Job title (e.g. "Software Engineer")', 'dialog-contact-form' ),
			'organization'       => __( 'Organization/Company name', 'dialog-contact-form' ),
			'bday'               => __( 'Birthday', 'dialog-contact-form' ),
			'sex'                => __( 'Gender identity (e.g. Female, Male)', 'dialog-contact-form' ),
		);
	}
}
