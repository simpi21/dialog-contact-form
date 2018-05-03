<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

class Textarea extends Abstract_Field {

	/**
	 * Render field html for frontend display
	 *
	 * @param $field
	 *
	 * @return string
	 */
	public function render( $field ) {
		$this->field = $field;

		$html = sprintf( '<textarea id="%1$s" class="%2$s" name="%3$s" %5$s %6$s >%4$s</textarea>',
			$this->get_id(),
			$this->get_class( 'textarea' ),
			$this->get_name(),
			$this->get_value(),
			$this->get_placeholder(),
			$this->get_required()
		);

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
	 *
	 * @return string
	 */
	public function sanitize( $value ) {
		if ( function_exists( 'sanitize_textarea_field' ) ) {
			return sanitize_textarea_field( $value );
		}

		return _sanitize_text_fields( $value, true );
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

		return esc_textarea( $_POST[ $this->field['field_name'] ] );
	}
}