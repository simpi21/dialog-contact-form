<?php

if ( ! function_exists( 'array_column' ) ) {
	/**
	 * array_column for php < 5.5
	 *
	 * @param  array $input
	 * @param  integer|string $column_key
	 * @param  integer|string $index_key
	 *
	 * @return array
	 */
	function array_column( $input, $column_key, $index_key = null ) {
		$arr = array_map( function ( $d ) use ( $column_key, $index_key ) {
			if ( ! isset( $d[ $column_key ] ) ) {
				return null;
			}
			if ( $index_key !== null ) {
				return array( $d[ $index_key ] => $d[ $column_key ] );
			}

			return $d[ $column_key ];
		}, $input );

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

if ( ! function_exists( 'dcf_generate_random_code' ) ) {
	/**
	 * Generate random characters by given length
	 *
	 * @param  integer $characters
	 *
	 * @return string
	 */
	function dcf_generate_random_code( $characters = 6 ) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code     = '';
		$i        = 0;
		while ( $i < $characters ) {
			$code .= substr( $possible, mt_rand( 0, strlen( $possible ) - 1 ), 1 );
			$i ++;
		}

		return $code;
	}
}

if ( ! function_exists( 'dcf_create_captcha' ) ) {
	/**
	 * Create captcha image from given width, height and text
	 *
	 * @param $text
	 * @param string $width
	 * @param string $height
	 *
	 * @return string
	 */
	function dcf_create_captcha( $text, $width = '120', $height = '40' ) {

		// Check if PHP GD extension is enabled
		if ( ! function_exists( 'gd_info' ) ) {
			return;
		}

		// Create a new palette based image
		$image = imagecreate( $width, $height );

		// Set the colours
		$background_color = imagecolorallocate( $image, 255, 255, 255 );
		$text_color       = imagecolorallocate( $image, 20, 40, 100 );
		$noise_color      = imagecolorallocate( $image, 190, 199, 224 );

		// Generate random dots in background
		for ( $i = 0; $i < ( $width * $height ) / 3; $i ++ ) {
			$cx = mt_rand( 0, $width ); // x-coordinate of the center.
			$cy = mt_rand( 0, $height ); // y-coordinate of the center.
			imagefilledellipse( $image, $cx, $cy, 1, 1, $noise_color );
		}

		// Generate random lines in background
		for ( $i = 0; $i < ( $width * $height ) / 150; $i ++ ) {
			$x1 = mt_rand( 0, $width ); // x-coordinate for first point.
			$y1 = mt_rand( 0, $height ); // y-coordinate for first point.
			$x2 = $x1; // x-coordinate for second point.
			$y2 = $y1; // y-coordinate for second point.
			imageline( $image, $x1, $y1, $x2, $y2, $noise_color );
		}


		// font size will be 55% of the image height
		$font_size = $height * 0.55;
		// The name of the TrueType font file (can be a URL)
		$fontfile = DIALOG_CONTACT_FORM_PATH . '/assets/fonts/ArchitectsDaughter.ttf';

		// Create a bounding box of a text using TrueType fonts
		$textbox = imagettfbbox( $font_size, 0, $fontfile, $text );

		// Write text to the image using TrueType fonts
		$x = ( $width - $textbox[4] ) / 2;
		$y = ( $height - $textbox[5] ) / 2;
		imagettftext( $image, $font_size, 0, $x, $y, $text_color, $fontfile, $text );

		// Turn on output buffering
		ob_start();
		// Output image to browser or file
		imagejpeg( $image, null, 80 );
		// Return the contents of the output buffer
		$image_data = ob_get_contents();
		// Clean (erase) the output buffer and turn off output buffering
		ob_end_clean();

		// Destroy the image
		imagedestroy( $image );

		return $image_data;
	}
}

if ( ! function_exists( 'dcf_available_field_types' ) ) {
	/**
	 * Available field types
	 *
	 * @return array
	 */
	function dcf_available_field_types() {
		$fieldType = [
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
			//'file'     => esc_html__( 'File', 'dialog-contact-form' ),
		];

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
		$validationField = [
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
		];

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
		$messages = [
			'mail_sent_ok'       => esc_html__( 'Thank you for your message. It has been sent successfully.', 'dialog-contact-form' ),
			'mail_sent_ng'       => esc_html__( 'There was an error trying to send your message. Please try again later.', 'dialog-contact-form' ),
			'validation_error'   => esc_html__( 'One or more fields have an error. Please check and try again.', 'dialog-contact-form' ),
			'invalid_required'   => esc_html__( 'The field is required.', 'dialog-contact-form' ),
			'invalid_too_long'   => esc_html__( 'The field is too long.', 'dialog-contact-form' ),
			'invalid_too_short'  => esc_html__( 'The field is too short.', 'dialog-contact-form' ),
			'number_too_small'   => esc_html__( 'The number is smaller than the minimum allowed.', 'dialog-contact-form' ),
			'number_too_large'   => esc_html__( 'The number is larger than the maximum allowed.', 'dialog-contact-form' ),
			'invalid_email'      => esc_html__( 'The email address is invalid.', 'dialog-contact-form' ),
			'invalid_url'        => esc_html__( 'The URL is invalid.', 'dialog-contact-form' ),
			'invalid_number'     => esc_html__( 'Please enter a valid number.', 'dialog-contact-form' ),
			'invalid_int'        => esc_html__( 'Please enter a valid integer.', 'dialog-contact-form' ),
			'invalid_alpha'      => esc_html__( 'Please enter only alphabetic letters.', 'dialog-contact-form' ),
			'invalid_alnum'      => esc_html__( 'Please enter only alphabetic and numeric characters.', 'dialog-contact-form' ),
			'invalid_alnumdash'  => esc_html__( 'only alphanumeric characters, dashes and underscores are permitted.', 'dialog-contact-form' ),
			'invalid_date'       => esc_html__( 'The date is invalid.', 'dialog-contact-form' ),
			'invalid_ip'         => esc_html__( 'The IP address is invalid.', 'dialog-contact-form' ),
			'invalid_checked'    => esc_html__( 'The field must be checked.', 'dialog-contact-form' ),
			'invalid_user_login' => esc_html__( 'No user exists with this information.', 'dialog-contact-form' ),
			'invalid_username'   => esc_html__( 'The username does not exists.', 'dialog-contact-form' ),
			'invalid_user_email' => esc_html__( 'The email does not exists.', 'dialog-contact-form' ),
		];

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
		$blogname    = get_option( 'blogname' );
		$siteurl     = get_option( 'siteurl' );
		$senderEmail = str_replace( [ 'https://', 'http://', 'www.' ], '', $siteurl );
		$senderEmail = sprintf( 'mail@%s', $senderEmail );
		$from        = esc_html__( 'From:', 'dialog-contact-form' );
		$subject     = esc_html__( 'Subject:', 'dialog-contact-form' );
		$message     = esc_html__( 'Message Body:', 'dialog-contact-form' );
		$sign        = sprintf(
			esc_html__( 'This email was sent from a contact form on %s (%s)', 'dialog-contact-form' ),
			$blogname,
			$siteurl
		);

		$defaults = [
			'receiver'      => get_option( 'admin_email' ),
			'senderEmail'   => $senderEmail,
			'senderName'    => $blogname,
			'subject'       => $blogname . ': %subject%',
			'labelPosition' => 'both',
			'btnAlign'      => 'left',
			'btnLabel'      => esc_html__( 'Send', 'dialog-contact-form' ),
			'body'          => "$from %your_name% <%your_email%>\n$subject %subject%\n\n$message\n%your_message%\n\n--\n$sign ",
		];

		return $defaults;
	}
}

if ( ! function_exists( 'dcf_default_mail_template' ) ) {
	/**
	 * Dialog Contact Form default email template
	 *
	 * @return array
	 */
	function dcf_default_mail_template() {
		$blogname    = get_option( 'blogname' );
		$siteurl     = get_option( 'siteurl' );
		$senderEmail = str_replace( [ 'https://', 'http://', 'www.' ], '', $siteurl );
		$senderEmail = sprintf( 'mail@%s', $senderEmail );
		$from        = esc_html__( 'From:', 'dialog-contact-form' );
		$subject     = esc_html__( 'Subject:', 'dialog-contact-form' );
		$message     = esc_html__( 'Message Body:', 'dialog-contact-form' );
		$sign        = sprintf(
			esc_html__( 'This email was sent from a contact form on %s (%s)', 'dialog-contact-form' ),
			$blogname,
			$siteurl
		);

		$defaults = [
			'receiver'    => get_option( 'admin_email' ),
			'senderEmail' => $senderEmail,
			'senderName'  => $blogname,
			'subject'     => $blogname . ': %subject%',
			'body'        => "$from %your_name% <%your_email%>\n$subject %subject%\n\n$message\n%your_message%\n\n--\n$sign ",
		];

		return $defaults;
	}
}