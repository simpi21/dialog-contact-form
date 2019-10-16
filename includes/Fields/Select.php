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
		$this->admin_icon  = '<svg width="30" height="30"><use href="#dcf-icon-select"></use></svg>';
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
		$options    = $this->getOptions();
		$value      = $this->getValue();
		$attributes = $this->buildAttributes( false );

		if ( $this->has( 'placeholder' ) ) {
			unset( $attributes['placeholder'] );
			$attributes['data-placeholder'] = $this->getPlaceholder();
		}

		$html = '<div class="dcf-select-container">';
		$html .= '<select ' . $this->arrayToAttributes( $attributes ) . '>';

		if ( $this->has( 'placeholder' ) ) {
			$html .= sprintf( '<option value="">%s</option>', $this->getPlaceholder() );
		}
		foreach ( $options as $option ) {
			$option   = trim( $option );
			$selected = ( $value == $option ) ? ' selected' : '';
			$html     .= sprintf( '<option value="%1$s" %2$s>%1$s</option>', esc_attr( $option ), $selected );
		}
		$html .= '</select>';
		$html .= '</div>';

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
		return in_array( $value, $this->getOptions() );
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function sanitize( $value ) {
		if ( in_array( $value, $this->getOptions() ) ) {
			return $value;
		}

		return '';
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
			'options'        => [
				'type'        => 'textarea',
				'label'       => __( 'Add options', 'dialog-contact-form' ),
				'description' => __( 'One option per line.', 'dialog-contact-form' ),
				'rows'        => 5,
			],
		];
	}
}

