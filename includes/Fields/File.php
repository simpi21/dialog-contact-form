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
		'field_type',
		'field_title',
		'field_id',
		'required_field',
		'field_class',
		'field_width',
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

		$accept = '';
		$html   = sprintf( '<input id="%1$s" class="%2$s" name="%3$s" type="file" %4$s %5$s %6$s>',
			$this->get_id(),
			$this->get_class( 'file' ),
			$this->get_name(),
			$this->multiple_files(),
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

	/**
	 * Get max upload size in bytes
	 *
	 * @return int
	 */
	private function get_max_file_size() {
		$max_upload_size = wp_max_upload_size();
		if ( isset( $this->field['max_file_size'] ) && is_numeric( $this->field['max_file_size'] ) ) {
			$max_upload_size = $this->field['max_file_size'] * pow( 1024, 2 );
		}

		return (int) $max_upload_size;
	}

	/**
	 * Get multiple file attribute
	 *
	 * @return string
	 */
	private function multiple_files() {
		if ( $this->is_multiple() ) {
			return ' multiple="true"';
		}

		return '';
	}

	/**
	 * Get allowed file types
	 *
	 * @return array
	 */
	private function allowed_file_types() {
		$allowed_mime_types = get_allowed_mime_types();
		$allowed_file_types = array_keys( $allowed_mime_types );
		if ( ! empty( $this->field['allowed_file_types'] ) ) {
			$allowed_file_types = $this->field['allowed_file_types'];
		}

		return $allowed_file_types;
	}

	/**
	 * Check if field support multiple file upload
	 *
	 * @return bool
	 */
	private function is_multiple() {
		return ( isset( $this->field['multiple_files'] ) && 'on' === $this->field['multiple_files'] );
	}
}
