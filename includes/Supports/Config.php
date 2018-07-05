<?php

namespace DialogContactForm\Supports;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Config {

	/**
	 * The instance of the class
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Current form ID
	 *
	 * @var int
	 */
	private $form_id = 0;

	/**
	 * Global form options
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Form validation messages
	 *
	 * @var array
	 */
	private $validation_messages = array();

	/**
	 * Current form fields
	 *
	 * @var array
	 */
	private $form_fields = array();

	/**
	 * Meta information for current submission
	 *
	 * @var array
	 */
	private $meta_data = array();

	/**
	 * Current form settings
	 *
	 * @var array
	 */
	private $form_settings = array();

	/**
	 * List of supported actions
	 *
	 * @var array
	 */
	private $form_actions = array();

	/**
	 * List of field types for current form
	 *
	 * @var array
	 */
	private $field_types = array();

	/**
	 * Check current form has file field
	 *
	 * @var bool
	 */
	private $has_file = false;

	/**
	 * Check if reCAPTCHA is enabled
	 *
	 * @var bool
	 */
	private $has_recaptcha = false;

	/**
	 * MailChimp API key
	 *
	 * @var string
	 */
	private $mailchimp_api_key = '';

	/**
	 * If form should reset after successful submission
	 *
	 * @var bool
	 */
	private $reset_form = true;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @param int $form_id
	 *
	 * @return Config
	 */
	public static function init( $form_id = 0 ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $form_id );
		}

		return self::$instance;
	}

	/**
	 * Config constructor.
	 *
	 * @param int $form_id
	 */
	public function __construct( $form_id = 0 ) {
		if ( $form_id ) {
			$this->setFormId( intval( $form_id ) );
		}

		if ( ! $form_id ) {
			global $post;

			if ( $post instanceof \WP_Post && 'dialog-contact-form' === $post->post_type ) {
				$this->setFormId( $post->ID );
			}
		}


		if ( ! $this->options ) {
			$this->options = Utils::get_option();
		}

		if ( ! empty( $this->options['mailchimp_api_key'] ) ) {
			$this->mailchimp_api_key = $this->options['mailchimp_api_key'];
		}

		if ( ! $this->validation_messages ) {
			$default_messages = Utils::validation_messages();
			$messages         = array();
			foreach ( $default_messages as $key => $message ) {
				if ( ! empty( $this->options[ $key ] ) ) {
					$messages[ $key ] = $this->options[ $key ];
					unset( $this->options[ $key ] );
					continue;
				}
				$messages[ $key ] = $message;
			}

			$this->validation_messages = $messages;
		}

		if ( $this->form_id ) {
			$this->form_fields   = (array) get_post_meta( $this->form_id, '_contact_form_fields', true );
			$this->form_settings = (array) get_post_meta( $this->form_id, '_contact_form_config', true );

			$form_actions = (array) get_post_meta( $this->form_id, '_contact_form_actions', true );
			if ( empty( $form_actions ) ) {
				$form_actions = array( 'email_notification', 'success_message', 'redirect' );
			}
			$this->form_actions = $form_actions;

			$messages                  = (array) get_post_meta( $this->form_id, '_contact_form_messages', true );
			$this->validation_messages = wp_parse_args( $messages, $this->validation_messages );

			// Check if current form has file
			if ( $this->form_fields ) {
				$this->field_types = array_unique( Utils::array_column( $this->form_fields, 'field_type' ) );

				if ( in_array( 'file', $this->field_types ) ) {
					$this->has_file = true;
				}
			}

			// Check if reCAPTCHA is enabled
			if ( isset( $this->form_settings['recaptcha'] ) && 'yes' === $this->form_settings['recaptcha'] ) {
				$this->has_recaptcha = true;
			}

			// If form should reset after submission
			if ( isset( $this->form_settings['reset_form'] ) && 'no' === $this->form_settings['reset_form'] ) {
				$this->reset_form = false;
			}
		}
	}

	/**
	 * Get config data as array
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			'title'         => get_the_title( $this->getFormId() ),
			'id'            => $this->getFormId(),
			'fields'        => $this->getFormFields(),
			'settings'      => $this->form_settings,
			'actions'       => $this->getFormActions(),
			'messages'      => $this->getValidationMessages(),
			'has_file'      => $this->hasFile(),
			'has_recaptcha' => $this->hasRecaptcha(),
			'reset_form'    => $this->resetForm(),
		);
	}

	/**
	 * Get form ID
	 *
	 * @return bool
	 */
	public function getFormId() {
		return $this->form_id;
	}

	/**
	 * Set form ID
	 *
	 * @param mixed $form_id
	 */
	public function setFormId( $form_id ) {
		$this->form_id = $form_id;
	}

	/**
	 * Get form Options
	 *
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * Get current form supported actions
	 *
	 * @return array
	 */
	public function getFormActions() {
		return $this->form_actions;
	}

	/**
	 * Get validation messages
	 *
	 * @return array
	 */
	public function getValidationMessages() {
		return $this->validation_messages;
	}

	/**
	 * Check if reCAPTCHA is enabled
	 *
	 * @return bool
	 */
	public function hasRecaptcha() {
		return $this->has_recaptcha;
	}

	/**
	 * @return bool
	 */
	public function hasFile() {
		return $this->has_file;
	}

	/**
	 * If form should reset after successful submission
	 *
	 * @return bool
	 */
	public function resetForm() {
		return $this->reset_form;
	}

	/**
	 * @return array
	 */
	public function getFormFields() {
		return $this->form_fields;
	}

	/**
	 * Get spam message
	 *
	 * @return string
	 */
	public function getSpamMessage() {
		return esc_attr( $this->validation_messages['spam_message'] );
	}

	/**
	 * Get invalid reCAPTCHA message
	 *
	 * @return string
	 */
	public function getInvalidRecaptchaMessage() {
		return esc_attr( $this->validation_messages['invalid_recaptcha'] );
	}

	/**
	 * Get validation error message
	 *
	 * @return string
	 */
	public function getValidationErrorMessage() {
		return esc_attr( $this->validation_messages['validation_error'] );
	}

	/**
	 * Get mail send fail message
	 *
	 * @return string
	 */
	public function getMailSendFailMessage() {
		return esc_attr( $this->validation_messages['mail_sent_ng'] );
	}

	/**
	 * Get MailChimp API key
	 *
	 * @return string
	 */
	public function getMailChimpApiKey() {
		return $this->mailchimp_api_key;
	}

	/**
	 * Get form meta information
	 *
	 * @param null $current_time
	 *
	 * @return array
	 */
	public function getMetaData( $current_time = null ) {
		if ( ! $this->meta_data ) {
			if ( ! $current_time ) {
				$current_time = current_time( 'mysql' );
			}

			return array(
				'form_id'    => $this->getFormId(),
				'user_id'    => get_current_user_id(),
				'user_ip'    => Utils::get_remote_ip(),
				'user_agent' => Utils::get_user_agent(),
				'referer'    => $this->getReferer(),
				'created_at' => $current_time,
			);
		}

		return $this->meta_data;
	}

	/**
	 * Get form referer
	 *
	 * @return string
	 */
	private function getReferer() {
		if ( isset( $_POST['_dcf_referer'] ) && is_string( $_POST['_dcf_referer'] ) ) {
			return sanitize_text_field( $_POST['_dcf_referer'] );
		}

		return '';
	}
}
