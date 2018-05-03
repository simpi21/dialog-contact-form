<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Dialog_Contact_Form_Form' ) ) {

	class Dialog_Contact_Form_Form {

		/**
		 * Form ID
		 * @var int
		 */
		private $form_id = 0;

		/**
		 * List of errors after validation
		 * @var array
		 */
		private $errors = array();

		/**
		 * Success message after form submission
		 * @var string|null
		 */
		private $success_message;

		/**
		 * Error message after form submission
		 * @var string|null
		 */
		private $error_message;

		/**
		 * Configuration for current form
		 * @var array
		 */
		private $configuration = array();

		/**
		 * List of fields for current form
		 * @var array
		 */
		private $fields = array();

		/**
		 * Plugin global options for all forms
		 * @var array
		 */
		private $options = array();

		/**
		 * @var object|null
		 */
		private static $instance = null;

		/**
		 * @param int $form_id
		 *
		 * @return Dialog_Contact_Form_Form
		 */
		public static function instance( $form_id = 0 ) {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self( $form_id );
			}

			return self::$instance;
		}

		/**
		 * Dialog_Contact_Form_Form constructor.
		 *
		 * @param int $form_id
		 */
		public function __construct( $form_id = 0 ) {
			$this->options         = get_dialog_contact_form_option();
			$this->errors          = isset( $GLOBALS['_dcf_errors'] ) ? $GLOBALS['_dcf_errors'] : array();
			$this->success_message = isset( $GLOBALS['_dcf_mail_sent_ok'] ) ? $GLOBALS['_dcf_mail_sent_ok'] : null;
			$this->error_message   = isset( $GLOBALS['_dcf_validation_error'] ) ? $GLOBALS['_dcf_validation_error'] : null;

			if ( $form_id ) {
				$this->form_id       = $form_id;
				$this->configuration = get_post_meta( $form_id, '_contact_form_config', true );
				$this->fields        = get_post_meta( $form_id, '_contact_form_fields', true );
			}
		}

		/**
		 * Print field html
		 *
		 * @param string $html
		 * @param bool $echo
		 *
		 * @return string|void
		 */
		private static function print_html( $html, $echo = true ) {
			if ( ! $echo ) {
				return $html;
			}

			echo $html;
		}

		/**
		 * Generate text field
		 *
		 * @param array $setting
		 * @param bool $echo
		 *
		 * @return string
		 */
		public function label( $setting, $echo = true ) {
			if ( 'hidden' == $setting['field_type'] ) {
				return '';
			}

			if ( 'placeholder' == $this->configuration['labelPosition'] ) {
				return '';
			}

			$required_abbr = '';
			if ( in_array( 'required', $setting['validation'] ) ) {
				$required_abbr = sprintf( '&nbsp;<abbr class="dcf-required" title="%s">*</abbr>',
					esc_html__( 'Required', 'dialog-contact-form' )
				);
			}
			$id   = sanitize_title_with_dashes( $setting['field_id'] . '-' . $this->form_id );
			$html = sprintf( '<label for="%1$s" class="label">%2$s%3$s</label>',
				$id, esc_attr( $setting['field_title'] ), $required_abbr
			);

			return self::print_html( $html, $echo );
		}

		/**
		 * Generate Google reCAPTCHA field
		 *
		 * @param array $options
		 *
		 * @return string
		 */
		public function reCAPTCHA( $options ) {
			if ( ! ( isset( $this->configuration['recaptcha'] ) && $this->configuration['recaptcha'] == 'yes' ) ) {
				return;
			}

			if ( empty( $options['recaptcha_site_key'] ) || empty( $options['recaptcha_secret_key'] ) ) {
				return;
			}

			wp_enqueue_script( 'dialog-contact-form-recaptcha' );

			echo '<div class="dcf-column is-12">';
			echo '<div class="dcf-field">';
			printf(
				'<div class="g-recaptcha" data-sitekey="%1$s" data-theme="%2$s"></div>',
				esc_attr( $options['recaptcha_site_key'] ),
				esc_attr( $options['recaptcha_theme'] )
			);
			echo '<div class="dcf-control">';
			echo '<input type="hidden" name="dcf_recaptcha">';

			// Show error message if any
			if ( isset( $this->errors['dcf_recaptcha'][0] ) ) {
				echo '<span class="dcf-error-message">' . esc_attr( $this->errors['dcf_recaptcha'][0] ) . '</span>';
			}

			echo '</div></div></div>' . PHP_EOL;
		}

		/**
		 * Get success message
		 *
		 * @return null|string
		 */
		public function get_success_message() {
			if ( empty( $this->success_message ) ) {
				return null;
			}

			return '<p>' . $this->success_message . '</p>';
		}

		/**
		 * Get error message
		 *
		 * @return null|string
		 */
		public function get_error_message() {
			if ( empty( $this->error_message ) ) {
				return null;
			}

			return '<p>' . $this->error_message . '</p>';
		}

		/**
		 * Form Opening tag
		 *
		 * @param array $options
		 *
		 * @return string
		 */
		public function form_open( $options = array() ) {
			$action = isset( $options['action'] ) ? $options['action'] : $_SERVER['REQUEST_URI'];
			$class  = isset( $options['class'] ) ? $options['class'] : 'dcf-form dcf-columns';

			return '<form action="' . $action . '" class="' . $class . '" method="POST" accept-charset="UTF-8" enctype="multipart/form-data" novalidate>';
		}

		/**
		 * Form Closing tag
		 *
		 * @return string
		 */
		public function form_close() {
			return '</form>';
		}

		/**
		 * Form content
		 *
		 * @param bool $submit_button
		 *
		 * @return string|null
		 */
		public function form_content( $submit_button = true ) {
			// If there is no field, exist
			if ( ! ( is_array( $this->fields ) && count( $this->fields ) > 0 ) ) {
				return null;
			}
			ob_start();
			?>
            <div class="dcf-response">
                <div class="dcf-success">
					<?php echo $this->get_success_message(); ?>
                </div>
                <div class="dcf-error">
					<?php $this->get_error_message(); ?>
                </div>
            </div>
			<?php wp_nonce_field( '_dcf_submit_form', '_dcf_nonce' ); ?>
            <input type="hidden" name="_user_form_id" value="<?php echo $this->form_id; ?>">

			<?php
			foreach ( $this->fields as $field ) {
				echo sprintf( '<div class="dcf-column %s">', $field['field_width'] );
				echo '<div class="dcf-field">';

				$this->label( $field );

				echo '<div class="dcf-control">';

				$field_type = isset( $field['field_type'] ) ? esc_attr( $field['field_type'] ) : 'text';

				// Load Field Class if exists
				$class_name = '\\DialogContactForm\\Fields\\' . ucfirst( $field_type );
				if ( method_exists( $class_name, 'render' ) ) {
					$field_class = new $class_name;
					$field_class->setFormId( $this->form_id );
					$field_class->setField( $field );
					echo $field_class->render();
				}

				do_action( 'dialog_contact_form_render_field', $this->form_id, $field, $field_type );

				// Show error message if any
				if ( isset( $this->errors[ $field['field_name'] ][0] ) ) {
					echo '<div class="dcf-error-message">' . esc_attr( $this->errors[ $field['field_name'] ][0] ) . '</div>';
				}

				echo '</div>';
				echo '</div>';
				echo '</div>' . PHP_EOL;
			}

			// If Google reCAPTCHA, add here
			$this->reCAPTCHA( $this->options );

			// Submit button
			if ( $submit_button ) {
				echo '<div class="dcf-column is-12"><div class="dcf-field"><div class="dcf-control">';
				echo $this->submit_button();
				echo '</div></div></div>' . PHP_EOL;
			}

			$html = ob_get_contents();
			ob_end_clean();

			return $html;
		}

		/**
		 * Contact form
		 *
		 * @return string
		 */
		public function form() {
			$html = $this->form_open();
			$html .= $this->form_content();
			$html .= $this->form_close();

			return $html;
		}

		/**
		 * Submit button
		 *
		 * @return string
		 */
		public function submit_button() {
			$html = sprintf( '<div class="%s"><button type="submit" class="button dcf-submit">%s</button></div>',
				( isset( $this->configuration['btnAlign'] ) && $this->configuration['btnAlign'] == 'right' ) ? 'dcf-level-right' : 'dcf-level-left',
				esc_attr( $this->configuration['btnLabel'] )
			);

			return $html;
		}
	}
}
