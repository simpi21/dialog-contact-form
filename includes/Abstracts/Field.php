<?php

namespace DialogContactForm\Abstracts;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Field implements \ArrayAccess {

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
		return sanitize_title_with_dashes( $this->get( 'field_id' ) . '-' . $this->form_id );
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
		$number_max = $this->get( 'number_max' );

		return ! empty( $number_max ) ? floatval( $this->field['number_max'] ) : '';
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
	 * Does this field have a given key?
	 *
	 * @param string $key The data key
	 *
	 * @return bool
	 */
	public function has( $key ) {
		return ! empty( $this->field[ $key ] );
	}

	/**
	 * Set field item
	 *
	 * @param string $key The data key
	 * @param mixed $value The data value
	 */
	public function set( $key, $value ) {
		if ( is_null( $key ) ) {
			$this->field[] = $value;
		} else {
			$this->field[ $key ] = $value;
		}
	}

	/**
	 * Get field item for key
	 *
	 * @param string $key The data key
	 * @param mixed $default The default value to return if data key does not exist
	 *
	 * @return mixed The key's value, or the default value
	 */
	public function get( $key, $default = null ) {
		return $this->has( $key ) ? $this->field[ $key ] : $default;
	}

	/**
	 * Remove item from field
	 *
	 * @param string $key The data key
	 */
	public function remove( $key ) {
		if ( $this->has( $key ) ) {
			unset( $this->field[ $key ] );
		}
	}

	/********************************************************************************
	 * ArrayAccess interface
	 *******************************************************************************/

	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset An offset to check for.
	 *
	 * @return boolean true on success or false on failure.
	 * @since 5.0.0
	 */
	public function offsetExists( $offset ) {
		return $this->has( $offset );
	}

	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset The offset to retrieve.
	 *
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet( $offset, $value ) {
		$this->set( $offset, $value );
	}

	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset The offset to unset.
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset( $offset ) {
		$this->remove( $offset );
	}
}
