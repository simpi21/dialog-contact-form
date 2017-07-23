<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'DialogContactFormSession' ) ):

	class DialogContactFormSession {

		/**
		 * If session is not started, Start session
		 */
		public static function start() {
			if ( self::is_session_started() === false ) {
				session_start();
			}
		}

		/**
		 * Check if session is started
		 * @return bool
		 */
		protected static function is_session_started() {
			if ( php_sapi_name() !== 'cli' ) {
				if ( version_compare( phpversion(), '5.4.0', '>=' ) ) {
					return session_status() === PHP_SESSION_ACTIVE ? true : false;
				} else {
					return session_id() === '' ? false : true;
				}
			}

			return false;
		}

		/**
		 * Check if key exists in session
		 *
		 * @param $name
		 *
		 * @return bool
		 */
		public static function exists( $name ) {
			return ( isset( $_SESSION[ $name ] ) ) ? true : false;
		}

		/**
		 * Put value is session
		 *
		 * @param $name
		 * @param $value
		 *
		 * @return mixed
		 */
		public static function put( $name, $value ) {
			return $_SESSION[ $name ] = $value;
		}

		/**
		 * Get value from session
		 *
		 * @param $name
		 *
		 * @return mixed
		 */
		public static function get( $name ) {
			return $_SESSION[ $name ];
		}

		/**
		 * Delete variable from session
		 *
		 * @param $name
		 */
		public static function delete( $name ) {
			if ( self::exists( $name ) ) {
				unset( $_SESSION[ $name ] );
			}
		}

		/**
		 * Flash message from session
		 *
		 * @param $name
		 * @param string $string
		 *
		 * @return mixed
		 */
		public static function flash( $name, $string = '' ) {
			if ( self::exists( $name ) ) {
				$session = self::get( $name );
				self::delete( $name );

				return $session;
			} else {
				self::put( $name, $string );
			}
		}
	}

endif;
