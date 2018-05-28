<?php

namespace DialogContactForm;

class Config {

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
	 * Form validation messages
	 *
	 * @var array
	 */
	private $validation_messages = array();

	/**
	 * Config constructor.
	 *
	 * @param int $form_id
	 */
	public function __construct( $form_id = 0 ) {
		if ( $form_id ) {
			$this->setFormId( $form_id );
		}
		if ( ! $form_id ) {
			global $post;

			if ( $post instanceof \WP_Post && 'dialog-contact-form' === $post->post_type ) {
				$this->setFormId( $post->ID );
			}
		}

		if ( $this->form_id ) {
			$this->form_fields         = get_post_meta( $form_id, '_contact_form_fields', true );
			$this->form_settings       = get_post_meta( $form_id, '_contact_form_config', true );
			$this->form_actions        = get_post_meta( $form_id, 'after_submit_actions', true );
			$this->validation_messages = get_post_meta( $form_id, '_contact_form_messages', true );
		}
	}

	/**
	 * @return mixed
	 */
	public function getFormId() {
		return $this->form_id;
	}

	/**
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
		if ( ! $this->options ) {
			$this->options = get_dialog_contact_form_option();
		}

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
}