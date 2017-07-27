<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'DialogContactFormProcessRequest' ) ):

	class DialogContactFormProcessRequest {

		protected static $instance = null;
		protected $validate;

		/**
		 * @return null|DialogContactFormProcessRequest
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
			$this->validate = new DialogContactFormValidator();

			add_action( 'wp_ajax_dcf_submit_form', array( $this, 'submit_form' ) );
			add_action( 'wp_ajax_nopriv_dcf_submit_form', array( $this, 'submit_form' ) );
			add_action( 'wp', array( $this, 'process_submited_form' ) );
		}

		/**
		 * Process non AJAX form submit
		 */
		public function process_submited_form() {
			if ( ! isset( $_POST['_dcf_nonce'] ) ) {
				return;
			}

			if ( ! isset( $_POST['_user_form_id'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['_dcf_nonce'], '_dcf_submit_form' ) ) {
				return;
			}

			$form_id  = intval( $_POST['_user_form_id'] );
			$mail     = get_post_meta( $form_id, '_contact_form_mail', true );
			$messages = get_post_meta( $form_id, '_contact_form_messages', true );
			$fields   = get_post_meta( $form_id, '_contact_form_fields', true );

			$field_name = array_column( $fields, 'field_name' );

			do_action( 'dcf_before_validation', $form_id, $mail, $fields, $messages );

			if ( ! $this->validate->is_array( $fields ) ) {
				return;
			}

			// Validate Form Data
			$errorData = array();
			foreach ( $_POST as $field => $value ) {
				if ( in_array( $field, $field_name ) ) {

					$indexNumber = array_search( $field, $field_name );
					$_field      = $fields[ $indexNumber ];

					$message = $this->validate_field( $value, $_field, $messages );

					if ( count( $message ) > 0 ) {
						$errorData[ $field ] = $message;
					}
				}
			}

			do_action( 'dcf_after_validation', $errorData );

			// Exit if there is any error
			if ( count( $errorData ) > 0 ) {
				DialogContactFormSession::flash( '_dcf_errors', $errorData );
				DialogContactFormSession::flash( '_dcf_validation_error', $messages['validation_error'] );

				return;
			}

			do_action( 'dcf_before_send_mail' );

			$mail_sent = $this->send_mail( $field_name, $mail );

			do_action( 'dcf_after_send_mail', $mail_sent );

			if ( $mail_sent ) {
				DialogContactFormSession::flash( '_dcf_mail_sent_ok', $errorData );

				return;
			}
		}

		/**
		 * Process AJAX form Submit
		 */
		public function submit_form() {
			$default_options = dcf_default_options();
			$options         = get_option( 'dialog_contact_form' );
			$options         = wp_parse_args( $options, $default_options );
			if ( ! isset( $_POST['nonce'] ) ) {
				$response = array(
					'status'  => 'fail',
					'message' => esc_attr( $options['spam_message'] ),
				);
				wp_send_json( $response, 403 );
			}

			if ( ! wp_verify_nonce( $_POST['nonce'], 'dialog_contact_form_ajax' ) ) {
				$response = array(
					'status'  => 'fail',
					'message' => esc_attr( $options['spam_message'] ),
				);
				wp_send_json( $response, 403 );
			}

			if ( ! isset( $_POST['_user_form_id'] ) ) {
				$response = array(
					'status'  => 'fail',
					'message' => esc_attr( $options['spam_message'] ),
				);
				wp_send_json( $response, 403 );
			}

			$form_id  = intval( $_POST['_user_form_id'] );
			$mail     = get_post_meta( $form_id, '_contact_form_mail', true );
			$fields   = get_post_meta( $form_id, '_contact_form_fields', true );
			$messages = get_post_meta( $form_id, '_contact_form_messages', true );
			$config   = get_post_meta( $form_id, '_contact_form_config', true );

			$default_options = dcf_default_options();
			$options         = get_option( 'dialog_contact_form' );
			$_options        = wp_parse_args( $options, $default_options );

			$field_name = array_column( $fields, 'field_name' );

			do_action( 'dcf_before_ajax_validation', $form_id, $mail, $fields, $messages );

			if ( ! $this->validate->is_array( $fields ) ) {
				return;
			}

			// Validate Form Data
			$errorData = array();

			// If Google reCAPTCHA enabled, verify it
			if ( ! $this->validate_google_recaptcha( $config, $_options ) ) {
				$errorData[] = array(
					'field'   => 'dcf_recaptcha',
					'message' => array( $messages['invalid_recaptcha'] ),
				);
			}

			// Loop through form fields and validate its data
			foreach ( $_POST as $field => $value ) {
				if ( in_array( $field, $field_name ) ) {

					$indexNumber = array_search( $field, $field_name );
					$_field      = $fields[ $indexNumber ];

					$message = $this->validate_field( $value, $_field, $messages );

					if ( count( $message ) > 0 ) {
						$errorData[] = array(
							'field'   => $field,
							'message' => $message,
						);
					}
				}
			}

			do_action( 'dcf_after_ajax_validation', $errorData );

			if ( $errorData ) {
				$response = array(
					'status'     => 'fail',
					'message'    => esc_attr( $messages['validation_error'] ),
					'validation' => $errorData,
				);
				wp_send_json( $response, 422 );
			}

			do_action( 'dcf_before_ajax_send_mail' );

			$mail_sent = $this->send_mail( $field_name, $mail );

			do_action( 'dcf_after_ajax_send_mail', $mail_sent );

			if ( $mail_sent ) {

				$response = array(
					'status'  => 'success',
					'message' => esc_attr( $messages['mail_sent_ok'] ),
				);

				wp_send_json( $response, 200 );
			} else {

				$response = array(
					'status'  => 'fail',
					'message' => esc_attr( $messages['mail_sent_ng'] ),
				);
				wp_send_json( $response, 500 );
			}
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

			// If field type is email, url or number
			// Add appropriate validation rule
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
						if ( ! $this->validate->required( $value ) ) {
							$message[] = $messages['invalid_required'];
						}
						break;
					case 'email':
						if ( ! $this->validate->email( $value ) ) {
							$message[] = $messages['invalid_email'];
						}
						break;
					case 'url':
						if ( ! $this->validate->url( $value ) ) {
							$message[] = $messages['invalid_url'];
						}
						break;
					case 'number':
						if ( ! $this->validate->number( $value ) ) {
							$message[] = $messages['invalid_number'];
						}
						break;
					case 'int':
						if ( ! $this->validate->int( $value ) ) {
							$message[] = $messages['invalid_int'];
						}
						break;
					case 'alpha':
						if ( ! $this->validate->alpha( $value ) ) {
							$message[] = $messages['invalid_alpha'];
						}
						break;
					case 'alnum':
						if ( ! $this->validate->alnum( $value ) ) {
							$message[] = $messages['invalid_alnum'];
						}
						break;
					case 'alnumdash':
						if ( ! $this->validate->alnumdash( $value ) ) {
							$message[] = $messages['invalid_alnumdash'];
						}
						break;
					case 'date':
						if ( ! $this->validate->date( $value ) ) {
							$message[] = $messages['invalid_date'];
						}
						break;
					case 'checked':
						if ( ! $this->validate->checked( $value ) ) {
							$message[] = $messages['invalid_checked'];
						}
						break;
					case 'ip':
						if ( ! $this->validate->ip( $value ) ) {
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

			// If field is not required
			// Hide message if field is empty
			if ( ! in_array( 'required', $validate_rules ) &&
			     ! $this->validate->required( $value ) ) {
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
		 * Send Mail to User
		 *
		 * @param $field_name
		 * @param $mail
		 *
		 * @internal param $headers
		 * @return bool
		 */
		private function send_mail( $field_name, $mail ) {
			$placeholder = array();
			foreach ( $field_name as $key => $_name ) {
				$placeholder[ "%" . $_name . "%" ] = $_POST[ $_name ];
			}

			$subject = $mail['subject'];
			$subject = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $subject );
			$subject = esc_attr( $subject );

			$body    = $mail['body'];
			$body    = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $body );
			$body    = str_replace( array( "\r\n", "\r", "\n" ), "<br>", $body );
			$message = wp_kses( $body, wp_kses_allowed_html( 'post' ) );

			$receiver = $mail['receiver'];
			$receiver = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $receiver );

			$senderEmail = sanitize_email( $mail['senderEmail'] );
			$senderEmail = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $senderEmail );

			$senderName = esc_attr( $mail['senderName'] );
			$senderName = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $senderName );

			$headers   = array();
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$headers[] = "From: $senderName <$senderEmail>";

			return wp_mail( $receiver, $subject, $message, $headers );
		}

		/**
		 * Verify Google reCAPTCHA code
		 *
		 * @param $config
		 * @param $options
		 *
		 * @return bool
		 */
		private function validate_google_recaptcha( $config, $options ) {
			if ( isset( $config['recaptcha'] ) && $config['recaptcha'] == 'yes' ) {
				if ( ! empty( $options['recaptcha_site_key'] ) &&
				     ! empty( $options['recaptcha_secret_key'] ) ) {

					$recaptcha = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : null;
					$_response = wp_remote_post(
						'https://www.google.com/recaptcha/api/siteverify',
						[
							'body' => [
								'secret'   => esc_attr( $options['recaptcha_secret_key'] ),
								'response' => $recaptcha,
								'remoteip' => $this->get_remote_ip_addr(),
							]
						]
					);
					$body      = json_decode( wp_remote_retrieve_body( $_response ), true );

					if ( isset( $body['success'] ) && ! $body['success'] ) {
						return false;

					}

				}
			}

			return true;
		}

		/**
		 * Get user IP address
		 *
		 * @return string
		 */
		private function get_remote_ip_addr() {
			if ( isset( $_SERVER['REMOTE_ADDR'] )
			     && WP_Http::is_ip_address( $_SERVER['REMOTE_ADDR'] ) ) {
				return $_SERVER['REMOTE_ADDR'];
			}

			return '';
		}
	}

endif;

DialogContactFormProcessRequest::init();
