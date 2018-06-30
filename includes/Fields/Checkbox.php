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
		$this->admin_icon  = '<i class="fas fa-list"></i>';
		$this->priority    = 40;
		$this->input_class = 'dcf-checkbox';
		$this->type        = 'checkbox';
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
		if ( empty( $_POST[ $this->field['field_name'] ] ) ) {
			return array();
		}

		if ( is_array( $_POST[ $this->field['field_name'] ] ) ) {
			return $_POST[ $this->field['field_name'] ];
		}

		return array();
	}
}
