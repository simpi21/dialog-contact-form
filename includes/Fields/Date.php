<?php

namespace DialogContactForm\Fields;

class Date extends Text {

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
		'field_class',
		// Optional
		'min_date',
		'max_date',
		'native_html5',
	);

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