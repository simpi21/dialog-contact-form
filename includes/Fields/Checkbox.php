<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Checkbox extends Abstract_Field {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_id',
		'options',
		'field_width',
		'field_class',
		'autocomplete',
	);

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

		$id    = $this->get_id();
		$value = $this->get_value();
		$name  = $this->get_name() . '[]';

		$html = '';
		foreach ( $this->get_options() as $option ) {
			$option = trim( $option );
			if ( empty( $option ) ) {
				continue;
			}
			$checked     = is_array( $value ) && in_array( $option, $value ) ? ' checked' : '';
			$checkbox_id = sanitize_title_with_dashes( $id . '_' . $option );
			$html        .= sprintf(
				'<label class="dcf-checkbox-container"><input type="checkbox" name="%1$s" value="%2$s" id="%3$s" %4$s> %2$s</label>',
				$name, esc_attr( $option ), $checkbox_id, $checked
			);
		}

		return $html;
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {

		if ( is_string( $value ) ) {
			return in_array( $value, $this->get_options() );
		}

		if ( is_array( $value ) ) {
			$is_valid = array();
			foreach ( $value as $item ) {
				if ( ! in_array( $item, $this->get_options() ) ) {
					$is_valid[] = 'no';
				}
			}

			return ! in_array( 'no', $is_valid );
		}

		return false;
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function sanitize( $value ) {
		$options = $this->get_options();

		if ( is_scalar( $value ) && in_array( $value, $options ) ) {
			return $value;
		}

		if ( is_array( $value ) ) {
			return array_intersect( $value, $options );
		}

		return '';
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function get_value() {
		if ( empty( $_POST[ $this->field['field_name'] ] ) ) {
			return array();
		}

		if ( is_array( $_POST[ $this->field['field_name'] ] ) ) {
			return $_POST[ $this->field['field_name'] ];
		}

		return array();
	}
}