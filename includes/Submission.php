<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Action;
use DialogContactForm\Collections\Actions;
use DialogContactForm\Collections\Fields;
use DialogContactForm\Fields\Recaptcha2;
use DialogContactForm\Supports\Attachment;
use DialogContactForm\Supports\Config;
use DialogContactForm\Supports\UploadedFile;
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
		$form    = Config::init( $form_id );

		// If it is spam, do nothing
		if ( ! $form->isValid() ) {

			if ( $this->is_ajax() ) {
				wp_send_json( array(
					'status'  => 'fail',
					'message' => $form->getSpamMessage(),
				), 403 );
			}

			return;
		}

		if ( 'disable' !== Utils::get_option( 'nonce_validation' ) && ! $this->is_nonce_valid() ) {
			if ( $this->is_ajax() ) {
				wp_send_json( array(
					'status'  => 'fail',
					'message' => $form->getSpamMessage(),
				), 403 );
			}

			return;
		}

		$fields = $form->getFormFields();

		/**
		 * @var \DialogContactForm\Supports\Config $form
		 */
		do_action( 'dialog_contact_form/before_validation', $form );

		$error_data = array();
		$files      = array();

		if ( $form->hasFile() ) {
			$files = UploadedFile::getUploadedFiles();
		}

		foreach ( $fields as $field ) {
			$field_name = isset( $field['field_name'] ) ? $field['field_name'] : '';
			if ( 'file' == $field['field_type'] ) {
				$file    = isset( $files[ $field_name ] ) ? $files[ $field_name ] : false;
				$message = Attachment::validate( $file, $field, $form );
			} else {
				$value   = isset( $_POST[ $field_name ] ) ? $_POST[ $field_name ] : null;
				$message = $this->validate( $value, $field, $form );
			}

			if ( count( $message ) > 0 ) {
				$error_data[ $field_name ] = $message;
			}
		}

		// If Google reCAPTCHA enabled, verify it
		if ( $form->hasRecaptcha() && ! Recaptcha2::_validate() ) {
			$error_data['dcf_recaptcha'] = array( $form->getInvalidRecaptchaMessage() );
		}

		/**
		 * @var \DialogContactForm\Supports\Config $form
		 * @var array $error_data
		 */
		do_action( 'dialog_contact_form/after_validation', $form, $error_data );

		// Exit if there is any error
		if ( count( $error_data ) > 0 ) {

			if ( $this->is_ajax() ) {
				wp_send_json( array(
					'status'     => 'fail',
					'message'    => $form->getValidationErrorMessage(),
					'validation' => $error_data,
				), 422 );
			}

			$GLOBALS['_dcf_errors']           = $error_data;
			$GLOBALS['_dcf_validation_error'] = $form->getValidationErrorMessage();

			return;
		}

		// Sanitize form data
		$data         = array();
		$fieldManager = Fields::init();
		foreach ( $fields as $field ) {
			if ( in_array( $field['field_type'], array( 'file', 'html', 'divider' ) ) ) {
				continue;
			}

			$class_name = $fieldManager->get( $field['field_type'] );
			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			/** @var \DialogContactForm\Abstracts\Field $class */
			$class = new $class_name;
			$class->setField( $field );

			$value = isset( $_POST[ $field['field_name'] ] ) ? $_POST[ $field['field_name'] ] : '';

			$data[ $field['field_name'] ] = $class->sanitize( $value );
		}

		// If form upload a file, handle here
		if ( $form->hasFile() ) {
			$attachments = Attachment::upload( $files, $fields );
			$data        = $data + $attachments;
		}

		$response = array();
		$actions  = Actions::init();

		foreach ( $actions as $action_id => $className ) {

			if ( ! in_array( $action_id, $form->getFormActions() ) ) {
				continue;
			}

			$action = new $className;
			if ( ! $action instanceof Action ) {
				continue;
			}

			$response[ $action->getId() ] = $action::process( $form, $data );
		}

		// If any action fails, display error message
		if ( false !== array_search( false, array_values( $response ), true ) ) {
			if ( $this->is_ajax() ) {
				wp_send_json( array(
					'status'  => 'fail',
					'message' => $form->getMailSendFailMessage(),
					'actions' => $response,
				), 500 );
			}

			$GLOBALS['_dcf_validation_error'] = $form->getMailSendFailMessage();

			return;
		}

		if ( $this->is_ajax() ) {
			// Display success message
			wp_send_json( array(
				'status'     => 'success',
				'reset_form' => $form->resetForm(),
				'actions'    => $response,
			), 200 );
		}

		// Process Success Message Action
		if ( isset( $response['success_message'] ) && $response['success_message'] ) {
			$GLOBALS['_dcf_mail_sent_ok'] = $response['success_message'];
		}

		// Reset form Data
		if ( $form->resetForm() ) {
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
	 * Validate form field
	 *
	 * @param mixed $value
	 * @param array $field
	 * @param \DialogContactForm\Supports\Config $config
	 *
	 * @return array
	 */
	private function validate( $value, $field, $config ) {
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

		if ( ! $class->isFillable() ) {
			return $message;
		}

		// If field is required, then check it is not empty
		if ( $class->isRequired() && $class->isEmpty( $value ) ) {
			$message[] = $messages['invalid_required'];
		}

		// Check if value is acceptable for field type
		if ( ! $class->validate( $value ) ) {
			$message[] = $error_message;
		}

		// If field is not required, hide message if field is empty
		if ( ! $class->isRequired() && $class->isEmpty( $value ) ) {
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
	 * Check current request is AJAX
	 *
	 * @return bool
	 */
	private function is_ajax() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}
}
