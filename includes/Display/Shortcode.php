<?php

namespace DialogContactForm\Display;

use DialogContactForm\Supports\FormBuilder;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcode {

	/**
	 * @var object
	 */
	private static $instance;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return self - Main instance
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Shortcode constructor.
	 */
	public function __construct() {
		add_shortcode( 'dialog_contact_form', array( $this, 'contact_form' ) );
		add_action( 'wp_footer', array( $this, 'dialog_button' ), 5 );
	}

	/**
	 * Dialog Contact Form Shortcode
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function contact_form( $attributes ) {
		$start = microtime( true );

		if ( empty( $attributes['id'] ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				return esc_html__( 'Dialog Contact form now required a form ID attribute. Please update your shortcode.',
					'dialog-contact-form' );
			}

			return '';
		}

		$form_id = intval( $attributes['id'] );
		$form    = new FormBuilder( $form_id );

		if ( ! $form->getForm()->isValid() ) {
			if ( current_user_can( 'manage_options' ) ) {
				return esc_html__( 'No form found with id #' . $form_id, 'dialog-contact-form' );
			}

			return '';
		}

		$html = $form->form();

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$finish = round( microtime( true ) - $start, 6 );
			$html   .= '<div style="display: none;">Form generated in ' . $finish . ' microseconds</div>';
		}

		return $html;
	}

	/**
	 * Display dialog button
	 */
	public function dialog_button() {
		$start   = microtime( true );
		$options = Utils::get_option();
		$form_id = isset( $options['dialog_form_id'] ) ? intval( $options['dialog_form_id'] ) : 0;

		if ( $form_id < 1 ) {
			return;
		}

		$form_builder = new FormBuilder( $form_id );
		$form         = $form_builder->getForm();

		// Check if form is valid
		if ( ! $form->isValid() ) {
			return;
		}

		printf(
			'<button class="button dcf-footer-btn" style="background-color: %2$s;color: %3$s" data-toggle="modal" data-target="#modal-%4$s">%1$s</button>',
			$form->getGlobalOption( 'dialog_button_text' ),
			$form->getGlobalOption( 'dialog_button_background' ),
			$form->getGlobalOption( 'dialog_button_color' ),
			$form->getGlobalOption( 'dialog_form_id' )
		);

		ob_start();
		?>
        <div id="modal-<?php echo $form->getId(); ?>" class="modal">
            <div class="modal-background"></div>
			<?php echo $form_builder->formOpen( array( 'class' => 'dcf-form' ) ); ?>
            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">
						<?php echo esc_html( $form->getTitle() ); ?>
                    </p>
                    <button class="modal-close" data-dismiss="modal"></button>
                </div>
                <div class="modal-card-body">
                    <div class="dcf-columns">
						<?php echo $form_builder->formContent( false ); ?>
                    </div>
                </div>
                <div class="modal-card-foot">
					<?php echo $form_builder->submitButton(); ?>
                </div>
            </div>
			<?php echo $form_builder->formClose(); ?>
        </div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$finish = round( microtime( true ) - $start, 6 );
			$html   .= '<div style="display: none;">Form generated in ' . $finish . ' microseconds</div>';
		}

		echo $html;
	}
}
