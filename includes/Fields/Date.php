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
	 * Checkbox constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'date';
		$this->admin_label = __( 'Date', 'dialog-contact-form' );
		$this->admin_icon  = '<svg><use href="#dcf-icon-date"></use></svg>';
		$this->priority    = 90;
		$this->input_class = 'dcf-input dcf-input-date';
		$this->type        = 'date';
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

		if ( ! $this->isHtmlDate() ) {
			$this->type = 'text';
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
	public function getMinDate() {
		$min_date = $this->get( 'min_date' );

		if ( ! $this->validate( $min_date ) ) {
			return '';
		}

		return esc_attr( $min_date );
	}

	/**
	 * Get max date
	 *
	 * @return string
	 */
	public function getMaxDate() {
		$max_date = $this->get( 'max_date' );

		if ( ! $this->validate( $max_date ) ) {
			return '';
		}

		return esc_attr( $max_date );
	}

	/**
	 * Check if it is HTML5 Date
	 *
	 * @return bool
	 */
	public function isHtmlDate() {
		return ( 'off' !== $this->get( 'native_html5' ) );
	}
}
