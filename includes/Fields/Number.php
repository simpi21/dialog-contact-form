<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Number extends Text {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_id',
		'placeholder',
		'required_field',
		'field_width',
		'number_min',
		'number_max',
		'number_step',
		'field_value',
		'field_class',
	);

	/**
	 * Number constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'number';
		$this->admin_label = __( 'Number', 'dialog-contact-form' );
		$this->admin_icon  = '<svg><use href="#dcf-icon-number"></use></svg>';
		$this->priority    = 70;
		$this->input_class = 'dcf-input dcf-input-number';
		$this->type        = 'number';
		$this->init_form_fields();
	}

	/**
	 * Render field html for frontend display
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	public function render( $field = array() ) {
		if ( ! empty( $field ) ) {
			$this->setField( $field );
		}

		$html = '<input ' . $this->buildAttributes() . '>';

		return apply_filters( 'dialog_contact_form/preview/field', $html, $this );
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return is_numeric( $value );
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return int|float
	 */
	public function sanitize( $value ) {
		if ( is_numeric( $value ) ) {
			return $value + 0;
		}

		return intval( $value );
	}

	/**
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed on the form settings screen.
	 */
	public function init_form_fields() {
		parent::init_form_fields();

		$this->form_fields['number_min'] = [
			'type'  => 'number',
			'label' => __( 'Min Number', 'dialog-contact-form' ),
		];

		$this->form_fields['number_max'] = [
			'type'  => 'number',
			'label' => __( 'Max Number', 'dialog-contact-form' ),
		];

		$this->form_fields['number_step'] = [
			'type'        => 'number',
			'label'       => __( 'Step Number', 'dialog-contact-form' ),
			'description' => __( 'For allowing decimal values set step value (e.g. "0.01" to allow decimals to two decimal places).',
				'dialog-contact-form' ),
		];
	}
}
