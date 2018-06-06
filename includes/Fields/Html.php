<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Html extends Abstract_Field {

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
	 * Render field html for frontend display
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	public function render( $field = array() ) {
		// TODO: Implement render() method.
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function validate( $value ) {
		// TODO: Implement validate() method.
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 */
	public function sanitize( $value ) {
		// TODO: Implement sanitize() method.
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function get_value() {
		// TODO: Implement get_value() method.
	}
}