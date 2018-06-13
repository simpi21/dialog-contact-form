<?php

namespace DialogContactForm;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Utils {

	/**
	 * Return the values from a single column in the input array
	 *
	 * @param  array $array A multi-dimensional array (record set) from which to pull a column of values.
	 * @param  mixed $column The column of values to return. This value may be
	 * the integer key of the column you wish to retrieve, or it may be
	 * the string key name for an associative array. It may also be
	 * NULL to return complete arrays (useful together with index_key to reindex the array).
	 * @param  mixed $index_key The column to use as the index/keys for the returned array.
	 * This value may be the integer key of the column, or it may be the string key name.
	 *
	 * @return array Returns an array of values representing a single column from the input array.
	 */
	public static function array_column( array $array, $column, $index_key = null ) {
		if ( function_exists( 'array_column' ) ) {
			return array_column( $array, $column, $index_key );
		}

		// For php < 5.5
		$arr = array_map( function ( $d ) use ( $column, $index_key ) {
			if ( ! isset( $d[ $column ] ) ) {
				return null;
			}
			if ( $index_key !== null ) {
				return array( $d[ $index_key ] => $d[ $column ] );
			}

			return $d[ $column ];
		}, $array );

		if ( $index_key !== null ) {
			$tmp = array();
			foreach ( $arr as $ar ) {
				$tmp[ key( $ar ) ] = current( $ar );
			}
			$arr = $tmp;
		}

		return $arr;
	}

	/**
	 * Get contact form option
	 *
	 * @param string $option
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public static function get_option( $option = null, $default = false ) {
		$default_options = static::default_options();
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

	/**
	 * Available field types
	 *
	 * @return array
	 */
	public static function field_types() {
		$fieldType = array(
			'text'       => array(
				'label' => esc_html__( 'Text', 'dialog-contact-form' ),
				'icon'  => 'fas fa-text-width',
			),
			'textarea'   => array(
				'label' => esc_html__( 'Textarea', 'dialog-contact-form' ),
				'icon'  => 'fas fa-paragraph',
			),
			'acceptance' => array(
				'label' => esc_html__( 'Acceptance', 'dialog-contact-form' ),
				'icon'  => 'far fa-check-square',
			),
			'checkbox'   => array(
				'label' => esc_html__( 'Checkbox', 'dialog-contact-form' ),
				'icon'  => 'fas fa-list',
			),
			'email'      => array(
				'label' => esc_html__( 'Email', 'dialog-contact-form' ),
				'icon'  => 'far fa-envelope',
			),
			'password'   => array(
				'label' => esc_html__( 'Password', 'dialog-contact-form' ),
				'icon'  => 'fas fa-key',
			),
			'number'     => array(
				'label' => esc_html__( 'Number', 'dialog-contact-form' ),
				'icon'  => 'fas fa-sort-numeric-up',
			),
			'hidden'     => array(
				'label' => esc_html__( 'Hidden', 'dialog-contact-form' ),
				'icon'  => 'far fa-eye-slash',
			),
			'date'       => array(
				'label' => esc_html__( 'Date', 'dialog-contact-form' ),
				'icon'  => 'far fa-calendar-alt',
			),
			'time'       => array(
				'label' => esc_html__( 'Time', 'dialog-contact-form' ),
				'icon'  => 'far fa-clock',
			),
			'url'        => array(
				'label' => esc_html__( 'URL', 'dialog-contact-form' ),
				'icon'  => 'fas fa-link',
			),
			'ip'         => array(
				'label' => esc_html__( 'IP Address', 'dialog-contact-form' ),
				'icon'  => 'fas fa-mouse-pointer',
			),
			'radio'      => array(
				'label' => esc_html__( 'Radio', 'dialog-contact-form' ),
				'icon'  => 'far fa-dot-circle',
			),
			'select'     => array(
				'label' => esc_html__( 'Select', 'dialog-contact-form' ),
				'icon'  => 'fas fa-angle-down',
			),
			'file'       => array(
				'label' => esc_html__( 'File', 'dialog-contact-form' ),
				'icon'  => 'fas fa-upload',
			),
			'html'       => array(
				'label' => esc_html__( 'HTML', 'dialog-contact-form' ),
				'icon'  => 'fas fa-code',
			),
		);

		return apply_filters( 'dialog_contact_form/field_types', $fieldType );
	}

	/**
	 * All available validation rules
	 *
	 * @return array
	 */
	public static function validation_rules() {
		$validationField = array(
			'user_login' => esc_html__( 'User Login', 'dialog-contact-form' ),
			'username'   => esc_html__( 'Username', 'dialog-contact-form' ),
			'user_email' => esc_html__( 'User Email', 'dialog-contact-form' ),
		);

		return apply_filters( 'dialog_contact_form/validation_rules', $validationField );
	}

	/**
	 * Dialog contact form default messages
	 *
	 * @return array
	 */
	public static function validation_messages() {
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

		return apply_filters( 'dialog_contact_form/validation_messages', $messages );
	}

	/**
	 * Dialog contact form default options
	 *
	 * @return array
	 */
	public static function default_options() {
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

		$messages = static::validation_messages();

		return array_merge( $options, $messages );
	}

	/**
	 * List of possible value for input autocomplete attribute
	 *
	 * @return array
	 */
	public static function autocomplete_values() {
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

	/**
	 * Get user IP address
	 *
	 * @return string
	 */
	public static function get_remote_ip() {
		$server_ip_keys = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		foreach ( $server_ip_keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) && filter_var( $_SERVER[ $key ], FILTER_VALIDATE_IP ) ) {
				return $_SERVER[ $key ];
			}
		}

		// Fallback local ip.
		return '127.0.0.1';
	}

	/**
	 * Get user browser name
	 *
	 * @return string
	 */
	public static function get_user_agent() {
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 );
		}

		return '';
	}
}