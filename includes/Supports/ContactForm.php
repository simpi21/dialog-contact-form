<?php

namespace DialogContactForm\Supports;

use DialogContactForm\Abstracts\Action;
use DialogContactForm\Collections\Actions;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContactForm {

	const POST_TYPE = 'dialog-contact-form';

	/**
	 * @var int
	 */
	private static $found_items = 0;

	/**
	 * @var int
	 */
	private $id = 0;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $title;

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
	 * ContactForm constructor.
	 *
	 * @param int|\WP_Post|null $post Optional. Post ID or post object.
	 */
	public function __construct( $post = null ) {

		if ( ! $this->options ) {
			$this->options = Utils::get_option();
		}

		if ( ! empty( $this->options['mailchimp_api_key'] ) ) {
			$this->mailchimp_api_key = $this->options['mailchimp_api_key'];
			unset( $this->options['mailchimp_api_key'] );
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

		$post = get_post( $post );

		if ( $post && self::POST_TYPE == get_post_type( $post ) ) {
			$this->id    = $post->ID;
			$this->name  = $post->post_name;
			$this->title = $post->post_title;

			$this->form_fields   = (array) get_post_meta( $this->id, '_contact_form_fields', true );
			$this->form_settings = (array) get_post_meta( $this->id, '_contact_form_config', true );

			$form_actions = (array) get_post_meta( $this->id, '_contact_form_actions', true );
			if ( empty( $form_actions ) ) {
				$form_actions = array( 'email_notification', 'success_message', 'redirect' );
			}
			$this->form_actions = $form_actions;

			$messages = (array) get_post_meta( $this->id, '_contact_form_messages', true );
			foreach ( $messages as $message_key => $message ) {
				if ( ! empty( $message ) ) {
					$this->validation_messages[ $message_key ] = $message;
				}
			}

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
	 * Get forms
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public static function find( $args = array() ) {
		$defaults = array(
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'offset'         => 0,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		);

		$args = wp_parse_args( $args, $defaults );

		$args['post_type'] = self::POST_TYPE;

		$q     = new \WP_Query();
		$posts = $q->query( $args );

		self::$found_items = $q->found_posts;

		$forms = array();

		foreach ( (array) $posts as $post ) {
			$forms[] = new self( $post );
		}

		return $forms;
	}

	/**
	 * Update current form
	 *
	 * @param int $form_id
	 * @param array $data
	 */
	public static function update( $form_id, array $data ) {

		// Update form settings
		if ( isset( $data['config'] ) && is_array( $data['config'] ) ) {
			update_post_meta( $form_id, '_contact_form_config', self::sanitize_value( $data['config'] ) );
		}

		// Update form validation messages
		if ( isset( $data['messages'] ) && is_array( $data['messages'] ) ) {
			update_post_meta( $form_id, '_contact_form_messages', self::sanitize_value( $data['messages'] ) );
		}

		// Update form fields settings
		if ( isset( $data['field'] ) && is_array( $data['field'] ) ) {
			$_data = array();
			foreach ( $data['field'] as $field ) {
				$_data[] = self::sanitize_field( $field );
			}

			update_post_meta( $form_id, '_contact_form_fields', $_data );
		} else {
			delete_post_meta( $form_id, '_contact_form_fields' );
		}

		// Update form actions
		if ( isset( $data['actions'] ) && is_array( $data['actions'] ) ) {

			$actions       = array();
			$actionManager = Actions::init();

			foreach ( $data['actions'] as $action_id => $action_data ) {
				$className = $actionManager->get( $action_id );
				$action    = new $className;
				if ( ! $action instanceof Action ) {
					continue;
				}

				$actions[] = $action_id;

				$action->save( $form_id, $action_data );
			}

			update_post_meta( $form_id, '_contact_form_actions', self::sanitize_value( $actions ) );
		}
	}

	/**
	 * Sanitize meta value
	 *
	 * @param $input
	 *
	 * @return array|string
	 */
	private static function sanitize_value( $input ) {
		// Initialize the new array that will hold the sanitize values
		$new_input = array();

		if ( is_array( $input ) ) {
			// Loop through the input and sanitize each of the values
			foreach ( $input as $key => $value ) {
				if ( is_array( $value ) ) {
					$new_input[ $key ] = self::sanitize_value( $value );
				} else {
					$new_input[ $key ] = sanitize_text_field( $value );
				}
			}
		} else {
			return sanitize_text_field( $input );
		}

		return $new_input;
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	private static function sanitize_field( $data ) {
		$_data = array(
			'field_title'        => isset( $data['field_title'] ) ? sanitize_text_field( $data['field_title'] ) : '',
			'field_name'         => isset( $data['field_id'] ) ? sanitize_text_field( $data['field_id'] ) : '',
			'field_id'           => isset( $data['field_id'] ) ? sanitize_text_field( $data['field_id'] ) : '',
			'field_type'         => isset( $data['field_type'] ) ? sanitize_text_field( $data['field_type'] ) : '',
			'options'            => isset( $data['options'] ) ? wp_strip_all_tags( $data['options'] ) : '',
			'number_min'         => isset( $data['number_min'] ) ? sanitize_text_field( $data['number_min'] ) : '',
			'number_max'         => isset( $data['number_max'] ) ? sanitize_text_field( $data['number_max'] ) : '',
			'number_step'        => isset( $data['number_step'] ) ? sanitize_text_field( $data['number_step'] ) : '',
			'field_value'        => isset( $data['field_value'] ) ? sanitize_text_field( $data['field_value'] ) : '',
			'required_field'     => isset( $data['required_field'] ) ? sanitize_text_field( $data['required_field'] ) : '',
			'field_class'        => isset( $data['field_class'] ) ? sanitize_text_field( $data['field_class'] ) : '',
			'field_width'        => isset( $data['field_width'] ) ? sanitize_text_field( $data['field_width'] ) : '',
			'placeholder'        => isset( $data['placeholder'] ) ? sanitize_text_field( $data['placeholder'] ) : '',
			'autocomplete'       => isset( $data['autocomplete'] ) ? sanitize_text_field( $data['autocomplete'] ) : '',
			// Acceptance Field
			'acceptance_text'    => isset( $data['acceptance_text'] ) ? wp_kses_post( $data['acceptance_text'] ) : '',
			'checked_by_default' => isset( $data['checked_by_default'] ) ? sanitize_text_field( $data['checked_by_default'] ) : '',
			// Date Field
			'min_date'           => isset( $data['min_date'] ) ? sanitize_text_field( $data['min_date'] ) : '',
			'max_date'           => isset( $data['max_date'] ) ? sanitize_text_field( $data['max_date'] ) : '',
			// Date & Time Field
			'native_html5'       => isset( $data['native_html5'] ) ? sanitize_text_field( $data['native_html5'] ) : '',
			// File Field
			'max_file_size'      => isset( $data['max_file_size'] ) ? absint( $data['max_file_size'] ) : '',
			'allowed_file_types' => isset( $data['allowed_file_types'] ) ? self::sanitize_value( $data['allowed_file_types'] ) : array(),
			'rows'               => isset( $data['rows'] ) ? intval( $data['rows'] ) : '',
			// File & Email
			'multiple'           => isset( $data['multiple'] ) ? sanitize_text_field( $data['multiple'] ) : '',
			// HTML
			'html'               => isset( $data['html'] ) ? wp_kses_post( $data['html'] ) : '',
			// Depreciated
			'validation'         => isset( $data['validation'] ) ? self::sanitize_value( $data['validation'] ) : array(),
			'error_message'      => isset( $data['error_message'] ) ? sanitize_text_field( $data['error_message'] ) : '',
		);

		return array_filter( $_data );
	}

	/**
	 * Delete current form
	 *
	 * @param int $form_id
	 *
	 * @return bool
	 */
	public static function delete( $form_id ) {
		if ( wp_delete_post( $form_id, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get numbers of items found in current query
	 *
	 * @return int
	 */
	public static function count() {
		return self::$found_items;
	}

	/**
	 * Get form all settings data as array
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			'title'         => $this->title(),
			'id'            => $this->id(),
			'fields'        => $this->getFormFields(),
			'settings'      => $this->getFormSettings(),
			'actions'       => $this->getFormActions(),
			'messages'      => $this->getValidationMessages(),
			'has_file'      => $this->hasFile(),
			'has_recaptcha' => $this->hasRecaptcha(),
			'reset_form'    => $this->resetForm(),
		);
	}

	/**
	 * Get current form title
	 *
	 * @return string
	 */
	public function title() {
		return $this->title;
	}

	/**
	 * Get current form id
	 *
	 * @return int
	 */
	public function id() {
		return $this->id;
	}

	/**
	 * Get current form fields
	 *
	 * @return array
	 */
	public function getFormFields() {
		return $this->form_fields;
	}

	/**
	 * Get current form settings
	 *
	 * @return array
	 */
	public function getFormSettings() {
		return $this->form_settings;
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
	 * If current form has file field
	 *
	 * @return bool
	 */
	public function hasFile() {
		return $this->has_file;
	}

	/**
	 * If reCAPTCHA is enabled for the form
	 *
	 * @return bool
	 */
	public function hasRecaptcha() {
		return $this->has_recaptcha;
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
	 * The form's slug.
	 *
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Get form available fields type
	 *
	 * @return array
	 */
	public function getFieldTypes() {
		return $this->field_types;
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
}
