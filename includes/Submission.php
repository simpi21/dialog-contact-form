<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Action;
use DialogContactForm\Collections\Actions;
use DialogContactForm\Collections\Fields;
use DialogContactForm\Fields\Recaptcha2;
use DialogContactForm\Supports\Attachment;
use DialogContactForm\Supports\Config;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Submission {

	/**
	 * The instance of the class
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * @return Submission
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			add_action( 'template_redirect', array( self::$instance, 'process_submission' ) );
			add_action( 'wp_ajax_dcf_submit_form', array( self::$instance, 'process_submission' ) );
			add_action( 'wp_ajax_nopriv_dcf_submit_form', array( self::$instance, 'process_submission' ) );
		}

		return self::$instance;
	}

	/**
	 * Process form submission
	 */
	public function process_submission() {

		// Early exist for non AJAX HTTP Request
		if ( ! ( $this->is_ajax() && isset( $_POST['_dcf_nonce'], $_POST['_dcf_id'] ) ) ) {
			return;
		}

		$form_id = isset( $_POST['_dcf_id'] ) ? intval( $_POST['_dcf_id'] ) : 0;
		$config  = Config::init( $form_id );

		// If it is spam, do nothing
		if ( ! $this->is_form_valid( $form_id ) ) {

			if ( $this->is_ajax() ) {
				wp_send_json( array(
					'status'  => 'fail',
					'message' => $config->getSpamMessage(),
				), 403 );
			}

			return;
		}

		if ( 'disable' !== Utils::get_option( 'nonce_validation' ) && ! $this->is_nonce_valid() ) {
			if ( $this->is_ajax() ) {
				wp_send_json( array(
					'status'  => 'fail',
					'message' => $config->getSpamMessage(),
				), 403 );
			}

			return;
		}

		/**
		 * @var \DialogContactForm\Supports\Config $config
		 */
		do_action( 'dialog_contact_form/before_validation', $config );

		$error_data = $this->validate_form_data( $config );

		/**
		 * @var \DialogContactForm\Supports\Config $config
		 * @var array $error_data
		 */
		do_action( 'dialog_contact_form/after_validation', $config, $error_data );

		// Exit if there is any error
		if ( count( $error_data ) > 0 ) {

			if ( $this->is_ajax() ) {
				wp_send_json( array(
					'status'     => 'fail',
					'message'    => $config->getValidationErrorMessage(),
					'validation' => $error_data,
				), 422 );
			}

			$GLOBALS['_dcf_errors']           = $error_data;
			$GLOBALS['_dcf_validation_error'] = $config->getValidationErrorMessage();

			return;
		}

		// Sanitize form data
		$data         = array();
		$fieldManager = Fields::init();
		foreach ( $config->getFormFields() as $field ) {
			if ( 'file' == $field['field_type'] ) {
				continue;
			}
			$value = isset( $_POST[ $field['field_name'] ] ) ? $_POST[ $field['field_name'] ] : '';

			$class_name = $fieldManager->get( $field['field_type'] );
			if ( class_exists( $class_name ) ) {
				/** @var \DialogContactForm\Abstracts\Field $class */
				$class = new $class_name;
				$class->setField( $field );

				$data[ $field['field_name'] ] = $class->sanitize( $value );
			} else {
				$data[ $field['field_name'] ] = self::sanitize( $value, $field['field_type'] );
			}
		}

		// If form upload a file, handle here
		if ( $config->hasFile() ) {
			$attachments = Attachment::upload( $config->getFormFields() );
			$data        = $data + $attachments;
		}

		$response = array();
		$actions  = Actions::init();

		foreach ( $actions as $action_id => $className ) {

			if ( ! in_array( $action_id, $config->getFormActions() ) ) {
				continue;
			}

			$action = new $className;
			if ( ! $action instanceof Action ) {
				continue;
			}

			$response[ $action->getId() ] = $action::process( $config, $data );
		}

		// If any action fails, display error message
		if ( false !== array_search( false, array_values( $response ), true ) ) {
			if ( $this->is_ajax() ) {
				wp_send_json( array(
					'status'  => 'fail',
					'message' => $config->getMailSendFailMessage(),
					'actions' => $response,
				), 500 );
			}

			$GLOBALS['_dcf_validation_error'] = $config->getMailSendFailMessage();

			return;
		}

		if ( $this->is_ajax() ) {
			// Display success message
			wp_send_json( array(
				'status'     => 'success',
				'reset_form' => $config->resetForm(),
				'actions'    => $response,
			), 200 );
		}

		// Process Success Message Action
		if ( isset( $response['success_message'] ) && $response['success_message'] ) {
			$GLOBALS['_dcf_mail_sent_ok'] = $response['success_message'];
		}

		// Reset form Data
		if ( $config->resetForm() ) {
			foreach ( array_keys( $data ) as $input_name ) {
				if ( isset( $_POST[ $input_name ] ) ) {
					unset( $_POST[ $input_name ] );
				}
			}
		}

		// Process Redirect Action
		if ( isset( $response['redirect'] ) && $response['redirect'] ) {
			wp_safe_redirect( $response['redirect'] );
			exit();
		}
	}

	/**
	 * Validate user submitted form data
	 *
	 * @param \DialogContactForm\Supports\Config $config
	 *
	 * @return mixed
	 */
	private function validate_form_data( $config ) {
		// Validate Form Data
		$errorData = array();
		foreach ( $config->getFormFields() as $field ) {
			$field_name = isset( $field['field_name'] ) ? $field['field_name'] : '';
			$value      = isset( $_POST[ $field_name ] ) ? $_POST[ $field_name ] : null;
			if ( 'file' == $field['field_type'] ) {
				$message = Attachment::validate( $field );
			} else {
				$message = $this->validate_post_field( $value, $field, $config );
			}

			if ( count( $message ) > 0 ) {
				$errorData[ $field_name ] = $message;
			}
		}

		// If Google reCAPTCHA enabled, verify it
		if ( $config->hasRecaptcha() && ! Recaptcha2::_validate() ) {
			$errorData['dcf_recaptcha'] = array( $config->getInvalidRecaptchaMessage() );
		}

		return $errorData;
	}

	/**
	 * Validate form field
	 *
	 * @param mixed $value
	 * @param array $field
	 * @param \DialogContactForm\Supports\Config $config
	 *
	 * @return array
	 */
	public function validate_post_field( $value, $field, $config ) {
		$messages      = $config->getValidationMessages();
		$message       = array();
		$field_type    = $field['field_type'] ? $field['field_type'] : '';
		$message_key   = sprintf( 'invalid_%s', $field_type );
		$error_message = isset( $messages[ $message_key ] ) ? $messages[ $message_key ] : $messages['generic_error'];

		$fieldManager = Fields::init();
		$class_name   = $fieldManager->get( $field_type );
		if ( ! class_exists( $class_name ) ) {
			return $message;
		}

		/** @var \DialogContactForm\Abstracts\Field $class */
		$class = new $class_name;
		$class->setField( $field );

		// If field is required, then check it is not empty
		if ( 'on' == $field['required_field'] && $class->isEmpty( $value ) ) {
			$message[] = $messages['invalid_required'];
		}

		// Check if value is acceptable for field type
		if ( ! $class->validate( $value ) ) {
			$message[] = $error_message;
		}

		// If field is not required, hide message if field is empty
		if ( 'off' == $field['required_field'] && $class->isEmpty( $value ) ) {
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
	 * Check if nonce is valid
	 *
	 * @return bool
	 */
	private function is_nonce_valid() {
		// Check if nonce field set
		if ( ! isset( $_POST['_dcf_nonce'] ) ) {
			return false;
		}

		// Check if nonce value is valid
		if ( ! wp_verify_nonce( $_POST['_dcf_nonce'], 'dialog_contact_form_nonce' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if current submitted form is spam
	 *
	 * @param int $form_id
	 *
	 * @return bool
	 */
	private function is_form_valid( $form_id = 0 ) {
		// Form ID is valid
		$form = get_post( $form_id );
		if ( ! $form instanceof \WP_Post ) {
			return false;
		}

		// Check if form post type and register post type is same
		if ( DIALOG_CONTACT_FORM_POST_TYPE !== $form->post_type ) {
			return false;
		}

		// Check form has fields
		$fields = (array) get_post_meta( $form_id, '_contact_form_fields', true );
		if ( count( $fields ) < 1 ) {
			return false;
		}

		return true;
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
					$new_input[ $key ] = sanitize_text_field( $value );
				}
			}

			return $new_input;
		}

		return sanitize_text_field( $input );
	}

	/**
	 * Check current request is AJAX
	 *
	 * @return bool
	 */
	private function is_ajax() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}
}
