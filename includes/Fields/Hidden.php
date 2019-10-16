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
	 * Hidden constructor.
	 */
	public function __construct() {
		$this->admin_id        = 'hidden';
		$this->admin_label     = __( 'Hidden', 'dialog-contact-form' );
		$this->admin_icon      = '<svg><use href="#dcf-icon-hidden"></use></svg>';
		$this->priority        = 80;
		$this->input_class     = 'dcf-input-hidden';
		$this->type            = 'hidden';
		$this->show_in_entry   = false;
		$this->is_hidden_field = true;
		$this->init_form_fields();
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return ( $value == $this->getValue() );
	}

	/**
	 * Get field value
	 *
	 * @return string
	 */
	protected function getValue() {
		return $this->get( 'field_value' );
	}

	/**
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed on the form settings screen.
	 */
	public function init_form_fields() {
		parent::init_form_fields();

		$this->form_fields['field_value'] = [
			'type'        => 'text',
			'label'       => __( 'Default Value', 'dialog-contact-form' ),
			'description' => __( 'Define field default value.', 'dialog-contact-form' ),
		];
	}
}
