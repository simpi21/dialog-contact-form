<?php

namespace DialogContactForm\Abstracts;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	 * Get metabox fields settings for current field type
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_id',
		'field_title',
		'field_width',
	);

	/**
	 * Input CSS class
	 *
	 * @var string
	 */
	protected $input_class;

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

	/**
	 * Get metabox fields settings for current field type
	 *
	 * @return array
	 */
	public function getMetaboxFields() {
		return $this->metabox_fields;
	}

	/**
	 * Check if the value is present.
	 *
	 * @param  mixed $value
	 *
	 * @return boolean
	 */
	public function is_empty( $value ) {
		$value = preg_replace( '/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $value );

		return empty( $value );
	}

	/**
	 * Generate input attribute
	 *
	 * @param bool $string
	 *
	 * @return array|string
	 */
	protected function build_attributes( $string = true ) {
		$input_type = $this->get_type();
		$attributes = array(
			'id'          => $this->get_id(),
			'class'       => $this->get_class(),
			'name'        => $this->get_name(),
			'placeholder' => $this->get_placeholder(),
		);

		if ( 'textarea' !== $input_type ) {
			$attributes['type'] = $input_type;
		}

		if ( 'textarea' === $input_type ) {
			$attributes['rows'] = $this->get_rows();
		}

		if ( ! in_array( $input_type, array( 'textarea', 'file' ) ) ) {
			$attributes['autocomplete'] = $this->get_autocomplete();
		}

		if ( ! in_array( $input_type, array( 'textarea', 'file', 'password' ) ) ) {
			$attributes['value'] = $this->get_value();
		}

		if ( 'file' === $input_type ) {
			$attributes['accept'] = $this->get_accept();
		}

		if ( 'number' === $input_type ) {
			$attributes['max']  = $this->get_max();
			$attributes['min']  = $this->get_min();
			$attributes['step'] = $this->get_step();
		}

		if ( 'date' === $input_type ) {
			$attributes['max'] = $this->get_max_date();
			$attributes['min'] = $this->get_min_date();
		}

		if ( 'radio' === $input_type || 'checkbox' === $input_type ) {
			// $attributes['checked'] = $this->get_checked();
		}

		if ( 'hidden' === $input_type ) {
			$attributes['spellcheck']   = false;
			$attributes['tabindex']     = '-1';
			$attributes['autocomplete'] = 'off';
		}

		if ( 'email' === $input_type || 'file' === $input_type ) {
			$attributes['multiple'] = $this->is_multiple();
		}

		if ( ! in_array( $input_type, array( 'hidden', 'image', 'submit', 'reset', 'button' ) ) ) {
			$attributes['required'] = $this->is_required();
		}

		if ( $string ) {
			return $this->array_to_attributes( $attributes );
		}

		return array_filter( $attributes );
	}

	/**
	 * Get field type
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
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
		if ( ! empty( $default ) ) {
			$this->input_class = $default;
		}
		$class = $this->input_class;
		if ( ! empty( $this->field['field_class'] ) ) {
			$class = $this->field['field_class'];
		}

		if ( $this->has_error() ) {
			$class .= ' dcf-has-error';
		}

		return esc_attr( $class );
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
	 * Get field name attribute
	 *
	 * @return string
	 */
	protected function get_name() {
		return sanitize_title_with_dashes( $this->field['field_name'] );
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

		return esc_attr( $this->field['placeholder'] );
	}

	/**
	 * Generate autocomplete for current field
	 *
	 * @return string
	 */
	public function get_autocomplete() {
		if ( empty( $this->field['autocomplete'] ) ) {
			return '';
		}

		return esc_attr( $this->field['autocomplete'] );
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	abstract protected function get_value();

	/**
	 * Get accept for file field
	 *
	 * @return string
	 */
	private function get_accept() {
		if ( 'file' !== $this->get_type() ) {
			return '';
		}

		$mimes              = array();
		$allowed_mime_types = get_allowed_mime_types();

		$file_types = $this->field['allowed_file_types'] ? $this->field['allowed_file_types'] : array();
		foreach ( $file_types as $file_type ) {
			if ( isset( $allowed_mime_types[ $file_type ] ) ) {
				$mimes[] = $allowed_mime_types[ $file_type ];
			}
		}

		if ( $mimes ) {
			return implode( ',', $mimes );
		}

		return '';
	}

	/**
	 * Get max attribute
	 *
	 * @return string
	 */
	protected function get_max() {
		if ( empty( $this->field['number_max'] ) ) {
			return '';
		}

		return floatval( $this->field['number_max'] );
	}

	/**
	 * Get min attribute
	 *
	 * @return string
	 */
	protected function get_min() {
		if ( empty( $this->field['number_min'] ) ) {
			return '';
		}

		return floatval( $this->field['number_min'] );
	}

	/**
	 * Get step attribute
	 *
	 * @return string
	 */
	protected function get_step() {
		if ( empty( $this->field['number_step'] ) ) {
			return '';
		}

		return floatval( $this->field['number_step'] );
	}

	/**
	 * Get max date
	 *
	 * @return string
	 */
	protected function get_max_date() {
		return '';
	}

	/**
	 * Get min date
	 *
	 * @return string
	 */
	protected function get_min_date() {
		return '';
	}

	/**
	 * Check if field support multiple file upload
	 *
	 * @return bool
	 */
	protected function is_multiple() {
		return ( isset( $this->field['multiple'] ) && 'on' === $this->field['multiple'] );
	}

	/**
	 * Check current field is required
	 *
	 * @return bool
	 */
	public function is_required() {
		if ( empty( $this->field['required_field'] ) ) {
			return false;
		}

		if ( 'on' == $this->field['required_field'] ) {
			return true;
		}
		// Backward compatibility
		if ( is_array( $this->field['validation'] ) && in_array( 'required', $this->field['validation'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Convert array to input attributes
	 *
	 * @param array $attributes
	 *
	 * @return string
	 */
	protected function array_to_attributes( $attributes ) {
		$string = array_map( function ( $key, $value ) {
			if ( empty( $value ) && 'value' !== $key ) {
				return null;
			}
			if ( in_array( $key, array( 'required', 'checked', 'multiple' ) ) && $value ) {
				return $key;
			}

			// If boolean value
			if ( is_bool( $value ) ) {
				return sprintf( '%s="%s"', $key, $value ? 'true' : 'false' );
			}

			// If array value
			if ( is_array( $value ) ) {
				return sprintf( '%s="%s"', $key, implode( " ", $value ) );
			}

			// If string value
			return sprintf( '%s="%s"', $key, esc_attr( $value ) );

		}, array_keys( $attributes ), array_values( $attributes ) );

		return implode( ' ', array_filter( $string ) );
	}

	/**
	 * Generate placeholder attribute for current field
	 *
	 * @return string
	 */
	protected function get_placeholder_attribute() {
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
	protected function get_required_attribute() {
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
	 * Get options
	 *
	 * @return array
	 */
	protected function get_options() {
		if ( is_array( $this->field['options'] ) ) {
			return $this->field['options'];
		}

		if ( is_string( $this->field['options'] ) ) {
			$options = explode( PHP_EOL, $this->field['options'] );

			return array_map( 'trim', $options );
		}

		if ( empty( $this->field['options'] ) ) {
			return array();
		}

		return array();
	}

	/**
	 * Get rows attribute
	 *
	 * @return string
	 */
	protected function get_rows() {
		if ( isset( $this->field['rows'] ) && is_numeric( $this->field['rows'] ) ) {
			return intval( $this->field['rows'] );
		}

		return '';
	}
}