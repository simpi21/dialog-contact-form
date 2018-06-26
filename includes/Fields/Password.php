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
	 * Password constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'password';
		$this->admin_label = __( 'Password', 'dialog-contact-form' );
		$this->admin_icon  = '<i class="fas fa-key"></i>';
		$this->priority    = 60;
		$this->input_class = 'dcf-input dcf-input-password';
		$this->type        = 'password';
	}
}
