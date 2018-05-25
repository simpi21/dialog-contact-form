<?php

namespace DialogContactForm\Fields;

class Time extends Text {

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
		'field_class',
		'native_html5',
	);

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'time';

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		// Validate 24 hour format
		if ( preg_match( "/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $value ) ) {
			return true;
		}

		return (bool) preg_match( '/^(1[0-2]|0?[1-9]):[0-5][0-9] (AM|PM)$/i', $value );
	}

	/**
	 * Check if it is HTML5 Date
	 *
	 * @return bool
	 */
	protected function is_html_time() {
		if ( empty( $this->field['native_html5'] ) ) {
			return false;
		}

		return ( 'off' !== $this->field['native_html5'] );
	}
}