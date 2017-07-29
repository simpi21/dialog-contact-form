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

			add_action( 'wp_footer', array( $this, 'dcf_button' ) );
		}

		/**
		 * Filterable Portfolio shortcode.
		 *
		 * @param  array $atts
		 * @param  null $content
		 *
		 * @return string
		 */
		public function contact_form( $atts, $content = null ) {
			extract( shortcode_atts( array( 'id' => 0 ), $atts ) );

			if ( ! $id ) {
				return '';
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

		public function dcf_button() {
			$default_option = dcf_default_options();
			$options        = get_option( 'dialog_contact_form' );
			$options        = wp_parse_args( $options, $default_option );

			if ( ! is_numeric( $options['dialog_form_id'] ) ) {
				return;
			}

			$config = get_post_meta( $options['dialog_form_id'], '_contact_form_config', true );

			printf(
				'<button class="button dcf-footer-btn" style="background-color: %2$s;color: %3$s" data-toggle="modal" data-target="#modal-%4$s">%1$s</button>',
				$options['dialog_button_text'],
				$options['dialog_button_background'],
				$options['dialog_button_color'],
				$options['dialog_form_id']
			);

			$_content  = sprintf( '[dialog_contact_form id="%s"]', $options['dialog_form_id'] );
			$shortcode = do_shortcode( $_content );

			ob_start();
			require DIALOG_CONTACT_FORM_TEMPLATES . '/dialog-form.php';
			$html = ob_get_contents();
			ob_end_clean();

			echo $html;

			if ( isset( $config['recaptcha'] ) && $config['recaptcha'] == 'yes' ) {
				echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
			}
		}
	}

endif;

DialogContactFormShortcode::init();
