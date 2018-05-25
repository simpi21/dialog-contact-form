<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

class Text extends Abstract_Field {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'placeholder',
		'required_field',
		'field_width',
		'field_id',
		'field_value',
		'field_class',
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

		$html = sprintf( '<input id="%1$s" class="%2$s" name="%3$s" value="%4$s" type="%5$s" %6$s %7$s>',
			$this->get_id(),
			$this->get_class( 'input' ),
			$this->get_name(),
			$this->get_value(),
			$this->get_type(),
			$this->get_placeholder(),
			$this->get_required()
		);

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
		return ! ( is_array( $value ) || is_object( $value ) );
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function sanitize( $value ) {
		return sanitize_text_field( $value );
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