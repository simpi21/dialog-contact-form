<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class File extends Field {

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
		'multiple',
	);

	/**
	 * File constructor.
	 */
	public function __construct() {
		$this->admin_id    = 'file';
		$this->admin_label = __( 'File', 'dialog-contact-form' );
		$this->admin_icon  = '<i class="fas fa-upload"></i>';
		$this->priority    = 150;
		$this->input_class = 'dcf-input-file';
		$this->type        = 'file';
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

		$attributes = $this->buildAttributes( false );

		if ( $this->isMultiple() ) {
			$attributes['name'] = $this->getName() . '[]';
		}

		$attributes = $this->arrayToAttributes( $attributes );

		return '<input ' . $attributes . '>';
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
	protected function getValue() {
		// TODO: Implement get_value() method.
	}

	/**
	 * Get max upload size in bytes
	 *
	 * @return int
	 */
	public function getMaxFileSize() {
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
	public function getAllowedMimeTypes() {
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
}
