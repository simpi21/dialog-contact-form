<?php

namespace DialogContactForm;

use DialogContactForm\Supports\Mailer;
use DialogContactForm\Supports\Validate;

class Submission {

	/**
	 * @var object
	 */
	protected static $instance;

	/**
	 * Form validation messages
	 *
	 * @var array
	 */
	private $messages = array();

	/**
	 * Global form options
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * @return Submission
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * DialogContactFormProcessRequest constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_dcf_submit_form', array( $this, 'process_ajax_form_submission' ) );
		add_action( 'wp_ajax_nopriv_dcf_submit_form', array( $this, 'process_ajax_form_submission' ) );
		add_action( 'template_redirect', array( $this, 'process_non_ajax_form_submission' ) );

		// Remove mail attachment
		add_action( 'dcf_after_send_mail', array( $this, 'remove_attachment_file' ), 0, 2 );
		add_action( 'dcf_after_ajax_send_mail', array( $this, 'remove_attachment_file' ), 0, 2 );

		$this->options = get_option( 'dialog_contact_form' );
	}

	/**
	 * @param $messages
	 *
	 * @return array
	 */
	private function get_validation_messages() {
		if ( $this->messages ) {
			return $this->messages;
		}

		$default  = dcf_validation_messages();
		$messages = array();
		foreach ( $default as $key => $message ) {
			$messages[ $key ] = ! empty( $this->options[ $key ] ) ? $this->options[ $key ] : $message;
		}

		$this->messages = $messages;

		return $this->messages;
	}

	/**
	 * Get for all configurations data
	 *
	 * @param array $options options for all form
	 * @param int $form_id
	 * @param array $fields
	 * @param array $config
	 * @param array $messages
	 * @param array $mail
	 *
	 * @return array
	 */
	private static function get_form_data( $options, $form_id, $fields, $config, $messages, $mail ) {
		$data = array(
			'global_options' => $options,
			'form_id'        => $form_id,
			'form_fields'    => $fields,
			'form_options'   => $config,
			'form_messages'  => $messages,
			'form_mail'      => $mail,
		);

		return $data;
	}

	/**
	 * @param $form_id
	 *
	 * @return array
	 */
	private static function get_form_settings( $form_id ) {
		$config   = get_post_meta( $form_id, '_contact_form_config', true );
		$mail     = get_post_meta( $form_id, '_contact_form_mail', true );
		$fields   = get_post_meta( $form_id, '_contact_form_fields', true );
		$messages = get_post_meta( $form_id, '_contact_form_messages', true );

		return array( $config, $mail, $fields, $messages );
	}

	/**
	 * Process non AJAX form submission
	 */
	public function process_non_ajax_form_submission() {
		$form_id = isset( $_POST['_user_form_id'] ) ? intval( $_POST['_user_form_id'] ) : 0;
		// If it is spam, do nothing
		if ( $this->is_spam( $form_id ) ) {
			return;
		}

		$default_options = dcf_default_options();
		$options         = wp_parse_args( get_option( 'dialog_contact_form' ), $default_options );
		list( $config, $mail, $fields, $messages ) = self::get_form_settings( $form_id );
		$form_options = self::get_form_data( $options, $form_id, $fields, $config, $messages, $mail );

		/**
		 * @var array $form_options
		 */
		do_action( 'dcf_before_validation', $form_options );

		$error_data = $this->validate_form_data( $fields, $messages, $config, $options );

		/**
		 * @var array $form_options
		 * @var array $error_data
		 */
		do_action( 'dcf_after_validation', $form_options, $error_data );

		// Exit if there is any error
		if ( count( $error_data ) > 0 ) {
			$GLOBALS['_dcf_errors']           = $error_data;
			$GLOBALS['_dcf_validation_error'] = $messages['validation_error'];

			return;
		}

		// If form upload a file, handle here
		$attachments = $this->upload_attachments( $fields );

		do_action( 'dcf_before_send_mail', $form_options, $attachments );
		$mail_sent = $this->send_mail( $fields, $mail, $attachments );
		do_action( 'dcf_after_send_mail', $form_options, $attachments, $mail_sent );

		if ( $mail_sent ) {
			$GLOBALS['_dcf_mail_sent_ok'] = $messages['mail_sent_ok'];
		} else {
			$GLOBALS['_dcf_validation_error'] = $messages['mail_sent_ng'];
		}
	}

