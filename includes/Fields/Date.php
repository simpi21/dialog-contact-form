<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Date extends Text {

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
		'field_class',
		'autocomplete',
		'min_date',
		'max_date',
		'native_html5',
	);

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'date';

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

		if ( ! $this->is_html_date() ) {
			$this->type = 'text';
		}

		return '<input ' . $this->build_attributes() . '>';
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		if ( $value instanceof \DateTime ) {
			return true;
		}

		if ( strtotime( $value ) === false ) {
			return false;
		}

		$date = date_parse( $value );

		return checkdate( $date['month'], $date['day'], $date['year'] );
	}

	/**
	 * Get min date
	 *
	 * @return string
	 */
	protected function get_min_date() {
		if ( empty( $this->field['min_date'] ) ) {
			return '';
		}

		if ( ! $this->validate( $this->field['min_date'] ) ) {
			return '';
		}

		return esc_attr( $this->field['min_date'] );
	}

	/**
	 * Get max date
	 *
	 * @return string
	 */
	protected function get_max_date() {
		if ( empty( $this->field['max_date'] ) ) {
			return '';
		}

		if ( ! $this->validate( $this->field['max_date'] ) ) {
			return '';
		}

		return esc_attr( $this->field['max_date'] );
	}

	/**
	 * Check if it is HTML5 Date
	 *
	 * @return bool
	 */
	protected function is_html_date() {
		if ( empty( $this->field['native_html5'] ) ) {
			return false;
		}

		return ( 'off' !== $this->field['native_html5'] );
	}
}
