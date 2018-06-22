<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Text extends Field {

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
	);

	/**
	 * Input CSS class
	 *
	 * @var string
	 */
	protected $input_class = 'input';

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

		return '<input ' . $this->build_attributes() . '>';
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return ! ( is_array( $value ) || is_object( $value ) );
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function sanitize( $value ) {
		return sanitize_text_field( $value );
	}
}