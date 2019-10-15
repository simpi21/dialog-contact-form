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
		$this->admin_icon  = '<svg><use href="#dcf-icon-file"></use></svg>';
		$this->priority    = 150;
		$this->input_class = 'dcf-input-file';
		$this->type        = 'file';
		$this->init_form_fields();
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

		$html = '<input ' . $this->arrayToAttributes( $attributes ) . '>';

		return apply_filters( 'dialog_contact_form/preview/field', $html, $this );
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
		$max_file_size   = $this->get( 'max_file_size' );

		if ( is_numeric( $max_file_size ) ) {
			$max_upload_size = $max_file_size * pow( 1024, 2 );
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

		if ( is_array( $this->get( 'allowed_file_types' ) ) ) {
			foreach ( $this->get( 'allowed_file_types' ) as $allowed_file_type ) {
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
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed on the form settings screen.
	 */
	public function init_form_fields() {
		$this->form_fields = [
			'field_id'           => array(
				'type'        => 'text',
				'label'       => __( 'Field ID', 'dialog-contact-form' ),
				'description' => __( 'Please make sure the ID is unique and not used elsewhere in this form. This field allows A-z 0-9 & underscore chars without spaces.',
					'dialog-contact-form' ),
			),
			'required_field'     => array(
				'type'        => 'buttonset',
				'label'       => __( 'Required Field', 'dialog-contact-form' ),
				'description' => __( 'Check this option to mark the field required. A form will not submit unless all required fields are provided.',
					'dialog-contact-form' ),
				'default'     => 'off',
				'options'     => array(
					'off' => esc_html__( 'No', 'dialog-contact-form' ),
					'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
				),
			),
			'field_class'        => array(
				'type'        => 'text',
				'label'       => __( 'Field Class', 'dialog-contact-form' ),
				'description' => __( 'Insert additional class(es) (separated by blank space) for more personalization.',
					'dialog-contact-form' ),
			),
			'max_file_size'      => array(
				'type'        => 'file_size',
				'label'       => __( 'Max. File Size', 'dialog-contact-form' ),
				'description' => __( 'If you need to increase max upload size please contact your hosting.',
					'dialog-contact-form' ),
				'default'     => '2',
				'options'     => static::get_upload_file_size_options(),
			),
			'allowed_file_types' => array(
				'type'        => 'mime_type',
				'label'       => __( 'Allowed File Types', 'dialog-contact-form' ),
				'description' => __( 'Choose file types.', 'dialog-contact-form' ),
				'multiple'    => true,
				'options'     => static::get_allowed_mime_types_options(),
			),
			'multiple'           => array(
				'type'    => 'buttonset',
				'label'   => __( 'Multiple', 'dialog-contact-form' ),
				'default' => 'off',
				'options' => array(
					'off' => esc_html__( 'No', 'dialog-contact-form' ),
					'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
				),
			),
		];
	}

	/**
	 * creates array of upload sizes based on server limits
	 * to use in the file_sizes control
	 * @return array
	 */
	private static function get_upload_file_size_options() {
		$max_file_size = wp_max_upload_size() / pow( 1024, 2 ); //MB

		$sizes = array();
		for ( $file_size = 1; $file_size <= $max_file_size; $file_size ++ ) {
			$sizes[ $file_size ] = $file_size . 'MB';
		}

		return $sizes;
	}

	/**
	 * Get allowed mime types list
	 *
	 * @return array
	 */
	private static function get_allowed_mime_types_options() {
		$mime_types         = array();
		$allowed_mime_types = get_allowed_mime_types();
		foreach ( $allowed_mime_types as $extension => $allowed_mime_type ) {
			$mime_types[ $extension ] = $extension;
		}

		return $mime_types;
	}
}
