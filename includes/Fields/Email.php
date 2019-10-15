<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Email extends Text {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_id',
		'required_field',
		'field_class',
		'field_width',
		'autocomplete',
		'placeholder',
		'multiple',
	);

	/**
	 * Email constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'email';
		$this->admin_label = __( 'Email', 'dialog-contact-form' );
		$this->admin_icon  = '<svg><use href="#dcf-icon-email"></use></svg>';
		$this->priority    = 50;
		$this->input_class = 'dcf-input dcf-input-email';
		$this->type        = 'email';
		$this->init_form_fields();
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return filter_var( $value, FILTER_VALIDATE_EMAIL ) !== false;
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function sanitize( $value ) {
		return sanitize_email( $value );
	}

	/**
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed on the form settings screen.
	 */
	public function init_form_fields() {
		parent::init_form_fields();

		$this->form_fields['autocomplete'] = [
			'type'    => 'select',
			'label'   => __( 'Autocomplete', 'dialog-contact-form' ),
			'default' => 'email',
			'options' => [
				'on'    => __( 'On', 'dialog-contact-form' ),
				'off'   => __( 'Off', 'dialog-contact-form' ),
				'email' => __( 'Email', 'dialog-contact-form' ),
			]
		];

		$this->form_fields['multiple'] = [
			'type'    => 'buttonset',
			'label'   => __( 'Multiple', 'dialog-contact-form' ),
			'default' => 'off',
			'options' => array(
				'off' => esc_html__( 'No', 'dialog-contact-form' ),
				'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
			),
		];
	}
}
