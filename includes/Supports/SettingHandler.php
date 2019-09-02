<?php

namespace DialogContactForm\Supports;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Very simple WordPress Settings API wrapper class
 *
 * WordPress Option Page Wrapper class that implements WordPress Settings API and
 * give you easy way to create multi tabs admin menu and
 * add setting fields with build in validation.
 *
 * @author  Sayful Islam <sayful.islam001@gmail.com>
 * @link    https://sayfulislam.com
 */
class SettingHandler {
	/**
	 * Settings options array
	 */
	private $options = array();

	/**
	 * Settings menu fields array
	 */
	private $menu_fields = array();

	/**
	 * Settings fields array
	 */
	private $fields = array();

	/**
	 * Settings tabs array
	 */
	private $panels = array();

	/**
	 * @var array
	 */
	private $sections = array();

	/**
	 * The instance of the class
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * @return SettingHandler
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add new admin menu
	 *
	 * This method is accessible outside the class for creating menu
	 *
	 * @param array $menu_fields
	 *
	 * @return \WP_Error|SettingHandler
	 */
	public function add_menu( array $menu_fields ) {
		if ( ! isset( $menu_fields['page_title'], $menu_fields['menu_title'], $menu_fields['menu_slug'] ) ) {
			return new \WP_Error( 'field_not_set', 'Required key is not set properly for creating menu.' );
		}

		$this->menu_fields = $menu_fields;

		return $this;
	}

	/**
	 * Add setting page tab
	 *
	 * This method is accessible outside the class for creating page tab
	 *
	 * @param array $panel
	 *
	 * @return \WP_Error|$this
	 */
	public function add_panel( array $panel ) {
		if ( ! isset( $panel['id'], $panel['title'] ) ) {
			return new \WP_Error( 'field_not_set', 'Required key is not set properly for creating tab.' );
		}

		$this->panels[] = $panel;

		return $this;
	}

	/**
	 * Add Setting page section
	 *
	 * @param array $section
	 *
	 * @return $this
	 */
	public function add_section( array $section ) {

		$this->sections[] = $section;

		return $this;
	}

	/**
	 * Get sections for current panel
	 *
	 * @param string $panel
	 *
	 * @return array
	 */
	public function getSectionsByPanel( $panel = '' ) {
		if ( empty( $panel ) ) {
			return $this->getSections();
		}

		$current_panel = array();
		foreach ( $this->getSections() as $section ) {
			if ( $section['panel'] == $panel ) {
				$current_panel[] = $section;
			}
		}

		return $current_panel;
	}

	/**
	 * @param string $section
	 *
	 * @return mixed
	 */
	public function getFieldsBySection( $section = '' ) {
		if ( empty( $section ) ) {
			return $this->getFields();
		}

		$current_field = array();
		foreach ( $this->getFields() as $field ) {
			if ( $field['section'] == $section ) {
				$current_field[ $field['id'] ] = $field;
			}
		}

		return $current_field;
	}

	/**
	 * Filter settings fields by page tab
	 *
	 * @param string $current_tab
	 *
	 * @return array
	 */
	public function getFieldsByPanel( $current_tab = null ) {

		if ( ! $current_tab ) {
			$panels      = $this->getPanels();
			$current_tab = isset ( $_GET['tab'] ) ? $_GET['tab'] : $panels[0]['id'];
		}

		$newarray = array();
		$sections = $this->getSectionsByPanel( $current_tab );

		foreach ( $sections as $section ) {
			$_section = $this->getFieldsBySection( $section['id'] );
			$newarray = array_merge( $newarray, $_section );
		}

		return $newarray;
	}

	/**
	 * Add new settings field
	 *
	 * This method is accessible outside the class for creating settings field
	 *
	 * @param array $field
	 *
	 * @return \WP_Error|$this
	 */
	public function add_field( array $field ) {
		if ( ! isset( $field['id'], $field['name'] ) ) {
			return new \WP_Error( 'field_not_set', 'Required key is not set properly for creating tab.' );
		}

		$this->fields[ $field['id'] ] = $field;

		return $this;
	}

	/**
	 * @param array $input
	 *
	 * @return array
	 */
	public function sanitize_options( array $input ) {
		$output_array = array();
		$fields       = $this->getFields();
		$options      = (array) $this->get_options();
		foreach ( $fields as $field ) {
			$key     = isset( $field['id'] ) ? $field['id'] : null;
			$default = isset( $field['std'] ) ? $field['std'] : null;
			$type    = isset( $field['type'] ) ? $field['type'] : 'text';
			$value   = isset( $input[ $field['id'] ] ) ? $input[ $field['id'] ] : $options[ $field['id'] ];

			if ( isset( $field['sanitize_callback'] ) && is_callable( $field['sanitize_callback'] ) ) {
				$output_array[ $key ] = call_user_func( $field['sanitize_callback'], $value );
				continue;
			}

			if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
				$output_array[ $key ] = in_array( $value, array_keys( $field['options'] ) ) ? $value : $default;
				continue;
			}

			if ( 'checkbox' == $type ) {
				$output_array[ $key ] = in_array( $input, array( 'on', 'yes', '1', 1, 'true', true ) ) ? 1 : 0;
				continue;
			}

			$rule                 = empty( $field['validate'] ) ? $field['type'] : $field['validate'];
			$output_array[ $key ] = $this->sanitize( $value, $rule );
		}

