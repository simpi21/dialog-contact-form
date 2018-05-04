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
	 * @param array $field
	 *
	 * @return string
	 */
	abstract public function render( $field = array() );

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
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

		if ( $this->has_error() ) {
			$class .= ' dcf-has-error';
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
		if ( ! empty( $this->field['required_field'] ) ) {
			if ( 'on' == $this->field['required_field'] ) {
				return ' required';
			}
			if ( 'off' == $this->field['required_field'] ) {
				return '';
			}
		}

		// Backward compatibility
		if ( is_array( $this->field['validation'] ) && in_array( 'required', $this->field['validation'] ) ) {
			return ' required';
		}

		return '';
	}

	/**
	 * Get field type
	 *
	 * @return string
	 */
	protected function get_type() {
		return $this->type;
	}

	/**
	 * Get options
	 *
	 * @return array
	 */
	protected function get_options() {
		if ( empty( $this->field['options'] ) ) {
			return array();
		}

		if ( is_string( $this->field['options'] ) ) {
			return explode( PHP_EOL, $this->field['options'] );
		}

		return is_array( $this->field['options'] ) ? $this->field['options'] : array();
	}

	/**
	 * Check if there is any error for current field
	 *
	 * @return bool
	 */
	protected function has_error() {
		if ( ! empty( $GLOBALS['_dcf_errors'][ $this->get_name() ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get current form ID
	 *
	 * @return int
	 */
	public function getFormId() {
		return $this->form_id;
	}

	/**
	 * Set current form ID
	 *
	 * @param int $form_id
	 */
	public function setFormId( $form_id ) {
		$this->form_id = $form_id;
	}

	/**
	 * @return array
	 */
	public function getField() {
		return $this->field;
	}

	/**
	 * @param array $field
	 */
	public function setField( $field ) {
		$this->field = $field;
	}
}