<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Acceptance extends Text {

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

		$html = sprintf( '<input type="hidden" name="%s" value="off">', $this->get_name() );
		$html .= sprintf(
			'<label class="dcf-checkbox-container"><input type="checkbox" id="%1$s" name="%2$s" value="on" %4$s %5$s> %3$s</label>',
			$this->get_id(),
			$this->get_name(),
			$this->get_acceptance_text(),
			$this->get_required(),
			( 'on' == $value ) ? ' checked' : ''
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