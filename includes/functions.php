<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'array_column' ) ) {
	/**
	 * Return the values from a single column in the input array
	 * array_column for php < 5.5
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
	function array_column( array $array, $column, $index_key = null ) {
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
}

if ( ! function_exists( 'dcf_available_field_types' ) ) {
	/**
	 * Available field types
	 *
	 * @return array
	 */
	function dcf_available_field_types() {
		$fieldType = array(
			'text'     => esc_html__( 'Single Line Text', 'dialog-contact-form' ),
			'email'    => esc_html__( 'Email Address', 'dialog-contact-form' ),
			'url'      => esc_html__( 'Web Address (URL)', 'dialog-contact-form' ),
			'number'   => esc_html__( 'Number', 'dialog-contact-form' ),
			'textarea' => esc_html__( 'Multi Line Text', 'dialog-contact-form' ),
			'password' => esc_html__( 'Password', 'dialog-contact-form' ),
			'date'     => esc_html__( 'Date', 'dialog-contact-form' ),
			'time'     => esc_html__( 'Time', 'dialog-contact-form' ),
			'hidden'   => esc_html__( 'Hidden', 'dialog-contact-form' ),
			'radio'    => esc_html__( 'Multiple choice', 'dialog-contact-form' ),
			'select'   => esc_html__( 'Dropdown', 'dialog-contact-form' ),
			'checkbox' => esc_html__( 'Checkbox', 'dialog-contact-form' ),
			'file'     => esc_html__( 'File', 'dialog-contact-form' ),
		);

		return $fieldType;
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
			'required'   => esc_html__( 'Required', 'dialog-contact-form' ),
			'email'      => esc_html__( 'Email', 'dialog-contact-form' ),
			'url'        => esc_html__( 'URL', 'dialog-contact-form' ),
			'number'     => esc_html__( 'Number', 'dialog-contact-form' ),
			'int'        => esc_html__( 'Integer', 'dialog-contact-form' ),
			'alpha'      => esc_html__( 'Alphabetic', 'dialog-contact-form' ),
			'alnum'      => esc_html__( 'Alphanumeric', 'dialog-contact-form' ),
			'alnumdash'  => esc_html__( 'Alphanumeric and Dash', 'dialog-contact-form' ),
			'user_login' => esc_html__( 'User Login', 'dialog-contact-form' ),
			'username'   => esc_html__( 'Username', 'dialog-contact-form' ),
			'user_email' => esc_html__( 'User Email', 'dialog-contact-form' ),
			'date'       => esc_html__( 'Date', 'dialog-contact-form' ),
			'checked'    => esc_html__( 'Checked', 'dialog-contact-form' ),
			'ip'         => esc_html__( 'IP Address', 'dialog-contact-form' ),
		);

		return $validationField;
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
			'mail_sent_ok'        => esc_html__( 'Thank you for your message. It has been sent successfully.',
				'dialog-contact-form' ),
			'mail_sent_ng'        => esc_html__( 'There was an error trying to send your message. Please try again later.',
				'dialog-contact-form' ),
			'validation_error'    => esc_html__( 'One or more fields have an error. Please check and try again.',
				'dialog-contact-form' ),
			'invalid_required'    => esc_html__( 'The field is required.', 'dialog-contact-form' ),
			'invalid_too_long'    => esc_html__( 'The field is too long.', 'dialog-contact-form' ),
			'invalid_too_short'   => esc_html__( 'The field is too short.', 'dialog-contact-form' ),
			'number_too_small'    => esc_html__( 'The number is smaller than the minimum allowed.',
				'dialog-contact-form' ),
			'number_too_large'    => esc_html__( 'The number is larger than the maximum allowed.',
				'dialog-contact-form' ),
			'invalid_email'       => esc_html__( 'The email address is invalid.', 'dialog-contact-form' ),
			'invalid_url'         => esc_html__( 'The URL is invalid.', 'dialog-contact-form' ),
			'invalid_number'      => esc_html__( 'Please enter a valid number.', 'dialog-contact-form' ),
			'invalid_int'         => esc_html__( 'Please enter a valid integer.', 'dialog-contact-form' ),
			'invalid_alpha'       => esc_html__( 'Please enter only alphabetic letters.', 'dialog-contact-form' ),
			'invalid_alnum'       => esc_html__( 'Please enter only alphabetic and numeric characters.',
				'dialog-contact-form' ),
			'invalid_alnumdash'   => esc_html__( 'only alphanumeric characters, dashes and underscores are permitted.',
				'dialog-contact-form' ),
			'invalid_date'        => esc_html__( 'The date is invalid.', 'dialog-contact-form' ),
			'invalid_ip'          => esc_html__( 'The IP address is invalid.', 'dialog-contact-form' ),
			'invalid_checked'     => esc_html__( 'The field must be checked.', 'dialog-contact-form' ),
			'invalid_user_login'  => esc_html__( 'No user exists with this information.', 'dialog-contact-form' ),
			'invalid_username'    => esc_html__( 'The username does not exists.', 'dialog-contact-form' ),
			'invalid_user_email'  => esc_html__( 'The email does not exists.', 'dialog-contact-form' ),
			'invalid_recaptcha'   => esc_html__( 'Check the checkbox.', 'dialog-contact-form' ),
			'file_too_large'      => esc_html__( 'File size too large.', 'dialog-contact-form' ),
			'invalid_file_format' => esc_html__( 'File format is invalid.', 'dialog-contact-form' ),
		);

		return $messages;
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
			'btnAlign'      => 'left',
			'btnLabel'      => esc_html__( 'Send', 'dialog-contact-form' ),
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
		$siteurl     = get_option( 'siteurl' );
		$senderEmail = str_replace( array( 'https://', 'http://', 'www.' ), '', $siteurl );
		$senderEmail = sprintf( 'noreply@%s', $senderEmail );
		$options     = array(
			'mailer'                   => 0,
			'smpt_host'                => '',
			'smpt_username'            => '',
			'smpt_password'            => '',
			'smpt_port'                => '',
			'encryption'               => '',
			'smpt_from'                => sanitize_email( $senderEmail ),
			'smpt_from_name'           => sanitize_text_field( get_option( 'blogname' ) ),
			'recaptcha_site_key'       => '',
			'recaptcha_secret_key'     => '',
			'recaptcha_theme'          => 'light',
			'recaptcha_lang'           => 'en',
			'spam_message'             => esc_html__( 'There was an error trying to send your message. Please try again later.',
				'dialog-contact-form' ),
			'invalid_recaptcha'        => esc_html__( 'Check the checkbox.', 'dialog-contact-form' ),
			'dialog_button_text'       => esc_html__( 'Leave a message', 'dialog-contact-form' ),
			'dialog_button_background' => '#f44336',
			'dialog_button_color'      => '#f5f5f5',
			'dialog_form_id'           => '',
			'default_style'            => 'enable',
		);

		return $options;
	}
}

if ( ! function_exists( 'dcf_default_mail_template' ) ) {
	/**
	 * Dialog Contact Form default email template
	 *
	 * @return array
	 */
	function dcf_default_mail_template() {
		$blogname = get_option( 'blogname' );
		$siteurl  = get_option( 'siteurl' );
		$from     = esc_html__( 'From:', 'dialog-contact-form' );
		$subject  = esc_html__( 'Subject:', 'dialog-contact-form' );
		$message  = esc_html__( 'Message Body:', 'dialog-contact-form' );
		$sign     = sprintf(
			esc_html__( 'This email was sent from a contact form on %s (%s)', 'dialog-contact-form' ),
			$blogname,
			$siteurl
		);

		$defaults = array(
			'receiver'    => get_option( 'admin_email' ),
			'senderEmail' => '[your_email]',
			'senderName'  => '[your_name]',
			'subject'     => $blogname . ': [subject]',
			'body'        => "$from [your_name] <[your_email]>\n$subject [subject]\n\n$message\n[your_message]\n\n--\n$sign ",
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