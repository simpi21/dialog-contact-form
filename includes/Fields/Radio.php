<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

class Radio extends Abstract_Field {

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

		$html = '';
		foreach ( $this->get_options() as $option ) {
			$option   = trim( $option );
			$checked  = ( $this->get_value() == $option ) ? ' checked' : '';
			$radio_id = $this->get_id() . '-' . sanitize_title_with_dashes( $option );
			$html     .= sprintf(
				'<label for="%6$s" class="%5$s"><input type="radio" id="%6$s" name="%1$s" value="%2$s"%3$s%4$s> %2$s</label>',
				$this->get_name(),
				esc_attr( $option ),
				$checked,
				$this->get_required(),
				$this->get_class( 'dcf-radio-container' ),
				$radio_id
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
			return null;
		}

		return esc_attr( $_POST[ $this->field['field_name'] ] );
	}
}