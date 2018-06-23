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
		$this->admin_icon         = 'fas fa-code';
		$this->priority           = 160;
		$this->type               = 'html';
		$this->show_in_entry      = false;
		$this->admin_only         = true;
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
		if ( empty( $this->field['html'] ) ) {
			return '';
		}

		return wp_filter_post_kses( $this->field['html'] );
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		return false;
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
	protected function get_value() {
		return null;
	}
}