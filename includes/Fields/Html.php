<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Html extends Field {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_title',
		'field_width',
		'html',
	);

	/**
	 * Html constructor.
	 */
	public function __construct() {
		$this->admin_id           = 'html';
		$this->admin_label        = __( 'HTML', 'dialog-contact-form' );
		$this->admin_icon         = '<svg><use href="#dcf-icon-html"></use></svg>';
		$this->priority           = 160;
		$this->type               = 'html';
		$this->show_in_entry      = false;
		$this->is_fillable        = false;
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
		if ( ! $this->has( 'html' ) ) {
			return '';
		}

		$html = wp_filter_post_kses( $this->get( 'html' ) );

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
		return true;
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 *
	 * @return null
	 */
	public function sanitize( $value ) {
		return null;
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function getValue() {
		return null;
	}
}
