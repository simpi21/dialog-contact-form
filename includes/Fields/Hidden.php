<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hidden extends Text {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_id',
		'field_value',
	);

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'hidden';

	/**
	 * Get field value
	 *
	 * @return string
	 */
	protected function get_value() {
		if ( ! empty( $this->field['field_value'] ) ) {
			return esc_attr( $this->field['field_value'] );
		}

		return '';
	}
}
