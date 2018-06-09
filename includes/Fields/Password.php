<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Password extends Text {

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
		'autocomplete',
	);

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'password';
}