<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Select extends Field {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'required_field',
		'options',
		'field_width',
		'field_id',
		'field_class',
		'field_value',
		'autocomplete',
		'placeholder',
	);

	/**
	 * Select constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'select';
		$this->admin_label = __( 'Select', 'dialog-contact-form' );
		$this->admin_icon  = 'fas fa-angle-down';
		$this->priority    = 130;
		$this->input_class = 'dcf-select';
		$this->type        = 'select';
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
		$options    = $this->get_options();
		$value      = $this->get_value();
		$attributes = $this->build_attributes( false );

		if ( ! empty( $this->field['placeholder'] ) ) {
			unset( $attributes['placeholder'] );
			$attributes['data-placeholder'] = $this->get_placeholder();
		}

		$html = '<div class="dcf-select-container">';
		$html .= '<select ' . $this->array_to_attributes( $attributes ) . '>';

		if ( ! empty( $this->field['placeholder'] ) ) {
			$html .= sprintf( '<option value="">%s</option>', esc_attr( $this->field['placeholder'] ) );
		}
		foreach ( $options as $option ) {
			$option   = trim( $option );
			$selected = ( $value == $option ) ? ' selected' : '';
			$html     .= sprintf( '<option value="%1$s" %2$s>%1$s</option>', esc_attr( $option ), $selected );
		}
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return in_array( $value, $this->get_options() );
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function sanitize( $value ) {
		if ( in_array( $value, $this->get_options() ) ) {
			return $value;
		}

		return '';
	}
}