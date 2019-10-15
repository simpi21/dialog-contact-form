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
		$this->admin_icon         = '<svg><use href="#dcf-icon-divider"></use></svg>';
		$this->priority           = 170;
		$this->input_class        = 'dcf-divider';
		$this->show_label_in_form = false;
		$this->show_in_entry      = false;
		$this->is_fillable        = false;
		$this->init_form_fields();
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

		$html = '<hr class="' . $this->getClass() . '">';

		return apply_filters( 'dialog_contact_form/preview/field', $html, $this );
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
	protected function getValue() {
		return null;
	}

	/**
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed on the form settings screen.
	 */
	public function init_form_fields() {
		$this->form_fields = [
			'field_class' => array(
				'type'        => 'text',
				'label'       => __( 'Field Class', 'dialog-contact-form' ),
				'description' => __( 'Insert additional class(es) (separated by blank space) for more personalization.',
					'dialog-contact-form' ),
			),
		];
	}
}