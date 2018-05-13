<?php

namespace DialogContactForm\Fields;

class Time extends Text {
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
}