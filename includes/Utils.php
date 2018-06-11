<?php

namespace DialogContactForm;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Utils {

	/**
	 * Return the values from a single column in the input array
	 *
	 * @param  array $array A multi-dimensional array (record set) from which to pull a column of values.
	 * @param  mixed $column The column of values to return. This value may be
	 * the integer key of the column you wish to retrieve, or it may be
	 * the string key name for an associative array. It may also be
	 * NULL to return complete arrays (useful together with index_key to reindex the array).
	 * @param  mixed $index_key The column to use as the index/keys for the returned array.
	 * This value may be the integer key of the column, or it may be the string key name.
	 *
	 * @return array Returns an array of values representing a single column from the input array.
	 */
	public static function array_column( array $array, $column, $index_key = null ) {
		if ( function_exists( 'array_column' ) ) {
			return array_column( $array, $column, $index_key );
		}

		// For php < 5.5
		$arr = array_map( function ( $d ) use ( $column, $index_key ) {
			if ( ! isset( $d[ $column ] ) ) {
				return null;
			}
			if ( $index_key !== null ) {
				return array( $d[ $index_key ] => $d[ $column ] );
			}

			return $d[ $column ];
		}, $array );

		if ( $index_key !== null ) {
			$tmp = array();
			foreach ( $arr as $ar ) {
				$tmp[ key( $ar ) ] = current( $ar );
			}
			$arr = $tmp;
		}

		return $arr;
	}

	/**
	 * Available field types
	 *
	 * @return array
	 */
	public static function field_types() {
		$fieldType = array(
			'text'       => array(
				'label' => esc_html__( 'Text', 'dialog-contact-form' ),
				'icon'  => 'fas fa-text-width',
			),
			'textarea'   => array(
				'label' => esc_html__( 'Textarea', 'dialog-contact-form' ),
				'icon'  => 'fas fa-paragraph',
			),
			'acceptance' => array(
				'label' => esc_html__( 'Acceptance', 'dialog-contact-form' ),
				'icon'  => 'far fa-check-square',
			),
			'checkbox'   => array(
				'label' => esc_html__( 'Checkbox', 'dialog-contact-form' ),
				'icon'  => 'fas fa-list',
			),
			'email'      => array(
				'label' => esc_html__( 'Email', 'dialog-contact-form' ),
				'icon'  => 'far fa-envelope',
			),
			'password'   => array(
				'label' => esc_html__( 'Password', 'dialog-contact-form' ),
				'icon'  => 'fas fa-key',
			),
			'number'     => array(
				'label' => esc_html__( 'Number', 'dialog-contact-form' ),
				'icon'  => 'fas fa-sort-numeric-up',
			),
			'hidden'     => array(
				'label' => esc_html__( 'Hidden', 'dialog-contact-form' ),
				'icon'  => 'far fa-eye-slash',
			),
			'date'       => array(
				'label' => esc_html__( 'Date', 'dialog-contact-form' ),
				'icon'  => 'far fa-calendar-alt',
			),
			'time'       => array(
				'label' => esc_html__( 'Time', 'dialog-contact-form' ),
				'icon'  => 'far fa-clock',
			),
			'url'        => array(
				'label' => esc_html__( 'URL', 'dialog-contact-form' ),
				'icon'  => 'fas fa-link',
			),
			'ip'         => array(
				'label' => esc_html__( 'IP Address', 'dialog-contact-form' ),
				'icon'  => 'fas fa-mouse-pointer',
			),
			'radio'      => array(
				'label' => esc_html__( 'Radio', 'dialog-contact-form' ),
				'icon'  => 'far fa-dot-circle',
			),
			'select'     => array(
				'label' => esc_html__( 'Select', 'dialog-contact-form' ),
				'icon'  => 'fas fa-angle-down',
			),
			'file'       => array(
				'label' => esc_html__( 'File', 'dialog-contact-form' ),
				'icon'  => 'fas fa-upload',
			),
			'html'       => array(
				'label' => esc_html__( 'HTML', 'dialog-contact-form' ),
				'icon'  => 'fas fa-code',
			),
		);

		return apply_filters( 'dialog_contact_form_field_types', $fieldType );
	}

	/**
	 * Get user IP address
	 *
	 * @return string
	 */
	public static function get_remote_ip() {
		$server_ip_keys = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		foreach ( $server_ip_keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) && filter_var( $_SERVER[ $key ], FILTER_VALIDATE_IP ) ) {
				return $_SERVER[ $key ];
			}
		}

		// Fallback local ip.
		return '127.0.0.1';
	}

	/**
	 * Get user browser name
	 *
	 * @return string
	 */
	public static function get_user_agent() {
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 );
		}

		return '';
	}
}