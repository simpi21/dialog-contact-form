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
		$this->admin_icon      = '<i class="far fa-eye-slash"></i>';
		$this->priority        = 80;
		$this->input_class     = 'dcf-input-hidden';
		$this->type            = 'hidden';
		$this->show_in_entry   = false;
		$this->is_hidden_field = true;
		$this->admin_only      = true;
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return ( $value == $this->field['field_value'] );
	}

	/**
	 * Get field value
	 *
	 * @return string
	 */
	protected function getValue() {
		if ( ! empty( $this->field['field_value'] ) ) {
			return esc_attr( $this->field['field_value'] );
		}

		return '';
	}
}
