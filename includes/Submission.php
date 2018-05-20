<?php

namespace DialogContactForm;

use DialogContactForm\Actions\EmailNotification;
use DialogContactForm\Fields\Recaptcha;
use DialogContactForm\Supports\Attachment;
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

		// Sanitize form data
		$data = array();
		foreach ( $fields as $field ) {
			if ( 'file' == $field['field_type'] ) {
				continue;
			}
			$value = isset( $_POST[ $field['field_name'] ] ) ? $_POST[ $field['field_name'] ] : '';

			$data[ $field['field_name'] ] = self::sanitize( $value, $field['field_type'] );
		}

		// If form upload a file, handle here
		$data['dcf_attachments'] = Attachment::upload( $fields );

		$response = array();
		$actions  = ActionManager::init();
		/** @var \DialogContactForm\Abstracts\Abstract_Action $action */
		foreach ( $actions as $action ) {
			$response[ $action->get_id() ] = $action::process( $form_id, $data );
		}

		// If any action fails, display error message
		if ( false !== array_search( false, array_values( $response ), true ) ) {
			$GLOBALS['_dcf_validation_error'] = $messages['mail_sent_ng'];
		} else {
			$GLOBALS['_dcf_mail_sent_ok'] = $messages['mail_sent_ok'];

			// @TODO reset form data
			if ( 'no' !== $config['reset_form'] ) {

			}
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

		// Sanitize form data
		$data = array();
		foreach ( $fields as $field ) {
			if ( 'file' == $field['field_type'] ) {
				continue;
			}
			$value = isset( $_POST[ $field['field_name'] ] ) ? $_POST[ $field['field_name'] ] : '';

			$data[ $field['field_name'] ] = self::sanitize( $value, $field['field_type'] );
		}

		// If form upload a file, handle here
		$data['dcf_attachments'] = Attachment::upload( $fields );

		$response = array();
		$actions  = ActionManager::init();
		/** @var \DialogContactForm\Abstracts\Abstract_Action $action */
		foreach ( $actions as $action ) {
			$response[ $action->get_id() ] = $action::process( $form_id, $data );
		}

		// If any action fails, display error message
		if ( false !== array_search( false, array_values( $response ), true ) ) {
			wp_send_json( array(
				'status'  => 'fail',
				'message' => esc_attr( $messages['mail_sent_ng'] ),
				'actions' => $response,
			), 500 );
		}

		// Display success message
		$reset_form = get_post_meta( $form_id, '_contact_form_config', true );
		$_response  = array(
			'status'     => 'success',
			'reset_form' => ( 'no' !== $reset_form['reset_form'] ),
			'actions'    => $response,
		);
		wp_send_json( $_response, 200 );
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
				$message = Attachment::validate( $field );
			} else {
				$message = $this->validate_post_field( $value, $field );
			}

			if ( count( $message ) > 0 ) {
				$errorData[ $field_name ] = $message;
			}
		}

		// If Google reCAPTCHA enabled, verify it
		$messages = $this->get_validation_messages();
		if ( isset( $config['recaptcha'] ) && $config['recaptcha'] === 'yes' ) {
			if ( ! Recaptcha::_validate() ) {
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
		$field_type     = $field['field_type'] ? $field['field_type'] : '';
		$message_key    = sprintf( 'invalid_%s', $field_type );
		$error_message  = isset( $messages[ $message_key ] ) ? $messages[ $message_key ] : $messages['generic_error'];

		// Backward compatibility for required field.
		if ( ! isset( $field['required_field'] ) ) {
			if ( in_array( 'required', $validate_rules ) ) {
				$field['required_field'] = 'on';
			} else {
				$field['required_field'] = 'off';
			}
		}

		$class_name = '\\DialogContactForm\\Fields\\' . ucfirst( $field_type );
		if ( class_exists( $class_name ) ) {
			/** @var \DialogContactForm\Abstracts\Abstract_Field $class */
			$class = new $class_name;
			$class->setField( $field );

			// If field is required, then check it is not empty
			if ( 'on' == $field['required_field'] && $class->is_empty( $value ) ) {
				$message[] = $messages['invalid_required'];
			}

			// Check if value is acceptable for field type
			if ( ! $class->validate( $value ) ) {
				$message[] = $error_message;
			}

			// If field is not required, hide message if field is empty
			if ( 'off' == $field['required_field'] && $class->is_empty( $value ) ) {
				$message = array();
			}

			// If user custom message exists, use it
			if ( count( $message ) > 0 && mb_strlen( $field['error_message'], 'UTF-8' ) > 10 ) {
				$message = array( $field['error_message'] );
			}
		}

		// Return field validation messages
		return $message;
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

		if ( dialog_contact_form()->is_request( 'ajax' ) ) {
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
	private static function sanitize( $input, $input_type = 'text' ) {
		// Initialize the new array that will hold the sanitize values
		$new_input = array();
		if ( is_array( $input ) ) {
			// Loop through the input and sanitize each of the values
			foreach ( $input as $key => $value ) {
				if ( is_array( $value ) ) {
					$new_input[ $key ] = self::sanitize( $value, $input_type );
				} else {
					$new_input[ $key ] = self::sanitize_string( $value, $input_type );
				}
			}

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
			/** @var \DialogContactForm\Abstracts\Abstract_Field $class */
			$class = new $class_name;

			return $class->sanitize( $string );
		}

		return sanitize_text_field( $string );
	}
}
