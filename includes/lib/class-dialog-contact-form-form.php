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
		 * Get field class
		 *
		 * @param array $setting
		 * @param string $default_class
		 *
		 * @return string
		 */
		private static function get_field_class( $setting, $default_class = '' ) {
			$class = $default_class;
			if ( ! empty( $setting['field_class'] ) ) {
				$class = $setting['field_class'] . ' ' . $default_class;
			}

			return esc_attr( $class );
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
		 * Generate text field
		 *
		 * @param array $setting
		 * @param bool $echo
		 *
		 * @return string
		 */
		public function text( $setting, $echo = true ) {
			$has_error     = $this->has_field_error( $setting );
			$required_attr = $this->get_required_attribute_text( $setting );
			$placeholder   = $this->get_placeholder( $setting );
			$default_class = $has_error ? 'input is-danger' : 'input';
			$class         = self::get_field_class( $setting, $default_class );
			$valid_types   = array( 'text', 'email', 'url', 'search', 'password', 'hidden', 'date', 'time' );

			list( $id, $name, $value ) = $this->get_general_attributes( $setting );

			$html = sprintf( '<input id="%1$s" class="%2$s" name="%3$s" value="%4$s" type="%7$s" %5$s %6$s>',
				$id, $class, $name, esc_attr( $value ), $placeholder, $required_attr,
				in_array( $setting['field_type'], $valid_types ) ? $setting['field_type'] : 'text'
			);

			return self::print_html( $html, $echo );
		}

		/**
		 * Generate text field
		 *
		 * @param array $setting
		 * @param bool $echo
		 *
		 * @return string
		 */
		public function number( $setting, $echo = true ) {
			$min  = empty( $setting['number_min'] ) ? '' : sprintf( ' min="%s"', floatval( $setting['number_min'] ) );
			$max  = empty( $setting['number_max'] ) ? '' : sprintf( ' max="%s"', floatval( $setting['number_max'] ) );
			$step = empty( $setting['number_step'] ) ? '' : sprintf( ' step="%s"', floatval( $setting['number_step'] ) );

			$has_error     = $this->has_field_error( $setting );
			$required_attr = $this->get_required_attribute_text( $setting );
			$placeholder   = $this->get_placeholder( $setting );
			$default_class = $has_error ? 'input is-danger' : 'input';
			$class         = self::get_field_class( $setting, $default_class );

			list( $id, $name, $value ) = $this->get_general_attributes( $setting );

			$html = sprintf(
				'<input id="%1$s" class="%2$s" name="%3$s" value="%4$s" type="number" %5$s %6$s %7$s %8$s %9$s>',
				$id, $class, $name, esc_attr( $value ), $placeholder, $min, $max, $step, $required_attr
			);

			return self::print_html( $html, $echo );
		}

		/**
		 * Generate radio field
		 *
		 * @param array $setting
		 * @param bool $echo
		 *
		 * @return string
		 */
		public function radio( array $setting, $echo = true ) {
			$required_attr = $this->get_required_attribute_text( $setting );
			$class         = self::get_field_class( $setting, 'radio' );

			list( $id, $name, $value ) = $this->get_general_attributes( $setting );
			$options = empty( $setting['options'] ) ? array() : explode( PHP_EOL, $setting['options'] );

			$html = '';
			foreach ( $options as $option ) {
				$option  = trim( $option );
				$checked = ( $value == $option ) ? ' checked' : '';
				$id      = $id . '-' . sanitize_title_with_dashes( $option );
				$html    .= sprintf(
					'<label for="%6$s" class="%5$s"><input type="radio" id="%6$s" name="%1$s" value="%2$s"%3$s%4$s> %2$s</label>',
					$name, esc_attr( $option ), $checked, $required_attr, $class, $id
				);
			}

			return self::print_html( $html, $echo );
		}

		/**
		 * Generate select field
		 *
		 * @param array $setting
		 * @param bool $echo
		 *
		 * @return string
		 */
		public function select( $setting, $echo = true ) {
			$has_error     = $this->has_field_error( $setting );
			$required_attr = $this->get_required_attribute_text( $setting );
			$placeholder   = $this->get_placeholder( $setting );
			$default_class = $has_error ? 'select is-danger' : 'select';
			$class         = self::get_field_class( $setting, $default_class );
			$options       = empty( $setting['options'] ) ? array() : explode( PHP_EOL, $setting['options'] );

			list( $id, $name, $value ) = $this->get_general_attributes( $setting );

			$html = sprintf( '<div class="%s">', $class );
			$html .= sprintf( '<select id="%1$s" name="%2$s" %3$s>', $id, $name, $required_attr );
			if ( ! empty( $setting['placeholder'] ) ) {
				$html .= sprintf( '<option value="">%s</option>', esc_attr( $setting['placeholder'] ) );
			}
			foreach ( $options as $option ) {
				$option   = trim( $option );
				$selected = ( $value == $option ) ? ' selected' : '';
				$html     .= sprintf( '<option value="%1$s" %2$s>%1$s</option>', esc_attr( $option ), $selected );
			}
			$html .= '</select>';
			$html .= '</div>';

			return self::print_html( $html, $echo );
		}

		/**
		 * Generate checkbox field
		 *
		 * @param array $setting
		 * @param bool $echo
		 *
		 * @return string
		 */
		public function checkbox( $setting, $echo = true ) {
			$options       = empty( $setting['options'] ) ? array() : explode( PHP_EOL, $setting['options'] );
			$required_attr = $this->get_required_attribute_text( $setting );

			list( $id, $name, $value ) = $this->get_general_attributes( $setting );

			$html = '';
			foreach ( $options as $option ) {
				$option  = trim( $option );
				$checked = ( $value == $option ) ? ' checked' : '';
				$html    .= sprintf(
					'<label class="checkbox"><input type="checkbox" name="%1$s" value="%2$s" %3$s %4$s> %2$s</label>',
					$name, esc_attr( $option ), $checked, $required_attr
				);
			}

			return self::print_html( $html, $echo );
		}

		/**
		 * Generate textarea field
		 *
		 * @param array $setting
		 * @param bool $echo
		 *
		 * @return string
		 */
		public function textarea( $setting, $echo = true ) {
			$has_error     = $this->has_field_error( $setting );
			$required_attr = $this->get_required_attribute_text( $setting );
			$placeholder   = $this->get_placeholder( $setting );
			$default_class = $has_error ? 'textarea is-danger' : 'textarea';
			$class         = self::get_field_class( $setting, $default_class );

			list( $id, $name, $value ) = $this->get_general_attributes( $setting );

			$html = sprintf( '<textarea id="%1$s" class="%2$s" name="%3$s" %5$s %5$s >%4$s</textarea>',
				$id, $class, $name, esc_textarea( $value ), $placeholder, $required_attr
			);

			return self::print_html( $html, $echo );
		}

		/**
		 * Generate file field
		 *
		 * @param array $setting
		 * @param bool $echo
		 *
		 * @return string
		 */
		public function file( $setting, $echo = true ) {
			$has_error     = $this->has_field_error( $setting );
			$required_attr = $this->get_required_attribute_text( $setting );
			$placeholder   = $this->get_placeholder( $setting );
			$default_class = $has_error ? 'file is-danger' : 'file';
			$class         = self::get_field_class( $setting, $default_class );

			list( $id, $name, $value ) = $this->get_general_attributes( $setting );

			$accept   = '';
			$multiple = '';
			$html     = sprintf( '<input id="%1$s" class="%2$s" name="%3$s" type="file" %4$s %5$s %6$s>',
				$id, $class, $name, $multiple, $accept, $required_attr
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

			echo '<div class="field column is-12">';
			printf(
				'<div class="g-recaptcha" data-sitekey="%1$s" data-theme="%2$s"></div>',
				esc_attr( $options['recaptcha_site_key'] ),
				esc_attr( $options['recaptcha_theme'] )
			);
			echo '<input type="hidden" name="dcf_recaptcha">';

			// Show error message if any
			if ( isset( $this->errors['dcf_recaptcha'][0] ) ) {
				echo '<span class="help is-danger">' . esc_attr( $this->errors['dcf_recaptcha'][0] ) . '</span>';
			}

			echo '</div>';
		}

		/**
		 * Generate placeholder for current field
		 *
		 * @param $setting
		 *
		 * @return string
		 */
		private function get_placeholder( $setting ) {
			$placeholder = '';
			if ( $this->configuration['labelPosition'] != 'label' ) {
				$placeholder = empty( $setting['placeholder'] ) ? '' : sprintf( ' placeholder="%s"', esc_attr( $setting['placeholder'] ) );
			}

			return $placeholder;
		}

		/**
		 * Generate field id, name and value
		 *
		 * @param array $setting
		 *
		 * @return array
		 */
		private function get_general_attributes( $setting ) {
			$id    = sanitize_title_with_dashes( $setting['field_id'] . '-' . $this->form_id );
			$name  = sanitize_title_with_dashes( $setting['field_name'] );
			$value = empty( $_POST[ $setting['field_name'] ] ) ? null : $_POST[ $setting['field_name'] ];

			return array( $id, $name, $value );
		}

		/**
		 * Check if there is any error for current field
		 *
		 * @param array $setting
		 *
		 * @return bool
		 */
		private function has_field_error( $setting ) {
			$has_error = false;
			if ( isset( $this->errors[ $setting['field_name'] ][0] ) ) {
				$has_error = true;
			}

			return $has_error;
		}

		/**
		 * Get required attribute text
		 *
		 * @param array $setting
		 *
		 * @return string
		 */
		private function get_required_attribute_text( $setting ) {
			$required_attr = '';
			if ( in_array( 'required', $setting['validation'] ) ) {
				$required_attr = ' required';
			}

			return $required_attr;
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
		 * Contact form
		 *
		 * @return string|null
		 */
		public function form() {
			// If there is no field, exist
			if ( ! ( is_array( $this->fields ) && count( $this->fields ) > 0 ) ) {
				return null;
			}
			ob_start();
			?>
            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="dcf-form columns is-multiline"
                  method="POST" accept-charset="UTF-8" enctype="multipart/form-data" novalidate>
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
					echo sprintf( '<div class="field column %s">', $field['field_width'] );

					$this->label( $field );

					echo '<div class="control">';

					$field_type = isset( $field['field_type'] ) ? esc_attr( $field['field_type'] ) : 'text';

					if ( method_exists( $this, $field_type ) ) {
						$this->$field_type( $field );
					} else {
						$this->text( $field );
					}

					// Show error message if any
					if ( isset( $this->errors[ $field['field_name'] ][0] ) ) {
						echo '<span class="help is-danger">' . esc_attr( $this->errors[ $field['field_name'] ][0] ) . '</span>';
					}

					echo '</div>';
					echo '</div>';
				}

				// If Google reCAPTCHA, add here
				$this->reCAPTCHA( $this->options );

				// Submit button
				printf( '<div class="field column is-12"><p class="%s"><button type="submit" class="button dcf-submit">%s</button></p></div>',
					( isset( $this->configuration['btnAlign'] ) && $this->configuration['btnAlign'] == 'right' ) ? 'control level-right' : 'control level-left',
					esc_attr( $this->configuration['btnLabel'] )
				);
				?>
            </form>
			<?php
			$html = ob_get_contents();
			ob_end_clean();

			return $html;
		}
	}
}