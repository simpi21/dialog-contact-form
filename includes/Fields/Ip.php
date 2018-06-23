<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ip extends Text {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_id',
		'placeholder',
		'required_field',
		'field_width',
		'field_class',
	);

	/**
	 * Ip constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'ip';
		$this->admin_label = __( 'IP Address', 'dialog-contact-form' );
		$this->admin_icon  = 'fas fa-mouse-pointer';
		$this->priority    = 120;
		$this->input_class = 'dcf-input dcf-input-ip';
		$this->type        = 'text';
	}

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