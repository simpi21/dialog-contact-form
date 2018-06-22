<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Radio extends Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'radio';

	/**
	 * Input CSS class
	 *
	 * @var string
	 */
	protected $input_class = 'dcf-radio';

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

		$id    = $this->get_id();
		$value = $this->get_value();
		$class = $this->get_class();
		$name  = $this->get_name();

		$html = '';
		foreach ( $this->get_options() as $option ) {
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
				'required' => $this->is_required(),
				'checked'  => ( $option === $value ),
			);

			$html .= '<label for="' . $radio_id . '" class="dcf-radio-container">';
			$html .= '<input ' . $this->array_to_attributes( $attributes ) . '> ' . esc_attr( $option );
			$html .= '</label>';
		}

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