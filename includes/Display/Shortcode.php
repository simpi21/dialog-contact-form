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
		if ( empty( $attributes['id'] ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				return esc_html__( 'Dialog Contact form now required a form ID attribute. Please update your shortcode.',
					'dialog-contact-form' );
			}

			return '';
		}

		return FormBuilder::init( intval( $attributes['id'] ) )->form();
	}

	/**
	 * Display dialog button
	 */
	public function dialog_button() {
		$options = Utils::get_option();
		$form_id = isset( $options['dialog_form_id'] ) ? intval( $options['dialog_form_id'] ) : 0;
		$form    = FormBuilder::init( $form_id );

		if ( $form_id < 1 ) {
			return;
		}

		// Check if form is valid
		if ( ! $form->isValidForm() ) {
			return;
		}

		printf(
			'<button class="button dcf-footer-btn" style="background-color: %2$s;color: %3$s" data-toggle="modal" data-target="#modal-%4$s">%1$s</button>',
			$options['dialog_button_text'],
			$options['dialog_button_background'],
			$options['dialog_button_color'],
			$options['dialog_form_id']
		);

		ob_start();
		?>
        <div id="modal-<?php echo $form_id; ?>" class="modal">
            <div class="modal-background"></div>
			<?php echo $form->formOpen( array( 'class' => 'dcf-form' ) ); ?>
            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">
						<?php echo esc_html( get_the_title( $form_id ) ); ?>
                    </p>
                    <button class="modal-close" data-dismiss="modal"></button>
                </div>
                <div class="modal-card-body">
                    <div class="dcf-columns">
						<?php echo $form->formContent( false ); ?>
                    </div>
                </div>
                <div class="modal-card-foot">
					<?php echo $form->submitButton(); ?>
                </div>
            </div>
			<?php echo $form->formClose(); ?>
        </div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();

		echo $html;
	}
}
