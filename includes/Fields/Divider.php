<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Divider extends Field {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_class',
	);

	/**
	 * Text constructor.
	 */
	public function __construct() {
		$this->admin_id           = 'divider';
		$this->admin_label        = __( 'Divider', 'dialog-contact-form' );
		$this->admin_icon         = 'fas fa-arrows-alt-h';
		$this->priority           = 170;
		$this->input_class        = 'dcf-divider';
		$this->show_label_in_form = false;
		$this->show_in_entry      = false;
	}

	/**
	 * Render field html for frontend display
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	public function render( $field = array() ) {
		if ( ! empty( $field ) ) {
			$this->setField( $field );
		}

		return '<hr class="' . $this->get_class() . '">';
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return true;
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return null
	 */
	public function sanitize( $value ) {
		return null;
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function get_value() {
		return null;
	}
}