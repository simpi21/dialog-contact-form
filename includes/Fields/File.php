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

		$name_attribute = $this->get_name();
		if ( $this->is_multiple() ) {
			$name_attribute = $name_attribute . '[]';
		}

		$html = sprintf( '<input id="%1$s" class="%2$s" name="%3$s" type="file" %4$s %5$s %6$s>',
			$this->get_id(),
			$this->get_class( 'file' ),
			$name_attribute,
			$this->get_multiple_attribute(),
			$this->get_accept_attribute(),
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
	public function get_max_file_size() {
		$max_upload_size = wp_max_upload_size();
		if ( isset( $this->field['max_file_size'] ) && is_numeric( $this->field['max_file_size'] ) ) {
			$max_upload_size = $this->field['max_file_size'] * pow( 1024, 2 );
		}

		return (int) $max_upload_size;
	}

	/**
	 * Get allowed file types
	 *
	 * @return array
	 */
	public function get_allowed_mime_types() {
		$mime_types         = array();
		$allowed_mime_types = get_allowed_mime_types();

		if ( ! empty( $this->field['allowed_file_types'] ) && is_array( $this->field['allowed_file_types'] ) ) {
			foreach ( $this->field['allowed_file_types'] as $allowed_file_type ) {
				if ( ! isset( $allowed_mime_types[ $allowed_file_type ] ) ) {
					continue;
				}
				$mime_types[ $allowed_file_type ] = $allowed_mime_types[ $allowed_file_type ];
			}

			$mime_types = array_filter( $mime_types );
		}

		return $mime_types ? $mime_types : $allowed_mime_types;
	}

	/**
	 * Get accept attribute
	 *
	 * @return string
	 */
	private function get_accept_attribute() {
		$mimes              = array();
		$allowed_mime_types = get_allowed_mime_types();

		$file_types = $this->field['allowed_file_types'] ? $this->field['allowed_file_types'] : array();
		foreach ( $file_types as $file_type ) {
			if ( isset( $allowed_mime_types[ $file_type ] ) ) {
				$mimes[] = $allowed_mime_types[ $file_type ];
			}
		}

		if ( $mimes ) {
			return ' accept="' . implode( ',', $mimes ) . '"';
		}

		return '';
	}

	/**
	 * Get multiple file attribute
	 *
	 * @return string
	 */
	private function get_multiple_attribute() {
		if ( $this->is_multiple() ) {
			return ' multiple="true"';
		}

		return '';
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
