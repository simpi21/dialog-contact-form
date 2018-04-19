<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Dialog_Contact_Form_Process_Request' ) ) {

	class Dialog_Contact_Form_Process_Request {

		/**
		 * @var object
		 */
		protected static $instance;

		/**
		 * @return null|Dialog_Contact_Form_Process_Request
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
			$config          = get_post_meta( $form_id, '_contact_form_config', true );
			$mail            = get_post_meta( $form_id, '_contact_form_mail', true );
			$fields          = get_post_meta( $form_id, '_contact_form_fields', true );
			$messages        = get_post_meta( $form_id, '_contact_form_messages', true );
			$field_names     = array_column( $fields, 'field_name' );

			// Validate form data
			do_action( 'dcf_before_validation', $form_id, $mail, $fields, $messages );
			$errorData = $this->validate_form_data( $fields, $messages, $config, $options );
			do_action( 'dcf_after_validation', $form_id, $mail, $fields, $messages, $errorData );

			// Exit if there is any error
			if ( count( $errorData ) > 0 ) {
				$GLOBALS['_dcf_errors']           = $errorData;
				$GLOBALS['_dcf_validation_error'] = $messages['validation_error'];

				return;
			}

			// If form upload a file, handle here
			$attachments = $this->upload_attachments( $fields );

			do_action( 'dcf_before_send_mail' );

			$mail_sent = $this->send_mail( $field_names, $mail, $attachments );

			do_action( 'dcf_after_send_mail', $mail_sent );

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

			$mail        = get_post_meta( $form_id, '_contact_form_mail', true );
			$fields      = get_post_meta( $form_id, '_contact_form_fields', true );
			$messages    = get_post_meta( $form_id, '_contact_form_messages', true );
			$config      = get_post_meta( $form_id, '_contact_form_config', true );
			$field_names = array_column( $fields, 'field_name' );

			// Validate form data
			do_action( 'dcf_before_ajax_validation', $form_id, $mail, $fields, $messages );
			$errorData = $this->validate_form_data( $fields, $messages, $config, $options );
			do_action( 'dcf_after_ajax_validation', $form_id, $mail, $fields, $messages, $errorData );

			// If there is a error, send error response
			if ( $errorData ) {
				wp_send_json( array(
					'status'     => 'fail',
					'message'    => esc_attr( $messages['validation_error'] ),
					'validation' => $errorData,
				), 422 );
			}

			// If form upload a file, handle here
			$attachments = $this->upload_attachments( $fields );

			do_action( 'dcf_before_ajax_send_mail' );

			// Send mail to user
			$mail_sent = $this->send_mail( $field_names, $mail, $attachments );

			do_action( 'dcf_after_ajax_send_mail', $mail_sent );

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
			$field_names = array_column( $fields, 'field_name' );
			// Validate Form Data
			$errorData = array();

			foreach ( $_POST as $field => $value ) {
				// If submitted field is not in form field list, then ignore it
				if ( ! in_array( $field, $field_names ) ) {
					continue;
				}

				$indexNumber = array_search( $field, $field_names );
				$_field      = $fields[ $indexNumber ];

				$message = $this->validate_field( $value, $_field, $messages );

				if ( count( $message ) > 0 ) {
					$errorData[ $field ] = $message;
				}
			}

			// If Google reCAPTCHA enabled, verify it
			if ( isset( $config['recaptcha'] ) && $config['recaptcha'] == 'yes' ) {
				if ( ! $this->validate_google_recaptcha( $options ) ) {
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
		 * @param array $messages
		 *
		 * @return array
		 */
		public function validate_field( $value, $field, $messages ) {
			$defaults = dcf_validation_messages();
			$messages = wp_parse_args( $messages, $defaults );

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
						if ( ! Dialog_Contact_Form_Validator::required( $value ) ) {
							$message[] = $messages['invalid_required'];
						}
						break;
					case 'email':
						if ( ! Dialog_Contact_Form_Validator::email( $value ) ) {
							$message[] = $messages['invalid_email'];
						}
						break;
					case 'url':
						if ( ! Dialog_Contact_Form_Validator::url( $value ) ) {
							$message[] = $messages['invalid_url'];
						}
						break;
					case 'number':
						if ( ! Dialog_Contact_Form_Validator::number( $value ) ) {
							$message[] = $messages['invalid_number'];
						}
						break;
					case 'int':
						if ( ! Dialog_Contact_Form_Validator::int( $value ) ) {
							$message[] = $messages['invalid_int'];
						}
						break;
					case 'alpha':
						if ( ! Dialog_Contact_Form_Validator::alpha( $value ) ) {
							$message[] = $messages['invalid_alpha'];
						}
						break;
					case 'alnum':
						if ( ! Dialog_Contact_Form_Validator::alnum( $value ) ) {
							$message[] = $messages['invalid_alnum'];
						}
						break;
					case 'alnumdash':
						if ( ! Dialog_Contact_Form_Validator::alnumdash( $value ) ) {
							$message[] = $messages['invalid_alnumdash'];
						}
						break;
					case 'date':
						if ( ! Dialog_Contact_Form_Validator::date( $value ) ) {
							$message[] = $messages['invalid_date'];
						}
						break;
					case 'checked':
						if ( ! Dialog_Contact_Form_Validator::checked( $value ) ) {
							$message[] = $messages['invalid_checked'];
						}
						break;
					case 'ip':
						if ( ! Dialog_Contact_Form_Validator::ip( $value ) ) {
							$message[] = $messages['invalid_ip'];
						}
						break;
					case 'user_login':
						if ( ! username_exists( $value ) && ! email_exists( $value ) ) {
							$message[] = $messages['invalid_user_login'];
						}
						break;
					case 'username':
						if ( ! username_exists( $value ) ) {
							$message[] = $messages['invalid_username'];
						}
						break;
					case 'user_email':
						if ( ! email_exists( $value ) ) {
							$message[] = $messages['invalid_user_email'];
						}
						break;
					default:
						break;
				}
			}

			// If field is not required, hide message if field is empty
			if ( ! in_array( 'required', $validate_rules ) &&
			     ! Dialog_Contact_Form_Validator::required( $value ) ) {
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
		 * Verify Google reCAPTCHA code
		 *
		 * @param array $options
		 *
		 * @return bool
		 */
		private function validate_google_recaptcha( $options ) {
			// If reCAPTCHA key or secret is empty, return true
			if ( empty( $options['recaptcha_site_key'] ) || empty( $options['recaptcha_secret_key'] ) ) {
				return true;
			}

			$captcha_code = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : null;
			if ( empty( $captcha_code ) ) {
				return false;
			}

			$_response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify',
				array(
					'body' => array(
						'secret'   => esc_attr( $options['recaptcha_secret_key'] ),
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
		 * @param $field_names
		 * @param $mail
		 * @param array $attachments
		 *
		 * @return bool
		 * @internal param $headers
		 */
		private function send_mail( $field_names, $mail, $attachments = array() ) {
			$placeholder = array();
			foreach ( $field_names as $_name ) {
				$placeholder[ "[" . $_name . "]" ] = isset( $_POST[ $_name ] ) ? $_POST[ $_name ] : '';
			}

			$subject = $mail['subject'];
			$subject = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $subject );
			$subject = sanitize_text_field( $subject );

			$body    = $mail['body'];
			$body    = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $body );
			$body    = str_replace( array( "\r\n", "\r", "\n" ), "<br>", $body );
			$message = stripslashes( wp_kses_post( $body ) );

			$receiver = $mail['receiver'];
			$receiver = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $receiver );
			if ( false !== strpos( $receiver, ',' ) ) {
				$receivers = explode( ',', $receiver );
				$receiver  = array_map( 'sanitize_email', $receivers );
			} else {
				$receiver = sanitize_email( $receiver );
			}

			$senderEmail = $mail['senderEmail'];
			$senderEmail = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $senderEmail );
			$senderEmail = sanitize_email( $senderEmail );

			$senderName = esc_attr( $mail['senderName'] );
			$senderName = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $senderName );
			$senderName = sanitize_text_field( $senderName );

			$headers   = array();
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$headers[] = "From: $senderName <$senderEmail>";
			$headers[] = "Reply-To: $senderName <$senderEmail>";

			return wp_mail( $receiver, $subject, $message, $headers, $attachments );
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

			if ( in_array( 'file', $field_types ) && isset( $_FILES ) ) {

				$upload_dir = wp_upload_dir();

				$attachment_dir = join( DIRECTORY_SEPARATOR, array(
					$upload_dir['basedir'],
					DIALOG_CONTACT_FORM_UPLOAD_DIR
				) );


				if ( ! file_exists( $attachment_dir ) ) {
					wp_mkdir_p( $attachment_dir );
				}


				foreach ( $_FILES as $input_name => $file ) {
					if ( in_array( $input_name, $field_names ) ) {
						$name     = $_FILES[ $input_name ]['name'];
						$tmp_name = $_FILES[ $input_name ]['tmp_name'];
						// $type     = $_FILES[ $input_name ]['type'];
						// $size     = $_FILES[ $input_name ]['size'];

						$safe_filename = sanitize_file_name( $name );
						// $file_ext      = strtolower( end( explode( '.', $safe_filename ) ) );

						$file_path = $attachment_dir . DIRECTORY_SEPARATOR . $safe_filename;
						$response  = move_uploaded_file( $tmp_name, $file_path );

						if ( $response ) {
							$attachments[] = $file_path;
						}
					}
				}
			}

			return $attachments;
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
	}
}

Dialog_Contact_Form_Process_Request::init();
