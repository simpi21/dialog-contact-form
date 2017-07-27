<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'DialogContactFormShortcode' ) ):

	class DialogContactFormShortcode {

		protected static $instance = null;

		/**
		 * Main DialogContactFormShortcode Instance
		 * Ensures only one instance of DialogContactFormShortcode is loaded or can be loaded.
		 *
		 * @return DialogContactFormShortcode - Main instance
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * DialogContactFormShortcode constructor.
		 */
		public function __construct() {
			add_shortcode( 'contact-form', array( $this, 'contact_form' ) );
			add_shortcode( 'dialog_contact_form', array( $this, 'contact_form' ) );
		}

		/**
		 * Filterable Portfolio shortcode.
		 *
		 * @param  array $atts
		 * @param  null $content
		 *
		 * @return string|void
		 */
		public function contact_form( $atts, $content = null ) {
			extract( shortcode_atts( array( 'id' => 0 ), $atts ) );

			if ( ! $id ) {
				return;
			}
			$fields = get_post_meta( $id, '_contact_form_fields', true );
			$config = get_post_meta( $id, '_contact_form_config', true );

			$default_options = dcf_default_options();
			$options         = get_option( 'dialog_contact_form' );
			$_options        = wp_parse_args( $options, $default_options );

			ob_start();
			require DIALOG_CONTACT_FORM_TEMPLATES . '/contact-form.php';
			$html = ob_get_contents();
			ob_end_clean();

			return $html;
		}
	}

endif;

DialogContactFormShortcode::init();
