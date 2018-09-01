<?php

namespace DialogContactForm\Abstracts;

use DialogContactForm\Supports\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Field extends Collection {

	/**
	 * Input type attribute
	 *
	 * @var string
	 */
	protected $type = 'text';

	/**
	 * Font Awesome Icon class
	 *
	 * @var string
	 */
	protected $admin_icon = '<svg><use href="#dcf-icon-text"></use></svg>';

	/**
	 * Field Label for admin usage
	 *
	 * @var string
	 */
	protected $admin_label = 'Untitled';

	/**
	 * Field unique id for admin usage
	 *
	 * @var string
	 */
	protected $admin_id = 'untitled';

	/**
	 * Field priority in admin
	 *
	 * @var int
	 */
	protected $priority = 300;

	/**
	 * Should this field show in admin entry
	 *
	 * @var bool
	 */
	protected $show_in_entry = true;

	/**
	 * Should this field's label show in form
	 *
	 * @var bool
	 */
	protected $show_label_in_form = true;

	/**
	 * Should this field hide in form
	 *
	 * @var bool
	 */
	protected $is_hidden_field = false;

	/**
	 * Check if current field can hold user submitted value.
	 * If set false, field will be exclude from sanitizing and validating
	 *
	 * @var bool
	 */
	protected $is_fillable = true;

	/**
	 * Current form ID
	 *
	 * @var int
	 */
	protected $form_id = 0;

	/**
	 * Check if current field has any error
	 *
	 * @var bool
	 */
	protected $has_error = false;

	/**
	 * Get metabox fields settings for current field type
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		'field_type',
		'field_id',
		'field_title',
		'field_width',
	);

	/**
	 * Input CSS class
	 *
	 * @var string
	 */
	protected $input_class = 'dcf-input';

	/**
	 * Render field html for frontend display
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	abstract public function render( $field = array() );

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	abstract public function validate( $value );

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 */
	abstract public function sanitize( $value );

	/**
	 * Get all items in collections
	 *
	 * @return array The collection's source data
	 */
	public function all() {
		$field = array(
			'type'               => $this->getType(),
			'label'              => $this->get( 'field_title' ),
			'id'                 => $this->get( 'field_id' ),
			'options'            => $this->getOptions(),
			'number_min'         => $this->getMin(),
			'number_max'         => $this->getMax(),
			'number_step'        => $this->getStep(),
			'default'            => $this->get( 'field_value' ),
			'required'           => $this->isRequired(),
			'field_class'        => $this->getClass(),
			'field_width'        => $this->get( 'field_width' ),
			'placeholder'        => $this->getPlaceholder(),
			'autocomplete'       => $this->getAutocomplete(),
			'acceptance_text'    => $this->getAcceptanceText(),
			'checked_by_default' => $this->isCheckedByDefault(),
			'min_date'           => $this->getMinDate(),
			'max_date'           => $this->getMaxDate(),
			'max_file_size'      => $this->getMaxFileSize(),
			'allowed_file_types' => $this->getAllowedMimeTypes(),
			'rows'               => $this->getRows(),
			'multiple'           => $this->isMultiple(),
			'html'               => $this->get( 'html' ),
		);

		return $field;
	}

	/**
	 * Get max upload size in bytes
	 *
	 * @return int
	 */
	public function getMaxFileSize() {
		$max_file_size = $this->get( 'max_file_size' );

		if ( is_numeric( $max_file_size ) ) {
			return $max_file_size * pow( 1024, 2 );
		}

		return null;
	}

	/**
	 * Get allowed file types
	 *
	 * @return array
	 */
	public function getAllowedMimeTypes() {
		return $this->get( 'allowed_file_types', array() );
	}

	/**
	 * Get current form ID
	 *
	 * @return int
	 */
	public function getFormId() {
		return $this->form_id;
	}

	/**
	 * Set current form ID
	 *
	 * @param int $form_id
	 */
	public function setFormId( $form_id ) {
		$this->form_id = $form_id;
	}

	/**
	 * Get field configuration
	 *
	 * @return array
	 */
	public function getField() {
		return $this->collections;
	}

	/**
	 * Set field configuration
	 *
	 * @param array $collections
	 */
	public function setField( $collections ) {
		$this->collections = $collections;
	}

	/**
	 * Get metabox fields settings for current field type
	 *
	 * @return array
	 */
	public function getMetaboxFields() {
		return $this->metabox_fields;
	}

	/**
	 * Check if the value is present.
	 *
	 * @param  mixed $value
	 *
	 * @return boolean
	 */
	public function isEmpty( $value ) {
		$value = preg_replace( '/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $value );

		return empty( $value );
	}

	/**
	 * Get admin label
	 *
	 * @return string
	 */
	public function getAdminLabel() {
		return $this->admin_label;
	}

	/**
	 * Get field icon
	 *
	 * @return string
	 */
	public function getAdminIcon() {
		return $this->admin_icon;
	}

	/**
	 * Get field unique id for admin usage
	 *
	 * @return string
	 */
	public function getAdminId() {
		return $this->admin_id;
	}

	/**
	 * If it is a hidden field
	 *
	 * @return bool
	 */
	public function isHiddenField() {
		return $this->is_hidden_field;
	}

	/**
	 * Check field label should show
	 * Some fields like Html, Divider should not show label
	 *
	 * @return bool
	 */
	public function showLabel() {
		return $this->show_label_in_form;
	}

	/**
	 * Should this field value show in admin entry page?
	 *
	 * @return bool
	 */
	public function showInEntry() {
		return $this->show_in_entry;
	}

	/**
	 * Get Priority
	 *
	 * Returns the priority for an action.
	 *
	 * @return int
	 */
	public function getPriority() {
		return $this->priority;
	}

	/**
	 * Check if current field can hold user submitted value.
	 * If set false, field will be exclude from sanitizing and validating
	 *
	 * @return bool
	 */
	public function isFillable() {
		return $this->is_fillable;
	}

	/**
	 * Generate input attribute
	 *
	 * @param bool $string
	 *
	 * @return array|string
	 */
	protected function buildAttributes( $string = true ) {
		$input_type = $this->getType();
		$attributes = array(
			'id'          => $this->getId(),
			'class'       => $this->getClass(),
			'name'        => $this->getName(),
			'placeholder' => $this->getPlaceholder(),
		);

		if ( ! in_array( $input_type, array( 'textarea', 'select' ) ) ) {
			$attributes['type'] = $input_type;
		}

		if ( 'textarea' === $input_type ) {
			$attributes['rows'] = $this->getRows();
		}

		if ( ! in_array( $input_type, array( 'textarea', 'file' ) ) ) {
			$attributes['autocomplete'] = $this->getAutocomplete();
		}

		if ( ! in_array( $input_type, array( 'textarea', 'file', 'password', 'select' ) ) ) {
			$attributes['value'] = $this->getValue();
		}

		if ( 'file' === $input_type ) {
			$attributes['accept'] = $this->getAccept();
		}

		if ( 'number' === $input_type ) {
			$attributes['max']  = $this->getMax();
			$attributes['min']  = $this->getMin();
			$attributes['step'] = $this->getStep();
		}

		if ( 'date' === $input_type ) {
			$attributes['max'] = $this->getMaxDate();
			$attributes['min'] = $this->getMinDate();
		}

		if ( 'hidden' === $input_type ) {
			$attributes['spellcheck']   = false;
			$attributes['tabindex']     = '-1';
			$attributes['autocomplete'] = 'off';
		}

		if ( 'email' === $input_type || 'file' === $input_type ) {
			$attributes['multiple'] = $this->isMultiple();
		}

		if ( ! in_array( $input_type, array( 'hidden', 'image', 'submit', 'reset', 'button' ) ) ) {
			$attributes['required'] = $this->isRequired();
		}

		if ( $string ) {
			return $this->arrayToAttributes( $attributes );
		}

		return array_filter( $attributes );
	}

	/**
	 * Get field type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Get field id attribute
	 *
	 * @return string
	 */
	public function getId() {
		return sanitize_title_with_dashes( $this->get( 'field_id' ) . '-' . $this->getFormId() );
	}

	/**
	 * Get field class attribute
	 *
	 * @param string $default
	 *
	 * @return string
	 */
	public function getClass( $default = '' ) {
		if ( ! empty( $default ) ) {
			$this->input_class = $default;
		}
		$class = $this->input_class;
		if ( $this->has( 'field_class' ) ) {
			$class = $this->get( 'field_class' );
		}

		if ( $this->hasError() ) {
			$class .= ' dcf-has-error';
		}

		return esc_attr( $class );
	}

	/**
	 * Check if there is any error for current field
	 *
	 * @return bool
	 */
	public function hasError() {
		if ( ! empty( $GLOBALS['_dcf_errors'][ $this->getName() ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get field name attribute
	 *
	 * @return string
	 */
	public function getName() {
		return esc_attr( $this->get( 'field_name' ) );
	}

	/**
	 * Generate placeholder for current field
	 *
	 * @return string
	 */
	public function getPlaceholder() {
		return esc_attr( $this->get( 'placeholder' ) );
	}

	/**
	 * Get rows attribute
	 *
	 * @return string
	 */
	public function getRows() {
		$rows = $this->get( 'rows' );

		return is_numeric( $rows ) ? intval( $rows ) : '';
	}

	/**
	 * Generate autocomplete for current field
	 *
	 * @return string
	 */
	public function getAutocomplete() {
		return esc_attr( $this->get( 'autocomplete' ) );
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function getValue() {
		if ( isset( $_POST[ $this->getName() ] ) ) {
			return $_POST[ $this->getName() ];
		}

		return $this->get( 'field_value' );
	}

	/**
	 * Get accept for file field
	 *
	 * @return string
	 */
	public function getAccept() {
		if ( 'file' !== $this->getType() ) {
			return '';
		}

		$mimes              = array();
		$allowed_mime_types = get_allowed_mime_types();

		$file_types = (array) $this->get( 'allowed_file_types' );
		foreach ( $file_types as $file_type ) {
			if ( isset( $allowed_mime_types[ $file_type ] ) ) {
				$mimes[] = $allowed_mime_types[ $file_type ];
			}
		}

		if ( $mimes ) {
			return implode( ',', $mimes );
		}

		return '';
	}

	/**
	 * Get max attribute
	 *
	 * @return string
	 */
	public function getMax() {
		if ( ! $this->has( 'number_max' ) ) {
			return '';
		}

		return floatval( $this->get( 'number_max' ) );
	}

	/**
	 * Get min attribute
	 *
	 * @return string
	 */
	public function getMin() {
		if ( ! $this->has( 'number_min' ) ) {
			return '';
		}

		return floatval( $this->get( 'number_min' ) );
	}

	/**
	 * Get step attribute
	 *
	 * @return string
	 */
	public function getStep() {
		if ( ! $this->has( 'number_step' ) ) {
			return '';
		}

		return floatval( $this->get( 'number_step' ) );
	}

	/**
	 * Get min date
	 *
	 * @return string
	 */
	public function getMinDate() {
		$min_date = $this->get( 'min_date' );

		if ( ! $this->validate( $min_date ) ) {
			return '';
		}

		return esc_attr( $min_date );
	}

	/**
	 * Get max date
	 *
	 * @return string
	 */
	public function getMaxDate() {
		$max_date = $this->get( 'max_date' );

		if ( ! $this->validate( $max_date ) ) {
			return '';
		}

		return esc_attr( $max_date );
	}

	/**
	 * Check if it is HTML5 Date
	 *
	 * @return bool
	 */
	public function isHtmlDate() {
		return ( 'off' !== $this->get( 'native_html5' ) );
	}

	/**
	 * Check if it is HTML5 Time
	 *
	 * @return bool
	 */
	protected function isHtmlTime() {
		return ( 'off' !== $this->get( 'native_html5' ) );
	}

	/**
	 * Check if field support multiple file upload
	 *
	 * @return bool
	 */
	public function isMultiple() {
		return ( 'on' === $this->get( 'multiple' ) );
	}

	/**
	 * Check current field is required
	 *
	 * @return bool
	 */
	public function isRequired() {
		if ( 'on' == $this->get( 'required_field' ) ) {
			return true;
		}

		// Backward compatibility
		if ( in_array( 'required', (array) $this->get( 'validation' ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Convert array to input attributes
	 *
	 * @param array $attributes
	 *
	 * @return string
	 */
	protected function arrayToAttributes( $attributes ) {
		$string = array_map( function ( $key, $value ) {
			if ( empty( $value ) && 'value' !== $key ) {
				return null;
			}
			if ( in_array( $key, array( 'required', 'checked', 'multiple' ) ) && $value ) {
				return $key;
			}

			// If boolean value
			if ( is_bool( $value ) ) {
				return sprintf( '%s="%s"', $key, $value ? 'true' : 'false' );
			}

			// If array value
			if ( is_array( $value ) ) {
				return sprintf( '%s="%s"', $key, implode( " ", $value ) );
			}

			// If string value
			return sprintf( '%s="%s"', $key, esc_attr( $value ) );

		}, array_keys( $attributes ), array_values( $attributes ) );

		return implode( ' ', array_filter( $string ) );
	}

	/**
	 * Get options
	 *
	 * @return array
	 */
	public function getOptions() {
		$options = $this->get( 'options' );

		if ( is_array( $options ) ) {
			return $options;
		}

		if ( is_string( $options ) ) {
			return array_map( 'trim', explode( PHP_EOL, $options ) );
		}

		return array();
	}

	/**
	 * Get field acceptance text
	 *
	 * @return string
	 */
	protected function getAcceptanceText() {
		return $this->get( 'acceptance_text' );
	}

	/**
	 * Check if field is checked by default
	 *
	 * @return boolean
	 */
	protected function isCheckedByDefault() {
		$value = $this->get( 'checked_by_default' );

		return in_array( $value, array( 'yes', 'on', '1', 1, true, 'true' ), true );
	}
}
