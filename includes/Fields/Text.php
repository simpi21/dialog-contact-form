<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Text extends Field {

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
	);

	/**
	 * Text constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'text';
		$this->admin_label = __( 'Text', 'dialog-contact-form' );
		$this->admin_icon  = '<svg><use href="#dcf-icon-text"></use></svg>';
		$this->priority    = 10;
		$this->input_class = 'dcf-input dcf-input-text';
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

		$html = '<input ' . $this->buildAttributes() . '>';

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
		return ! ( is_array( $value ) || is_object( $value ) );
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

	/**
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed on the form settings screen.
	 */
	public function init_form_fields() {
		$this->form_fields = [
			'field_id'       => array(
				'type'        => 'text',
				'label'       => __( 'Field ID', 'dialog-contact-form' ),
				'description' => __( 'Please make sure the ID is unique and not used elsewhere in this form. This field allows A-z 0-9 & underscore chars without spaces.',
					'dialog-contact-form' ),
			),
			'required_field' => array(
				'type'        => 'buttonset',
				'label'       => __( 'Required Field', 'dialog-contact-form' ),
				'description' => __( 'Check this option to mark the field required. A form will not submit unless all required fields are provided.',
					'dialog-contact-form' ),
				'default'     => 'off',
				'options'     => array(
					'off' => esc_html__( 'No', 'dialog-contact-form' ),
					'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
				),
			),
			'field_class'    => array(
				'type'        => 'text',
				'label'       => __( 'Field Class', 'dialog-contact-form' ),
				'description' => __( 'Insert additional class(es) (separated by blank space) for more personalization.',
					'dialog-contact-form' ),
			),
			'placeholder'    => array(
				'type'        => 'text',
				'label'       => __( 'Placeholder Text', 'dialog-contact-form' ),
				'description' => __( 'Insert placeholder message.', 'dialog-contact-form' ),
			),
			'autocomplete'   => array(
				'type'    => 'select',
				'label'   => __( 'Autocomplete', 'dialog-contact-form' ),
				'options' => Utils::autocomplete_values()
			),
		];
	}
}
