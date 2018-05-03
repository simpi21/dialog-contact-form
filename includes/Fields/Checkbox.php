<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

class Checkbox extends Abstract_Field {

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
	 */
	public function validate( $value ) {
		// TODO: Implement validate() method.
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 */
	public function sanitize( $value ) {
		// TODO: Implement sanitize() method.
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