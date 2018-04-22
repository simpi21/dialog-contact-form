<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Dialog_Contact_Form_Shortcode' ) ) {

	class Dialog_Contact_Form_Shortcode {

		/**
		 * @var object
		 */
		private static $instance;

		/**
		 * Main DialogContactFormShortcode Instance
		 * Ensures only one instance of DialogContactFormShortcode is loaded or can be loaded.
		 *
		 * @return Dialog_Contact_Form_Shortcode - Main instance
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Dialog_Contact_Form_Shortcode constructor.
		 */
		public function __construct() {
			add_shortcode( 'dialog_contact_form', array( $this, 'contact_form' ) );
			add_action( 'wp_footer', array( $this, 'dialog_button' ), 5 );
		}

		/**
		 * Dialog Contact Form Shortcode
		 *
		 * @param  array $atts
		 * @param  null $content
		 *
		 * @return string
		 */
		public function contact_form( $atts, $content = null ) {
			if ( empty( $atts['id'] ) ) {
				if ( current_user_can( 'manage_options' ) ) {
					return esc_html__( 'Dialog Contact form now required a form ID attribute. Please update your shortcode.', 'dialog-contact-form' );
				}

				return '';
			}

			return Dialog_Contact_Form_Form::instance( intval( $atts['id'] ) )->form();
		}

		/**
		 * Display dialog button
		 */
		public function dialog_button() {
			$options = get_dialog_contact_form_option();
			$form_id = isset( $options['dialog_form_id'] ) ? intval( $options['dialog_form_id'] ) : 0;
			$form    = Dialog_Contact_Form_Form::instance( $form_id );

			if ( $form_id < 1 ) {
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
            <div id="modal-<?php echo absint( $options['dialog_form_id'] ); ?>" class="modal">
                <div class="modal-background"></div>
				<?php echo $form->form_open( array( 'class' => 'dcf-form' ) ); ?>
                <div class="modal-card">
                    <div class="modal-card-head">
                        <p class="modal-card-title">
							<?php echo esc_html( get_the_title( $options['dialog_form_id'] ) ); ?>
                        </p>
                        <button class="modal-close" data-dismiss="modal"></button>
                    </div>
                    <div class="modal-card-body">
                        <div class="columns is-multiline">
							<?php echo $form->form_content( false ); ?>
                        </div>
                    </div>
                    <div class="modal-card-foot">
						<?php echo $form->submit_button(); ?>
                    </div>
                </div>
				<?php echo $form->form_close(); ?>
            </div>
			<?php
			$html = ob_get_contents();
			ob_end_clean();

			echo $html;
		}
	}
}

Dialog_Contact_Form_Shortcode::init();
