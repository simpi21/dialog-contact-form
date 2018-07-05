<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Number extends Field {

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
}
