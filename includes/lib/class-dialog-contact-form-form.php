<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Dialog_Contact_Form_Form' ) ) {

	class Dialog_Contact_Form_Form {

		private $form_id = 0;

		private $errors = array();

		private $success_message = array();

		private $error_message = array();

		private $configuration = array();

		public function __construct( $form_id = 0 ) {
			$this->form_id         = $form_id;
			$this->errors          = isset( $GLOBALS['_dcf_errors'] ) ? $GLOBALS['_dcf_errors'] : array();
			$this->success_message = isset( $GLOBALS['_dcf_mail_sent_ok'] ) ? $GLOBALS['_dcf_mail_sent_ok'] : null;
			$this->error_message   = isset( $GLOBALS['_dcf_validation_error'] ) ? $GLOBALS['_dcf_validation_error'] : null;
			$this->configuration   = get_post_meta( $form_id, '_contact_form_config', true );
		}

		/**
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

			add_action( 'wp_footer', function () use ( $options ) {
				$hl = isset( $options['recaptcha_lang'] ) ? $options['recaptcha_lang'] : 'en';
				$hl = in_array( $hl, array_keys( dcf_google_recaptcha_lang() ) ) ? $hl : 'en';
				echo sprintf( '<script src="https://www.google.com/recaptcha/api.js?hl=%s"></script>', $hl );
			} );
		}

		/**
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
	}
}