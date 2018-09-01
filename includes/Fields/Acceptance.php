<?php

namespace DialogContactForm\Fields;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Acceptance extends Text {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_id',
		'required_field',
		'field_width',
		'acceptance_text',
		'checked_by_default',
	);

	/**
	 * Acceptance constructor.
	 */
	public function __construct() {
		$this->admin_id           = 'acceptance';
		$this->admin_label        = __( 'Acceptance', 'dialog-contact-form' );
		$this->admin_icon         = '<svg><use href="#dcf-icon-acceptance"></use></svg>';
		$this->priority           = 30;
		$this->input_class        = 'dcf-checkbox';
		$this->type               = 'checkbox';
		$this->show_label_in_form = false;
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

		$value = $this->getValue();
		if ( empty( $value ) && $this->isCheckedByDefault() ) {
			$value = 'on';
		}

		$attributes          = $this->buildAttributes( false );
		$attributes['value'] = 'on';
		if ( 'on' == $value ) {
			$attributes['checked'] = true;
		}

		$html = '<input type="hidden" name="' . $this->getName() . '" value="off">';
		$html .= '<label class="dcf-checkbox-container">';
		$html .= '<input ' . $this->arrayToAttributes( $attributes ) . '> ' . $this->getAcceptanceText();
		$html .= '</label>';

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
		return in_array( $value, array( 'yes', 'on', '1', 1, true, 'true' ), true );
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function sanitize( $value ) {
		return in_array( $value, array( 'on', 'off' ) ) ? $value : 'off';
	}
}
