<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Select extends Abstract_Field {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'required_field',
		'options',
		'field_width',
		'field_id',
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
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return in_array( $value, $this->get_options() );
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function sanitize( $value ) {
		if ( in_array( $value, $this->get_options() ) ) {
			return $value;
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
			return null;
		}

		return esc_attr( $_POST[ $this->field['field_name'] ] );
	}
}