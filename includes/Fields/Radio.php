<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Radio extends Abstract_Field {

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
		// Optional
		'inline_list'
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