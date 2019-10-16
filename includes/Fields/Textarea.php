<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Textarea extends Text {

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
		'rows',
	);

	/**
	 * Text constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'textarea';
		$this->admin_label = __( 'Textarea', 'dialog-contact-form' );
		$this->admin_icon  = '<svg><use href="#dcf-icon-textarea"></use></svg>';
		$this->priority    = 20;
		$this->input_class = 'dcf-textarea';
		$this->type        = 'textarea';
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

		$html = '<textarea ' . $this->buildAttributes() . '>' . $this->getValue() . '</textarea>';

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
		return _sanitize_text_fields( $value, true );
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function getValue() {
		if ( isset( $_POST[ $this->get( 'field_name' ) ] ) ) {
			return esc_textarea( $_POST[ $this->get( 'field_name' ) ] );
		}

		if ( $this->has( 'field_value' ) ) {
			return esc_textarea( $this->get( 'field_value' ) );
		}

		return null;
	}

	/**
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed on the form settings screen.
	 */
	public function init_form_fields() {
		parent::init_form_fields();

		$this->form_fields['rows'] = [
			'type'     => 'number',
			'meta_key' => '_contact_form_fields',
			'label'    => __( 'Rows', 'dialog-contact-form' ),
		];
	}
}