	/**
	 * Process AJAX form submission
	 */
	public function process_ajax_form_submission() {
		$default_options = dcf_default_options();
		$options         = wp_parse_args( get_option( 'dialog_contact_form' ), $default_options );
		$form_id         = isset( $_POST['_user_form_id'] ) ? intval( $_POST['_user_form_id'] ) : 0;

		if ( $this->is_spam( $form_id ) ) {
			wp_send_json( array(
				'status'  => 'fail',
				'message' => esc_attr( $options['spam_message'] ),
			), 403 );
		}

		list( $config, $mail, $fields, $messages ) = self::get_form_settings( $form_id );
		$form_options = self::get_form_data( $options, $form_id, $fields, $config, $messages, $mail );

		// Validate form data
		do_action( 'dcf_before_ajax_validation', $form_options );
		$error_data = $this->validate_form_data( $fields, $messages, $config, $options );
		do_action( 'dcf_after_ajax_validation', $form_options, $error_data );

		// If there is a error, send error response
		if ( $error_data ) {
			wp_send_json( array(
				'status'     => 'fail',
				'message'    => esc_attr( $messages['validation_error'] ),
				'validation' => $error_data,
			), 422 );
		}

		// If form upload a file, handle here
		$attachments = $this->upload_attachments( $fields );

		do_action( 'dcf_before_ajax_send_mail', $form_options, $attachments );

		// Send mail to user
		$mail_sent = $this->send_mail( $fields, $mail, $attachments );

		do_action( 'dcf_after_ajax_send_mail', $form_options, $attachments, $mail_sent );

		if ( $mail_sent ) {
			wp_send_json( array(
				'status'  => 'success',
				'message' => esc_attr( $messages['mail_sent_ok'] ),
			), 200 );
		}

		wp_send_json( array(
			'status'  => 'fail',
			'message' => esc_attr( $messages['mail_sent_ng'] ),
		), 500 );
	}

	/**
	 * Validate user submitted form data
	 *
	 * @param array $fields
	 * @param array $messages
	 * @param array $config
	 * @param array $options
	 *
	 * @return mixed
	 */
	private function validate_form_data( $fields, $messages, $config, $options ) {
		// Validate Form Data
		$errorData = array();
		foreach ( $fields as $field ) {
			$field_name = isset( $field['field_name'] ) ? $field['field_name'] : '';
			$value      = isset( $_POST[ $field_name ] ) ? $_POST[ $field_name ] : null;
			if ( 'file' == $field['field_type'] ) {
				$message = $this->validate_file_field( $field );
			} else {
				$message = $this->validate_post_field( $value, $field );
			}

			if ( count( $message ) > 0 ) {
				$errorData[ $field_name ] = $message;
			}
		}

		// If Google reCAPTCHA enabled, verify it
		if ( isset( $config['recaptcha'] ) && $config['recaptcha'] == 'yes' ) {
			if ( ! $this->validate_google_recaptcha() ) {
				$errorData['dcf_recaptcha'] = array( $messages['invalid_recaptcha'] );
			}
		}

		return $errorData;
	}

