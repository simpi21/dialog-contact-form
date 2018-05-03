<?php

namespace DialogContactForm\Fields;

class Url extends Text {

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'url';

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return filter_var( $value, FILTER_VALIDATE_URL ) !== false;
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function sanitize( $value ) {
		return esc_url_raw( $value );
	}
}