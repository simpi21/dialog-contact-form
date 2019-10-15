<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Checkbox extends Field {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_id',
		'options',
		'field_width',
		'field_class',
	);

	/**
	 * Checkbox constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'checkbox';
		$this->admin_label = __( 'Checkbox', 'dialog-contact-form' );
		$this->admin_icon  = '<svg><use href="#dcf-icon-checkbox"></use></svg>';
		$this->priority    = 40;
		$this->input_class = 'dcf-checkbox';
		$this->type        = 'checkbox';
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
		$name  = $this->getName() . '[]';

		$html = '';
		foreach ( $this->getOptions() as $option ) {
			$option = trim( $option );
			if ( empty( $option ) ) {
				continue;
			}

			$attributes = array(
				'type'    => 'checkbox',
				'id'      => sanitize_title_with_dashes( $id . '_' . $option ),
				'class'   => $class,
				'name'    => $name,
				'value'   => esc_attr( $option ),
				'checked' => in_array( $option, $value ),
			);
			$html       .= '<label class="dcf-checkbox-container">';
			$html       .= '<input ' . $this->arrayToAttributes( $attributes ) . '> ' . esc_attr( $option );
			$html       .= '</label>';
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

		if ( is_string( $value ) ) {
			return in_array( $value, $this->getOptions() );
		}

		if ( is_array( $value ) ) {
			$is_valid = array();
			foreach ( $value as $item ) {
				if ( ! in_array( $item, $this->getOptions() ) ) {
					$is_valid[] = 'no';
				}
			}

			return ! in_array( 'no', $is_valid );
		}

		return false;
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function sanitize( $value ) {
		$options = $this->getOptions();

		if ( is_scalar( $value ) && in_array( $value, $options ) ) {
			return $value;
		}

		if ( is_array( $value ) ) {
			return array_intersect( $value, $options );
		}

		return '';
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function getValue() {
		if ( empty( $_POST[ $this->data['field_name'] ] ) ) {
			return array();
		}

		if ( is_array( $_POST[ $this->data['field_name'] ] ) ) {
			return $_POST[ $this->data['field_name'] ];
		}

		return array();
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
