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

		return '<input ' . $this->generate_attributes() . '>';
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
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function get_value() {
		if ( empty( $_POST[ $this->field['field_name'] ] ) ) {
			return null;
		}

		return esc_attr( $_POST[ $this->field['field_name'] ] );
	}

	/**
	 * Get min attribute
	 *
	 * @return string
	 */
	protected function get_min_attribute() {
		if ( empty( $this->field['number_min'] ) ) {
			return '';
		}

		return sprintf( ' min="%s"', floatval( $this->field['number_min'] ) );
	}

	/**
	 * Get max attribute
	 *
	 * @return string
	 */
	protected function get_max_attribute() {
		if ( empty( $this->field['number_max'] ) ) {
			return '';
		}

		return sprintf( ' max="%s"', floatval( $this->field['number_max'] ) );
	}

	/**
	 * Get step attribute
	 *
	 * @return string
	 */
	protected function get_step_attribute() {
		if ( empty( $this->field['number_step'] ) ) {
			return '';
		}

		return sprintf( ' step="%s"', floatval( $this->field['number_step'] ) );
	}
}