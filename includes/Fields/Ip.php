<?php

namespace DialogContactForm\Fields;

class Ip extends Text {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		// Content
		'field_type',
		'field_title',
		'placeholder',
		'required_field',
		'field_width',
		// Advance
		'field_id',
		// Additional
		'field_value',
		'field_class',
	);

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return filter_var( $value, FILTER_VALIDATE_IP ) !== false;
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
}