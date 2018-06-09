<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Acceptance extends Text {

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'checkbox';

	/**
	 * Input CSS class
	 *
	 * @var string
	 */
	protected $input_class = 'dcf-checkbox';

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_id',
		'required_field',
		'field_width',
		'acceptance_text',
		'checked_by_default',
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

		$value = $this->get_value();
		if ( empty( $value ) && $this->is_checked_by_default() ) {
			$value = 'on';
		}

		$attributes          = $this->build_attributes( false );
		$attributes['value'] = 'on';
		if ( 'on' == $value ) {
			$attributes['checked'] = true;
		}

		$html = '<input type="hidden" name="' . $this->get_name() . '" value="off">';
		$html .= '<label class="dcf-checkbox-container">';
		$html .= '<input ' . $this->array_to_attributes( $attributes ) . '> ' . $this->get_acceptance_text();
		$html .= '</label>';

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
		return in_array( $value, array( 'yes', 'on', '1', 1, true, 'true' ), true );
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function sanitize( $value ) {
		return in_array( $value, array( 'on', 'off' ) ) ? $value : 'off';
	}

	/**
	 * Get field acceptance text
	 *
	 * @return string
	 */
	protected function get_acceptance_text() {
		return ! empty( $this->field['acceptance_text'] ) ? $this->field['acceptance_text'] : '';
	}

	/**
	 * Check if field is checked by default
	 *
	 * @return boolean
	 */
	protected function is_checked_by_default() {
		if ( empty( $this->field['checked_by_default'] ) ) {
			return false;
		}

		return $this->validate( $this->field['checked_by_default'] );
	}
}