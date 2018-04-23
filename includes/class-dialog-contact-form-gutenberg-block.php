<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Dialog_Contact_Form_Gutenberg_Block' ) ) {
	/**
	 * Adds Gutenberg Forms block.
	 */
	class Dialog_Contact_Form_Gutenberg_Block {

		public static $instance = null;

		/**
		 * @return Dialog_Contact_Form_Gutenberg_Block
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Dialog_Contact_Form_Gutenberg_Block constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'gutenberg_block' ) );
			add_filter( 'template_include', array( $this, 'template_include' ) );
		}

		/**
		 * Form preview template
		 *
		 * @param $template
		 *
		 * @return string
		 */
		public function template_include( $template ) {
			if ( isset( $_GET['dcf_forms_preview'], $_GET['dcf_forms_iframe'], $_GET['form_id'] ) ) {
				wp_enqueue_script( 'jquery' );
				$template = DIALOG_CONTACT_FORM_TEMPLATES . '/public/form.php';
			}

			return $template;
		}

		/**
		 * Register gutenberg block
		 */
		public function gutenberg_block() {
			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}
			wp_register_script( 'dialog-contact-form-gutenberg-block',
				DIALOG_CONTACT_FORM_ASSETS . '/js/gutenberg-block.js',
				array( 'wp-blocks', 'wp-element' )
			);

			wp_register_style( 'dialog-contact-form-gutenberg-style',
				DIALOG_CONTACT_FORM_ASSETS . '/css/dcf-gutenberg.css',
				array( 'wp-edit-blocks' )
			);

			wp_localize_script(
				'dialog-contact-form-gutenberg-block',
				'dcf_gutenberg_block',
				self::block()
			);

			register_block_type( 'dialog-contact-form/form', array(
				'editor_script' => 'dialog-contact-form-gutenberg-block',
				'editor_style'  => 'dialog-contact-form-gutenberg-style',
			) );
		}

		/**
		 * Block dynamic value
		 *
		 * @return array
		 */
		public static function block() {
			$forms = get_posts( array(
				'posts_per_page' => - 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
				'post_status'    => 'publish',
			) );

			$_forms = array(
				array(
					'value' => '',
					'label' => '-- Select a Form --',
				)
			);
			foreach ( $forms as $form ) {
				if ( ! $form instanceof \WP_Post ) {
					continue;
				}

				$_forms[] = array(
					'value' => $form->ID,
					'label' => get_the_title( $form ),
				);
			}

			return array(
				'forms'         => $_forms,
				'site_url'      => site_url(),
				'block_title'   => __( 'Dialog Contact Form', 'dialog-contact-form' ),
				'selected_form' => __( 'Selected Form', 'dialog-contact-form' ),
			);
		}
	}
}

Dialog_Contact_Form_Gutenberg_Block::init();
