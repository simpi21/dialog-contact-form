<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Email extends Text {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_id',
		'required_field',
		'field_class',
		'field_width',
		'autocomplete',
		'placeholder',
		'multiple',
	);

	/**
	 * Email constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'email';
		$this->admin_label = __( 'Email', 'dialog-contact-form' );
		$this->admin_icon  = 'far fa-envelope';
		$this->priority    = 50;
		$this->input_class = 'dcf-input dcf-input-email';
		$this->type        = 'email';
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return filter_var( $value, FILTER_VALIDATE_EMAIL ) !== false;
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function sanitize( $value ) {
		return sanitize_email( $value );
	}
}