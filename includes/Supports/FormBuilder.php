<?php

namespace DialogContactForm\Supports;

use DialogContactForm\Fields\Recaptcha2;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormBuilder {

	/**
	 * @var ContactForm
	 */
	private $form;

	/**
	 * List of errors after validation
	 * @var array
	 */
	private $errors = array();

	/**
	 * Success message after form submission
	 * @var string|null
	 */
	private $success_message;

	/**
	 * Error message after form submission
	 * @var string|null
	 */
	private $error_message;

	/**
	 * Dialog_Contact_Form_Form constructor.
	 *
	 * @param int $form_id
	 */
	public function __construct( $form_id = 0 ) {
		$this->errors          = isset( $GLOBALS['_dcf_errors'] ) ? $GLOBALS['_dcf_errors'] : array();
		$this->success_message = isset( $GLOBALS['_dcf_mail_sent_ok'] ) ? $GLOBALS['_dcf_mail_sent_ok'] : null;
		$this->error_message   = isset( $GLOBALS['_dcf_validation_error'] ) ? $GLOBALS['_dcf_validation_error'] : null;
		$this->form            = new ContactForm( $form_id );
	}

	/**
	 * Print field html
	 *
	 * @param string $html
	 * @param bool $echo
	 *
	 * @return string|void
	 */
	private static function printHtml( $html, $echo = true ) {
		if ( ! $echo ) {
			return $html;
		}

		echo $html;
	}

	/**
	 * Generate text field
	 *
	 * @param \DialogContactForm\Abstracts\Field $field
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function label( $field, $echo = true ) {
		if ( 'placeholder' == $this->form->getSetting( 'labelPosition' ) ) {
			return '';
		}

		$required_abbr = '';
		if ( $field->isRequired() ) {
			$required_abbr = sprintf( '&nbsp;<abbr class="dcf-required" title="%s">*</abbr>',
				esc_html__( 'Required', 'dialog-contact-form' )
			);
		} // Backward compatibility
		elseif ( in_array( 'required', (array) $field->get( 'validation' ) ) ) {
			$required_abbr = sprintf( '&nbsp;<abbr class="dcf-required" title="%s">*</abbr>',
				esc_html__( 'Required', 'dialog-contact-form' )
			);
		}

		$html = sprintf( '<label for="%1$s" class="label">%2$s%3$s</label>',
			$field->getId(), esc_attr( $field->get( 'field_title' ) ), $required_abbr
		);

		return self::printHtml( $html, $echo );
	}

	/**
	 * Get success message
	 *
	 * @return null|string
	 */
	public function getSuccessMessage() {
		if ( empty( $this->success_message ) ) {
			return null;
		}

		return '<p>' . $this->success_message . '</p>';
	}

	/**
	 * Get error message
	 *
	 * @return null|string
	 */
	public function getErrorMessage() {
		if ( empty( $this->error_message ) ) {
			return null;
		}

		return '<p>' . $this->error_message . '</p>';
	}

	/**
	 * Form Opening tag
	 *
	 * @param array $options
	 *
	 * @return string
	 */
	public function formOpen( $options = array() ) {
		$action = isset( $options['action'] ) ? $options['action'] : $_SERVER['REQUEST_URI'];
		$class  = isset( $options['class'] ) ? $options['class'] : 'dcf-form dcf-columns';

		return '<form action="' . $action . '" class="' . $class . '" method="POST" accept-charset="UTF-8" enctype="multipart/form-data" novalidate>';
	}

	/**
	 * Form Closing tag
	 *
	 * @return string
	 */
	public function formClose() {
		return '</form>';
	}

	/**
	 * Form content
	 *
	 * @param bool $submit_button
	 *
	 * @return string|null
	 */
	public function formContent( $submit_button = true ) {
		// If there is no field, exist
		if ( ! $this->getForm()->isValid() ) {
			return null;
		}
		$nonce   = wp_create_nonce( 'dialog_contact_form_nonce' );
		$referer = esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$html    = '';

		$html .= '<div class="dcf-response">';
		$html .= '<div class="dcf-success">' . $this->getSuccessMessage() . '</div>';
		$html .= '<div class="dcf-error">' . $this->getErrorMessage() . '</div>';
		$html .= '</div>';

		// System field
		$html .= '<input type="hidden" id="_dcf_nonce" name="_dcf_nonce" value="' . $nonce . '"/>';
		$html .= '<input type="hidden" name="_dcf_referer" value="' . $referer . '"/>';
		$html .= '<input type="hidden" name="_dcf_id" value="' . $this->getForm()->getId() . '"/>';

		/** @var \DialogContactForm\Abstracts\Field $field */
		foreach ( $this->getForm()->getFormFields() as $field ) {

			$style = '';
			if ( $field->isHiddenField() ) {
				$style .= 'style="display: none"';
			}

			$field_width = $field->has( 'field_width' ) ? esc_attr( $field->get( 'field_width' ) ) : 'is-12';

			$html .= sprintf( '<div class="dcf-column %s" %s>', $field_width, $style );
			$html .= '<div class="dcf-field">';

			if ( $field->showLabel() ) {
				$html .= $this->label( $field, false );
			}

			$html .= '<div class="dcf-control">';

			$html .= $field->render();

			// Show error message if any
			if ( ! empty( $this->errors[ $field->getName() ][0] ) ) {
				$html .= '<div class="dcf-error-message">';
				$html .= esc_attr( $this->errors[ $field['field_name'] ][0] );
				$html .= '</div>';
			}

			$html .= '</div>'; // .dcf-control
			$html .= '</div>'; // .dcf-field
			$html .= '</div>'; // .dcf-column
		}

		// If Google reCAPTCHA, add here
		$recaptcha = new Recaptcha2();
		$recaptcha->setFormId( $this->getForm()->getId() );
		$html .= $recaptcha->render();

		// Submit button
		if ( $submit_button ) {
			$html .= '<div class="dcf-column is-12"><div class="dcf-field"><div class="dcf-control">';
			$html .= $this->submitButton();
			$html .= '</div></div></div>' . PHP_EOL;
		}

		return $html;
	}

	/**
	 * Contact form
	 *
	 * @return string
	 */
	public function form() {
		$html = $this->formOpen();
		$html .= $this->formContent();
		$html .= $this->formClose();

		return $html;
	}

	/**
	 * Submit button
	 *
	 * @return string
	 */
	public function submitButton() {
		$btnAlign = $this->getForm()->getSetting( 'btnAlign', 'left' );
		$btnLabel = $this->getForm()->getSetting( 'btnLabel', __( 'Submit', 'dialog-contact-form' ) );

		$html = sprintf( '<div class="%s"><button type="submit" class="button dcf-submit">%s</button></div>',
			( $btnAlign == 'right' ) ? 'dcf-level-right' : 'dcf-level-left', esc_attr( $btnLabel )
		);

		return $html;
	}

	/**
	 * @return ContactForm
	 */
	public function getForm() {
		return $this->form;
	}
}