	/**
	 * Validate form field
	 *
	 * @param mixed $value
	 * @param array $field
	 *
	 * @return array
	 */
	public function validate_post_field( $value, $field ) {
		$messages       = $this->get_validation_messages();
		$message        = array();
		$validate_rules = is_array( $field['validation'] ) ? $field['validation'] : array();

		// If field type is email, url, number or date then add appropriate validation rule
		if ( in_array( $field['field_type'], array( 'email', 'url', 'number', 'date' ) ) ) {
			$validate_rules[] = $field['field_type'];
		}

		// Make sure, validation rules are unique
		$validate_rules = array_unique( $validate_rules );

		// Loop through all validation rules and
		// Add error message if any error occur
		foreach ( $validate_rules as $rule ) {
			switch ( $rule ) {
				case 'required':
					if ( ! Validate::required( $value ) ) {
						$message[] = $messages['invalid_required'];
					}
					break;
				case 'email':
					if ( ! Validate::email( $value ) ) {
						$message[] = $messages['invalid_email'];
					}
					break;
				case 'url':
					if ( ! Validate::url( $value ) ) {
						$message[] = $messages['invalid_url'];
					}
					break;
				case 'number':
					if ( ! Validate::number( $value ) ) {
						$message[] = $messages['invalid_number'];
					}
					break;
				case 'int':
					if ( ! Validate::int( $value ) ) {
						$message[] = $messages['invalid_int'];
					}
					break;
				case 'alpha':
					if ( ! Validate::alpha( $value ) ) {
						$message[] = $messages['invalid_alpha'];
					}
					break;
				case 'alnum':
					if ( ! Validate::alnum( $value ) ) {
						$message[] = $messages['invalid_alnum'];
					}
					break;
				case 'alnumdash':
					if ( ! Validate::alnumdash( $value ) ) {
						$message[] = $messages['invalid_alnumdash'];
					}
					break;
				case 'date':
					if ( ! Validate::date( $value ) ) {
						$message[] = $messages['invalid_date'];
					}
					break;
				case 'checked':
					if ( ! Validate::checked( $value ) ) {
						$message[] = $messages['invalid_checked'];
					}
					break;
				case 'ip':
					if ( ! Validate::ip( $value ) ) {
						$message[] = $messages['invalid_ip'];
					}
					break;
				case 'user_login':
					if ( ! Validate::user_login( $value ) ) {
						$message[] = $messages['invalid_user_login'];
					}
					break;
				case 'username':
					if ( ! Validate::username( $value ) ) {
						$message[] = $messages['invalid_username'];
					}
					break;
				case 'user_email':
					if ( ! Validate::user_email( $value ) ) {
						$message[] = $messages['invalid_user_email'];
					}
					break;
				default:
					break;
			}
		}

		// If field is not required, hide message if field is empty
		if ( ! in_array( 'required', $validate_rules ) &&
		     ! Validate::required( $value ) ) {
			$message = array();
		}

		// If user custom message exists, use it
		if ( count( $message ) > 0 && mb_strlen( $field['error_message'], 'UTF-8' ) > 10 ) {
			$message = array( $field['error_message'] );
		}

		// Return field validation messages
		return $message;
	}

