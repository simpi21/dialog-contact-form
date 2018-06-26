<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Textarea extends Field {

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
		$this->admin_icon  = '<i class="fas fa-paragraph"></i>';
		$this->priority    = 20;
		$this->input_class = 'dcf-textarea';
		$this->type        = 'textarea';
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

		return '<textarea ' . $this->buildAttributes() . '>' . $this->getValue() . '</textarea>';
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
		if ( function_exists( 'sanitize_textarea_field' ) ) {
			return sanitize_textarea_field( $value );
		}

		return _sanitize_text_fields( $value, true );
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function getValue() {
		if ( isset( $_POST[ $this->field['field_name'] ] ) ) {
			return esc_textarea( $_POST[ $this->field['field_name'] ] );
		}

		if ( ! empty( $this->field['field_value'] ) ) {
			return esc_textarea( $this->field['field_value'] );
		}

		return null;
	}
}
