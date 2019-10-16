<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Radio extends Field {

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
	);

	/**
	 * Radio constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'radio';
		$this->admin_label = __( 'Radio', 'dialog-contact-form' );
		$this->admin_icon  = '<svg><use href="#dcf-icon-radio"></use></svg>';
		$this->priority    = 120;
		$this->input_class = 'dcf-radio';
		$this->type        = 'radio';
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

		$id    = $this->getId();
		$value = $this->getValue();
		$class = $this->getClass();
		$name  = $this->getName();

		$html = '';
		foreach ( $this->getOptions() as $option ) {
			$option = trim( $option );
			if ( empty( $option ) ) {
				continue;
			}

			$radio_id   = sanitize_title_with_dashes( $id . '_' . $option );
			$attributes = array(
				'type'     => 'radio',
				'id'       => $radio_id,
				'class'    => $class,
				'name'     => $name,
				'value'    => esc_attr( $option ),
				'required' => $this->isRequired(),
				'checked'  => ( $option === $value ),
			);

			$html .= '<label for="' . $radio_id . '" class="dcf-radio-container">';
			$html .= '<input ' . $this->arrayToAttributes( $attributes ) . '> ' . esc_attr( $option );
			$html .= '</label>';
		}

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