		return $output_array;
	}

	/**
	 * @param array $options
	 */
	public function update( array $options ) {
		update_option( $this->menu_fields['option_name'], $options );
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 *
	 * @return array
	 */
	public function sanitize_callback( array $input ) {
		$output_array = array();
		$fields       = $this->getFields();
		$options      = (array) get_option( $this->menu_fields['option_name'] );

		if ( empty( $options ) ) {
			$options = (array) $this->get_options();
		}

		$panels = $this->getPanels();
		if ( count( $panels ) > 0 ) {
			parse_str( $_POST['_wp_http_referer'], $referrer );
			$tab    = isset( $referrer['tab'] ) ? $referrer['tab'] : $panels[0]['id'];
			$fields = $this->getFieldsByPanel( $tab );
		}

		// Loop through each setting being saved and
		// pass it through a sanitization filter
		foreach ( $input as $key => $value ) {
			$field = isset( $fields[ $key ] ) ? $fields[ $key ] : array();
			if ( empty( $field['id'] ) ) {
				continue;
			}

			$default = isset( $field['std'] ) ? $field['std'] : null;
			$type    = isset( $field['type'] ) ? $field['type'] : 'text';

			if ( isset( $field['sanitize_callback'] ) && is_callable( $field['sanitize_callback'] ) ) {
				$output_array[ $key ] = call_user_func( $field['sanitize_callback'], $value );
				continue;
			}

			if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
				$output_array[ $key ] = in_array( $value, array_keys( $field['options'] ) ) ? $value : $default;
				continue;
			}

			if ( 'checkbox' == $type ) {
				$output_array[ $key ] = in_array( $input, array( 'on', 'yes', '1', 1, 'true', true ) ) ? 1 : 0;
				continue;
			}

			$rule                 = empty( $field['validate'] ) ? $field['type'] : $field['validate'];
			$output_array[ $key ] = $this->sanitize( $value, $rule );
		}

		return array_filter( array_merge( $options, $output_array ) );
	}

	/**
	 * Validate the option's value
	 *
	 * @param mixed $input
	 * @param string $validation_rule
	 *
	 * @return mixed
	 */
	private function sanitize( $input, $validation_rule = 'text' ) {
		switch ( $validation_rule ) {
			case 'text':
				return sanitize_text_field( $input );
				break;

			case 'number':
				return is_numeric( $input ) ? intval( $input ) : intval( $input );
				break;

			case 'url':
				return esc_url_raw( trim( $input ) );
				break;

			case 'email':
				return sanitize_email( $input );
				break;

			case 'date':
				return $this->is_date( $input ) ? date( 'F d, Y', strtotime( $input ) ) : '';
				break;

			case 'textarea':
				return _sanitize_text_fields( $input, true );
				break;

			case 'inlinehtml':
				return wp_filter_kses( force_balance_tags( $input ) );
				break;

			case 'linebreaks':
				return wp_strip_all_tags( $input );
				break;

			case 'wp_editor':
				return wp_kses_post( $input );
				break;

			default:
				return sanitize_text_field( $input );
				break;
		}
	}

	/**
	 * Get options parsed with default value
	 * @return array
	 */
	public function get_options() {
		$defaults = array();

		foreach ( $this->getFields() as $value ) {
			$std_value                = ( isset( $value['std'] ) ) ? $value['std'] : '';
			$defaults[ $value['id'] ] = $std_value;
		}

		$options = wp_parse_args( get_option( $this->menu_fields['option_name'] ), $defaults );

		return $this->options = $options;
	}

	/**
	 * @return mixed
	 */
	public function getPanels() {
		return $this->panels;
	}

	/**
	 * @return array
	 */
	public function getSections() {
		$sections = array();
		foreach ( $this->sections as $section ) {
			$sections[] = wp_parse_args( $section, array(
				'id'          => '',
				'panel'       => '',
				'title'       => '',
				'description' => '',
				'priority'    => 200,
			) );
		}

		// Sort by priority
		usort( $sections, function ( $a, $b ) {
			return $a['priority'] - $b['priority'];
		} );

		return $sections;
	}

	/**
	 * @return mixed
	 */
	public function getFields() {
		$fields = array();

		foreach ( $this->fields as $field ) {
			if ( ! isset( $field['priority'] ) ) {
				$field['priority'] = 200;
			}
			$fields[] = $field;
		}

		$fields = apply_filters( 'dialog_contact_form/settings/fields', $fields );

		// Sort by priority
		usort( $fields, function ( $a, $b ) {
			return $a['priority'] - $b['priority'];
		} );

		return $fields;
	}

	/**
	 * @param mixed $panels
	 */
	public function setPanels( $panels ) {
		$this->panels = $panels;
	}

	/**
	 * @param array $sections
	 */
	public function setSections( $sections ) {
		$this->sections = $sections;
	}

	/**
	 * @param mixed $fields
	 */
	public function setFields( $fields ) {
		$this->fields = $fields;
	}

	/**
	 * Check if the given input is a valid date.
	 *
	 * @param mixed $value
	 *
	 * @return boolean
	 */
	private function is_date( $value ) {
		if ( $value instanceof \DateTime ) {
			return true;
		}

		if ( strtotime( $value ) === false ) {
			return false;
		}

		$date = date_parse( $value );

		return checkdate( $date['month'], $date['day'], $date['year'] );
	}
}
