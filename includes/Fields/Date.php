<?php

namespace DialogContactForm\Fields;

class Date extends Text {
	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'date';

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		if ( $value instanceof \DateTime ) {
			return true;
		}

		if ( strtotime( $value ) === false ) {
			return false;
		}

		$date = date_parse( $value );

		return checkdate( $date['month'], $date['day'], $date['year'] );
	}
}