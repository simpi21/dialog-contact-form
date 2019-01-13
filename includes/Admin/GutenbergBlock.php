<?php

namespace DialogContactForm\Admin;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds Gutenberg Forms block.
 */
class GutenbergBlock {

	public static $instance = null;

	/**
	 * @return GutenbergBlock
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			add_action( 'init', array( self::$instance, 'gutenberg_block' ) );
		}

		return self::$instance;
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
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' )
		);

		wp_register_style( 'dialog-contact-form-gutenberg-style',
			DIALOG_CONTACT_FORM_ASSETS . '/css/gutenberg-block.css',
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

		$_forms = array();
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
			'block_logo'    => site_url(),
			'block_title'   => __( 'Dialog Contact Form', 'dialog-contact-form' ),
			'selected_form' => __( 'Selected Form', 'dialog-contact-form' ),
		);
	}
}
