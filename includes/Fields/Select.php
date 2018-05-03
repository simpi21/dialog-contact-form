<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

class Select extends Abstract_Field {

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
		$options = $this->get_options();

		$html = '<div class="dcf-select-container">';
		$html .= sprintf(
			'<select id="%1$s" class="%4$s" name="%2$s" %3$s>',
			$this->get_id(),
			$this->get_name(),
			$this->get_required(),
			$this->get_class( 'select' )
		);

		if ( ! empty( $this->field['placeholder'] ) ) {
			$html .= sprintf( '<option value="">%s</option>', esc_attr( $this->field['placeholder'] ) );
		}
		foreach ( $options as $option ) {
			$option   = trim( $option );
			$selected = ( $this->get_value() == $option ) ? ' selected' : '';
			$html     .= sprintf( '<option value="%1$s" %2$s>%1$s</option>', esc_attr( $option ), $selected );
		}
		$html .= '</select>';
		$html .= '</div>';

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
			return null;
		}

		return esc_attr( $_POST[ $this->field['field_name'] ] );
	}
}