	/**
	 * Validate file field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public function validate_file_field( $field ) {
		$messages = $this->get_validation_messages();

		$message        = array();
		$validate_rules = is_array( $field['validation'] ) ? $field['validation'] : array();

		// Check if global $_FILES exists
		if ( ! isset( $_FILES[ $field['field_name'] ] ) ) {
			return $message;
		}

		$name     = $_FILES[ $field['field_name'] ]['name'];
		$tmp_name = $_FILES[ $field['field_name'] ]['tmp_name'];
		$size     = $_FILES[ $field['field_name'] ]['size'];

		if ( empty( $tmp_name ) || empty( $name ) ) {
			$message[] = $messages['invalid_required'];
		} else {
			// Get file mime type for uploaded file
			$finfo     = new \finfo( FILEINFO_MIME_TYPE );
			$file_type = $finfo->file( $tmp_name );

			// Get file extension from uploaded file name
			$temp_ext           = explode( '.', $name );
			$original_extension = strtolower( end( $temp_ext ) );

			// Get file extension from allowed mime types
			$ext = array_search( $file_type, get_allowed_mime_types(), true );

			// Check if uploaded file mime type is allowed
			if ( false === strpos( $ext, $original_extension ) ) {
				$message[] = $messages['invalid_file_format'];
			}

			// check file size here.
			$max_upload_size = wp_max_upload_size();
			if ( $size > $max_upload_size ) {
				$message[] = $messages['file_too_large'];
			}
		}
		// If field is not required, hide message if field is empty
		if ( ! in_array( 'required', $validate_rules ) ) {
			if ( empty( $tmp_name ) || empty( $name ) ) {
				$message = array();
			}
		}

		// If user custom message exists, use it
		if ( count( $message ) > 0 && mb_strlen( $field['error_message'], 'UTF-8' ) > 10 ) {
			$message = array( $field['error_message'] );
		}

		return $message;
	}

	/**
	 * Verify Google reCAPTCHA code
	 *
	 * @return bool
	 */
	private function validate_google_recaptcha() {
		// If reCAPTCHA key or secret is empty, return true
		if ( empty( $this->options['recaptcha_site_key'] ) || empty( $this->options['recaptcha_secret_key'] ) ) {
			return true;
		}

		$captcha_code = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : null;
		if ( empty( $captcha_code ) ) {
			return false;
		}

		$_response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body' => array(
					'secret'   => esc_attr( $this->options['recaptcha_secret_key'] ),
					'response' => $captcha_code,
					'remoteip' => $this->get_remote_ip_addr(),
				)
			)
		);
		$body      = json_decode( wp_remote_retrieve_body( $_response ), true );

		if ( isset( $body['success'] ) && ! $body['success'] ) {
			return false;
		}

		return false;
	}

	/**
	 * Get user IP address
	 *
	 * @return string
	 */
	private function get_remote_ip_addr() {
		if ( isset( $_SERVER['REMOTE_ADDR'] ) && WP_Http::is_ip_address( $_SERVER['REMOTE_ADDR'] ) ) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return '';
	}

	/**
	 * Send Mail to User
	 *
	 * @param $fields
	 * @param $mail
	 * @param array $attachments
	 *
	 * @return bool
	 * @internal param $headers
	 */
	private function send_mail( $fields, $mail, $attachments = array() ) {
		$placeholder = array();
		$all_fields  = array();
		foreach ( $fields as $field ) {
			if ( 'file' == $field['field_type'] ) {
				continue;
			}
			$value = isset( $_POST[ $field['field_name'] ] ) ? $_POST[ $field['field_name'] ] : '';
			$value = self::sanitize_value( $value, $field['field_type'] );

			$all_fields[ $field['field_name'] ] = array(
				'label' => $field['field_title'],
				'value' => $value,
			);

			$placeholder[ "[" . $field['field_name'] . "]" ] = $value;
		}

		$subject = $mail['subject'];
		$subject = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $subject );

		$body = $mail['body'];
		if ( false !== strpos( $body, '[all_fields_table]' ) ) {
			ob_start();
			include_once DIALOG_CONTACT_FORM_TEMPLATES . '/emails/email-notification.php';
			$message = ob_get_clean();
		} else {
			$body    = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $body );
			$body    = str_replace( array( "\r\n", "\r", "\n" ), "<br>", $body );
			$message = stripslashes( wp_kses_post( $body ) );
		}

		$receiver = $mail['receiver'];
		$receiver = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $receiver );
		$receiver = ( false !== strpos( $receiver, ',' ) ) ? explode( ',', $receiver ) : $receiver;

		$senderEmail = $mail['senderEmail'];
		$senderEmail = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $senderEmail );

		$senderName = esc_attr( $mail['senderName'] );
		$senderName = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $senderName );

		$mailer = new Mailer();
		$mailer->setReceiver( $receiver );
		$mailer->setSubject( $subject );
		$mailer->setMessage( $message );
		$mailer->setFrom( $senderEmail, $senderName );
		$mailer->setReplyTo( $senderEmail, $senderName );
		$mailer->setContentType( 'html' );
		$mailer->setAttachments( $attachments );

		return $mailer->send();
	}

	/**
	 * Upload attachments
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	private function upload_attachments( $fields ) {
		$attachments = array();
		$field_names = array_column( $fields, 'field_name' );
		$field_types = array_column( $fields, 'field_type' );

		// Check if global $_FILES exists
		if ( ! isset( $_FILES ) ) {
			return $attachments;
		}

		// Check if current form has any file field
		if ( ! in_array( 'file', $field_types ) ) {
			return $attachments;
		}

		$upload_dir = wp_upload_dir();

		$attachment_dir = join( DIRECTORY_SEPARATOR, array(
			$upload_dir['basedir'],
			DIALOG_CONTACT_FORM_UPLOAD_DIR
		) );

		// Make attachment directory in upload directory if not already exists
		if ( ! file_exists( $attachment_dir ) ) {
			wp_mkdir_p( $attachment_dir );
		}

		foreach ( $_FILES as $input_name => $file ) {

			// Check if file field exists in our field list
			if ( ! in_array( $input_name, $field_names ) ) {
				continue;
			}

			$name     = $_FILES[ $input_name ]['name'];
			$tmp_name = $_FILES[ $input_name ]['tmp_name'];
			$size     = $_FILES[ $input_name ]['size'];

			// $type     = $_FILES[ $input_name ]['type'];

			if ( empty( $tmp_name ) || empty( $name ) ) {
				continue;
			}

			// Generate unique file name
			$filename = wp_unique_filename( $attachment_dir, $name );

			// Get file mime type for uploaded file
			$finfo     = new \finfo( FILEINFO_MIME_TYPE );
			$file_type = $finfo->file( $tmp_name );

			// Get file extension from uploaded file name
			$temp_ext           = explode( '.', $name );
			$original_extension = strtolower( end( $temp_ext ) );

			// Get file extension from allowed mime types
			$ext = array_search( $file_type, get_allowed_mime_types(), true );

			// Check if uploaded file mime type is allowed
			if ( false === strpos( $ext, $original_extension ) ) {
				continue;
			}

			// check file size here.
			$max_upload_size = wp_max_upload_size();
			if ( $size > $max_upload_size ) {
				continue;
			}

			// Generate new file path
			$new_file = $attachment_dir . DIRECTORY_SEPARATOR . $filename;

			// Upload file
			$response = @move_uploaded_file( $tmp_name, $new_file );

			if ( $response ) {
				// Set correct file permissions.
				$stat  = stat( dirname( $new_file ) );
				$perms = $stat['mode'] & 0000666;
				@ chmod( $new_file, $perms );

				// Save uploaded file path for later use
				$attachments[] = $new_file;
			}
		}

		return $attachments;
	}

	/**
	 * Remove attachment after mail sent
	 *
	 * @param array $form_options
	 * @param array $attachments
	 */
	public function remove_attachment_file( $form_options, $attachments ) {
		foreach ( $attachments as $attachment ) {
			if ( file_exists( $attachment ) ) {
				unlink( $attachment );
			}
		}
	}

	/**
	 * Check if current submitted form is spam
	 *
	 * @param int $form_id
	 *
	 * @return bool
	 */
	private function is_spam( $form_id = 0 ) {
		$nonce_field_name  = '_dcf_nonce';
		$nonce_action_name = '_dcf_submit_form';

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$nonce_field_name  = 'nonce';
			$nonce_action_name = 'dialog_contact_form_ajax';
		}

		// Check if nonce field set
		if ( ! isset( $_POST[ $nonce_field_name ] ) ) {
			return true;
		}

		// Check if nonce value is valid
		if ( ! wp_verify_nonce( $_POST[ $nonce_field_name ], $nonce_action_name ) ) {
			return true;
		}

		// Form ID is valid
		$form = get_post( $form_id );
		if ( ! $form instanceof \WP_Post ) {
			return true;
		}

		// Check if form post type and register post type is same
		if ( DIALOG_CONTACT_FORM_POST_TYPE !== $form->post_type ) {
			return true;
		}

		// Check form has fields
		$fields = get_post_meta( $form_id, '_contact_form_fields', true );
		if ( ! ( is_array( $fields ) && count( $fields ) > 0 ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Sanitize user input
	 *
	 * @param mixed $input
	 * @param string $input_type
	 *
	 * @return array|string
	 */
	private static function sanitize_value( $input, $input_type = 'text' ) {
		// Initialize the new array that will hold the sanitize values
		$new_input = array();
		if ( is_array( $input ) ) {
			// Loop through the input and sanitize each of the values
			foreach ( $input as $key => $value ) {
				if ( is_array( $value ) ) {
					$new_input[ $key ] = self::sanitize_value( $value, $input_type );
				} else {
					$new_input[ $key ] = self::sanitize_string( $value, $input_type );
				}
			}

			// Join array elements with a new line string
			$new_input = implode( PHP_EOL, $new_input );

			return $new_input;
		}

		return self::sanitize_string( $input, $input_type );
	}

	/**
	 * Sanitize string
	 *
	 * @param string $string
	 * @param string $input_type
	 *
	 * @return string
	 */
	private static function sanitize_string( $string, $input_type = 'text' ) {
		if ( is_array( $string ) || is_object( $string ) ) {
			return $string;
		}

		$class_name = '\\DialogContactForm\\Fields\\' . ucfirst( $input_type );
		if ( method_exists( $class_name, 'sanitize' ) ) {
			$class = new $class_name;

			return $class->sanitize( $string );
		}

		return sanitize_text_field( $string );
	}
}
