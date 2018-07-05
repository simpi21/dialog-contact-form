<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Url extends Text {

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
		'field_value',
		'field_class',
		'autocomplete',
	);

	/**
	 * Text constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'url';
		$this->admin_label = __( 'URL', 'dialog-contact-form' );
		$this->admin_icon  = '<svg><use href="#dcf-icon-url"></use></svg>';
		$this->priority    = 110;
		$this->input_class = 'dcf-input dcf-input-url';
		$this->type        = 'url';
	}

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
