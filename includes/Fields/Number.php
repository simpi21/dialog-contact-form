<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Number extends Abstract_Field {

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
		'autocomplete',
	);

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'number';

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