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
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed on the form settings screen.
	 */
	public function init_form_fields() {
		parent::init_form_fields();

		$this->form_fields['autocomplete'] = [
			'type'    => 'select',
			'label'   => __( 'Autocomplete', 'dialog-contact-form' ),
			'options' => [
				'on'  => __( 'On', 'dialog-contact-form' ),
				'off' => __( 'Off', 'dialog-contact-form' ),
			]
		];

		$this->form_fields['min_date'] = [
			'type'  => 'date',
			'label' => __( 'Min. Date', 'dialog-contact-form' ),
		];

		$this->form_fields['max_date'] = [
			'type'  => 'date',
			'label' => __( 'Max. Date', 'dialog-contact-form' ),
		];

		$this->form_fields['native_html5'] = [
			'type'    => 'buttonset',
			'label'   => __( 'Native HTML5', 'dialog-contact-form' ),
			'default' => 'on',
			'options' => array(
				'off' => esc_html__( 'No', 'dialog-contact-form' ),
				'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
			),
		];
	}
}
