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
		$this->admin_icon  = '<svg><use href="#dcf-icon-password"></use></svg>';
		$this->priority    = 60;
		$this->input_class = 'dcf-input dcf-input-password';
		$this->type        = 'password';
		$this->init_form_fields();
	}

	/**
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed on the form settings screen.
	 */
	public function init_form_fields() {
		parent::init_form_fields();

		$this->form_fields['autocomplete'] = array(
			'type'    => 'select',
			'label'   => __( 'Autocomplete', 'dialog-contact-form' ),
			'options' => [
				'off'              => __( 'Off', 'dialog-contact-form' ),
				'current-password' => __( 'Current Password', 'dialog-contact-form' ),
				'new-password'     => __( 'New Password', 'dialog-contact-form' ),
			]
		);
	}
}
