<?php

namespace DialogContactForm\Supports;

use DialogContactForm\FieldManager;
use DialogContactForm\Fields\Recaptcha2;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormBuilder {

	/**
	 * Form ID
	 * @var int
	 */
	private $form_id = 0;

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
	 * Configuration for current form
	 * @var array
	 */
	private $configuration = array();

	/**
	 * List of fields for current form
	 * @var array
	 */
	private $fields = array();

	/**
	 * Check if form is valid
	 *
	 * @var bool
	 */
	private $is_valid_form = false;

	/**
	 * Plugin global options for all forms
	 * @var array
	 */
	private $options = array();

	/**
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * @param int $form_id
	 *
	 * @return FormBuilder
	 */
	public static function init( $form_id = 0 ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $form_id );
		}

		return self::$instance;
	}

	/**
	 * Dialog_Contact_Form_Form constructor.
	 *
	 * @param int $form_id
	 */
	public function __construct( $form_id = 0 ) {
		$this->options         = Utils::get_option();
		$this->errors          = isset( $GLOBALS['_dcf_errors'] ) ? $GLOBALS['_dcf_errors'] : array();
		$this->success_message = isset( $GLOBALS['_dcf_mail_sent_ok'] ) ? $GLOBALS['_dcf_mail_sent_ok'] : null;
		$this->error_message   = isset( $GLOBALS['_dcf_validation_error'] ) ? $GLOBALS['_dcf_validation_error'] : null;

		if ( $form_id ) {
			$this->form_id = $form_id;
			$fields        = get_post_meta( $form_id, '_contact_form_fields', true );
			$configuration = get_post_meta( $form_id, '_contact_form_config', true );

			if ( is_array( $fields ) && count( $fields ) ) {
				$this->fields        = $fields;
				$this->is_valid_form = true;
				$this->configuration = is_array( $configuration ) && count( $configuration ) ? $configuration : array();
			}
		}
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
	 * @param array $setting
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function label( $setting, $echo = true ) {
		if ( 'placeholder' == $this->configuration['labelPosition'] ) {
			return '';
		}

		$validation    = isset( $setting['validation'] ) ? (array) $setting['validation'] : array();
		$required_abbr = '';
		if ( isset( $setting['required_field'] ) && 'on' == $setting['required_field'] ) {
			$required_abbr = sprintf( '&nbsp;<abbr class="dcf-required" title="%s">*</abbr>',
				esc_html__( 'Required', 'dialog-contact-form' )
			);
		} // Backward compatibility
		elseif ( in_array( 'required', $validation ) ) {
			$required_abbr = sprintf( '&nbsp;<abbr class="dcf-required" title="%s">*</abbr>',
				esc_html__( 'Required', 'dialog-contact-form' )
			);
		}

		$id   = sanitize_title_with_dashes( $setting['field_id'] . '-' . $this->form_id );
		$html = sprintf( '<label for="%1$s" class="label">%2$s%3$s</label>',
			$id, esc_attr( $setting['field_title'] ), $required_abbr
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
		if ( ! ( is_array( $this->fields ) && count( $this->fields ) > 0 ) ) {
			return null;
		}
		$fieldManager = FieldManager::init();
		$nonce        = wp_create_nonce( 'dialog_contact_form_nonce' );
		$referer      = esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$html         = '';

		$html .= '<div class="dcf-response">';
		$html .= '<div class="dcf-success">' . $this->getSuccessMessage() . '</div>';
		$html .= '<div class="dcf-error">' . $this->getErrorMessage() . '</div>';
		$html .= '</div>';

		// System field
		$html .= '<input type="hidden" id="_dcf_nonce" name="_dcf_nonce" value="' . $nonce . '"/>';
		$html .= '<input type="hidden" name="_dcf_referer" value="' . $referer . '"/>';
		$html .= '<input type="hidden" name="_dcf_id" value="' . $this->form_id . '"/>';

		foreach ( $this->fields as $field ) {
			$field_type = isset( $field['field_type'] ) ? esc_attr( $field['field_type'] ) : 'text';
			$class_name = $fieldManager->get( $field_type );
			if ( ! method_exists( $class_name, 'render' ) ) {
				continue;
			}

			/** @var \DialogContactForm\Abstracts\Field $field_class */
			$field_class = new $class_name;
			$field_class->setFormId( $this->form_id );
			$field_class->setField( $field );

			$style = '';
			if ( $field_class->isHiddenField() ) {
				$style .= 'style="display: none"';
			}

			$field_width = ! empty( $field['field_width'] ) ? esc_attr( $field['field_width'] ) : 'is-12';

			$html .= sprintf( '<div class="dcf-column %s" %s>', $field_width, $style );
			$html .= '<div class="dcf-field">';

			if ( $field_class->showLabel() ) {
				$html .= $this->label( $field, false );
			}

			$html .= '<div class="dcf-control">';

			$html .= $field_class->render();

			// Show error message if any
			if ( isset( $field['field_name'], $this->errors[ $field['field_name'] ][0] ) ) {
				$html .= '<div class="dcf-error-message">' . esc_attr( $this->errors[ $field['field_name'] ][0] ) . '</div>';
			}

			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
		}

		// If Google reCAPTCHA, add here
		$recaptcha = new Recaptcha2();
		$recaptcha->setFormId( $this->form_id );
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
		$html = sprintf( '<div class="%s"><button type="submit" class="button dcf-submit">%s</button></div>',
			( isset( $this->configuration['btnAlign'] ) && $this->configuration['btnAlign'] == 'right' ) ? 'dcf-level-right' : 'dcf-level-left',
			esc_attr( $this->configuration['btnLabel'] )
		);

		return $html;
	}

	/**
	 * @return bool
	 */
	public function isValidForm() {
		return $this->is_valid_form;
	}
}
