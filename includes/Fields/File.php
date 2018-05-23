<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

class File extends Abstract_Field {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		// Content
		'field_type',
		'field_title',
		'required_field',
		'field_width',
		// Advance
		'field_id',
		// Optional
		'max_file_size',
		'allowed_file_types',
		'multiple_files',
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

		$accept   = '';
		$multiple = '';
		$html     = sprintf( '<input id="%1$s" class="%2$s" name="%3$s" type="file" %4$s %5$s %6$s>',
			$this->get_id(),
			$this->get_class( 'file' ),
			$this->get_name(),
			$multiple,
			$accept,
			$this->get_required()
		);

		return $html;
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
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
