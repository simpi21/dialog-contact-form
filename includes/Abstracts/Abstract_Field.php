<?php

namespace DialogContactForm\Abstracts;

abstract class Abstract_Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'text';

	/**
	 * Current form ID
	 *
	 * @var int
	 */
	protected $form_id = 0;

	/**
	 * Current field configuration
	 *
	 * @var array
	 */
	protected $field = array();

	/**
	 * Check if current field has any error
	 *
	 * @var bool
	 */
	protected $has_error = false;

	/**
	 * Render field html for frontend display
	 *
	 * @param $field
	 *
	 * @return string
	 */
	abstract public function render( $field );

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 */
	abstract public function validate( $value );

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 */
	abstract public function sanitize( $value );

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	abstract protected function get_value();

	/**
	 * Get field name attribute
	 *
	 * @return string
	 */
	protected function get_name() {
		return sanitize_title_with_dashes( $this->field['field_name'] );
	}

	/**
	 * Get field id attribute
	 *
	 * @return string
	 */
	protected function get_id() {
		return sanitize_title_with_dashes( $this->field['field_id'] . '-' . $this->form_id );
	}

	/**
	 * Get field class attribute
	 *
	 * @param string $default
	 *
	 * @return string
	 */
	protected function get_class( $default = '' ) {
		$class = $default;
		if ( ! empty( $this->field['field_class'] ) ) {
			$class = $this->field['field_class'];
		}

		return esc_attr( $class );
	}

	/**
	 * Generate placeholder for current field
	 *
	 * @return string
	 */
	protected function get_placeholder() {
		if ( empty( $this->field['placeholder'] ) ) {
			return '';
		}

		return sprintf( ' placeholder="%s"', esc_attr( $this->field['placeholder'] ) );
	}

	/**
	 * Get required attribute text
	 *
	 * @return string
	 */
	protected function get_required() {
		$required_attr = '';
		if ( in_array( 'required', $this->field['validation'] ) ) {
			$required_attr = ' required';
		}

		return $required_attr;
	}

	/**
	 * Get field type
	 *
	 * @return string
	 */
	protected function get_type() {
		return $this->type;
	}
}