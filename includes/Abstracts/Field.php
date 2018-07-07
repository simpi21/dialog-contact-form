<?php

namespace DialogContactForm\Abstracts;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Field {

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
	protected $admin_icon = '<i class="fas fa-text-width"></i>';

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
	 * Should this field value available only for administration purpose
	 *
	 * @var bool
	 */
	protected $admin_only = false;

	/**
	 * Current form ID
	 *
	 * @var int
	 */
	protected $form_id = 0;

	/**
	 * Current field configuration
	 *
	 * @var array
	 */
	protected $field = array();

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
		return $this->field;
	}

	/**
	 * Set field configuration
	 *
	 * @param array $field
	 */
	public function setField( $field ) {
		$this->field = $field;
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
	protected function getId() {
		return sanitize_title_with_dashes( $this->field['field_id'] . '-' . $this->form_id );
	}

	/**
	 * Get field class attribute
	 *
	 * @param string $default
	 *
	 * @return string
	 */
	protected function getClass( $default = '' ) {
		if ( ! empty( $default ) ) {
			$this->input_class = $default;
		}
		$class = $this->input_class;
		if ( ! empty( $this->field['field_class'] ) ) {
			$class = $this->field['field_class'];
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
	protected function hasError() {
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
	protected function getName() {
		return sanitize_title_with_dashes( $this->field['field_name'] );
	}

	/**
	 * Generate placeholder for current field
	 *
	 * @return string
	 */
	protected function getPlaceholder() {
		if ( empty( $this->field['placeholder'] ) ) {
			return '';
		}

		return esc_attr( $this->field['placeholder'] );
	}

	/**
	 * Get rows attribute
	 *
	 * @return string
	 */
	protected function getRows() {
		if ( isset( $this->field['rows'] ) && is_numeric( $this->field['rows'] ) ) {
			return intval( $this->field['rows'] );
		}

		return '';
	}

	/**
	 * Generate autocomplete for current field
	 *
	 * @return string
	 */
	protected function getAutocomplete() {
		if ( empty( $this->field['autocomplete'] ) ) {
			return '';
		}

		return esc_attr( $this->field['autocomplete'] );
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function getValue() {
		if ( isset( $_POST[ $this->field['field_name'] ] ) ) {
			return esc_attr( $_POST[ $this->field['field_name'] ] );
		}

		if ( ! empty( $this->field['field_value'] ) ) {
			return esc_attr( $this->field['field_value'] );
		}

		return null;
	}

	/**
	 * Get accept for file field
	 *
	 * @return string
	 */
	protected function getAccept() {
		if ( 'file' !== $this->getType() ) {
			return '';
		}

		$mimes              = array();
		$allowed_mime_types = get_allowed_mime_types();

		$file_types = $this->field['allowed_file_types'] ? $this->field['allowed_file_types'] : array();
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
	protected function getMax() {
		if ( empty( $this->field['number_max'] ) ) {
			return '';
		}

		return floatval( $this->field['number_max'] );
	}

	/**
	 * Get min attribute
	 *
	 * @return string
	 */
	protected function getMin() {
		if ( empty( $this->field['number_min'] ) ) {
			return '';
		}

		return floatval( $this->field['number_min'] );
	}

	/**
	 * Get step attribute
	 *
	 * @return string
	 */
	protected function getStep() {
		if ( empty( $this->field['number_step'] ) ) {
			return '';
		}

		return floatval( $this->field['number_step'] );
	}

	/**
	 * Get max date
	 *
	 * @return string
	 */
	protected function getMaxDate() {
		return '';
	}

	/**
	 * Get min date
	 *
	 * @return string
	 */
	protected function getMinDate() {
		return '';
	}

	/**
	 * Check if field support multiple file upload
	 *
	 * @return bool
	 */
	protected function isMultiple() {
		return ( isset( $this->field['multiple'] ) && 'on' === $this->field['multiple'] );
	}

	/**
	 * Check current field is required
	 *
	 * @return bool
	 */
	public function isRequired() {
		if ( empty( $this->field['required_field'] ) ) {
			return false;
		}

		if ( 'on' == $this->field['required_field'] ) {
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
	protected function getOptions() {
		if ( is_array( $this->field['options'] ) ) {
			return $this->field['options'];
		}

		if ( is_string( $this->field['options'] ) ) {
			$options = explode( PHP_EOL, $this->field['options'] );

			return array_map( 'trim', $options );
		}

		return array();
	}
}
