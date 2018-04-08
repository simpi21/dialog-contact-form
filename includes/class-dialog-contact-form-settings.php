<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'Dialog_Contact_Form_Settings' ) ) {

	class Dialog_Contact_Form_Settings {

		private $plugin_name = 'dcf-settings';

		/**
		 * Holds the values to be used in the fields callbacks
		 */
		private $options;

		/**
		 * Default options
		 *
		 * @var array
		 */
		private $default_options = array();

		/**
		 * @var object
		 */
		protected static $instance;

		/**
		 * Main DialogContactFormSettings Instance
		 * Ensures only one instance of DialogContactFormSettings is loaded or can be loaded.
		 *
		 * @return Dialog_Contact_Form_Settings - Main instance
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Start Up
		 */
		public function __construct() {
			add_action( 'phpmailer_init', array( $this, 'phpmailer_config' ) );

			if ( is_admin() ) {
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
				add_action( 'admin_init', array( $this, 'page_init' ) );
			}

			$this->default_options = dcf_default_options();
		}

		/**
		 * Configure PHPMailer for sending email over SMPT
		 *
		 * @param PHPMailer $mailer
		 */
		public function phpmailer_config( PHPMailer $mailer ) {

			$_smpt = get_option( 'dialog_contact_form' );
			if ( ! $_smpt ) {
				return;
			}

			if ( ! isset( $_smpt['mailer'] ) ) {
				return;
			}

			if ( ! isset( $_smpt['smpt_host'], $_smpt['smpt_username'], $_smpt['smpt_password'], $_smpt['smpt_port'] ) ) {
				return;
			}

			$mailer->isSMTP();
			$mailer->SMTPAuth = true;
			$mailer->Host     = esc_attr( $_smpt['smpt_host'] );
			$mailer->Port     = absint( $_smpt['smpt_port'] );
			$mailer->Username = esc_attr( $_smpt['smpt_username'] );
			$mailer->Password = esc_attr( $_smpt['smpt_password'] );

			// Additional settings…
			if ( in_array( $_smpt['encryption'], array( 'ssl', 'tls' ) ) ) {
				$mailer->SMTPSecure = esc_attr( $_smpt['encryption'] );
			}
		}

		/**
		 * Add options page
		 */
		public function admin_menu() {
			add_submenu_page(
				'edit.php?post_type=dialog-contact-form',
				esc_html__( 'Settings', 'dialog-contact-form' ),
				esc_html__( 'Settings', 'dialog-contact-form' ),
				'manage_options',
				$this->plugin_name,
				array( $this, 'admin_menu_callback' )
			);
		}

		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys
		 *
		 * @return array
		 */
		public function sanitize( $input ) {
			$new_input = array();
			if ( isset( $input['mailer'] ) ) {
				$new_input['mailer'] = absint( $input['mailer'] );
			}
			if ( isset( $input['smpt_host'] ) ) {
				$new_input['smpt_host'] = sanitize_text_field( $input['smpt_host'] );
			}

			if ( isset( $input['smpt_username'] ) ) {
				$new_input['smpt_username'] = sanitize_text_field( $input['smpt_username'] );
			}

			if ( isset( $input['smpt_password'] ) ) {
				$new_input['smpt_password'] = sanitize_text_field( $input['smpt_password'] );
			}

			if ( isset( $input['smpt_port'] ) ) {
				$smpt_port              = empty( $input['smpt_port'] ) ? '' : absint( $input['smpt_port'] );
				$new_input['smpt_port'] = $smpt_port;
			}

			if ( isset( $input['encryption'] ) ) {
				$new_input['encryption'] = sanitize_text_field( $input['encryption'] );
			}

			if ( isset( $input['recaptcha_site_key'] ) ) {
				$new_input['recaptcha_site_key'] = sanitize_text_field( $input['recaptcha_site_key'] );
			}

			if ( isset( $input['recaptcha_secret_key'] ) ) {
				$new_input['recaptcha_secret_key'] = sanitize_text_field( $input['recaptcha_secret_key'] );
			}

			if ( isset( $input['recaptcha_theme'] ) ) {
				$new_input['recaptcha_theme'] = sanitize_text_field( $input['recaptcha_theme'] );
			}

			if ( isset( $input['recaptcha_lang'] ) ) {
				$new_input['recaptcha_lang'] = sanitize_text_field( $input['recaptcha_lang'] );
			}

			if ( isset( $input['spam_message'] ) ) {
				$new_input['spam_message'] = sanitize_text_field( $input['spam_message'] );
			}

			if ( isset( $input['invalid_recaptcha'] ) ) {
				$new_input['invalid_recaptcha'] = sanitize_text_field( $input['invalid_recaptcha'] );
			}

			if ( isset( $input['dialog_button_text'] ) ) {
				$new_input['dialog_button_text'] = sanitize_text_field( $input['dialog_button_text'] );
			}

			if ( isset( $input['dialog_button_background'] ) ) {
				$new_input['dialog_button_background'] = sanitize_hex_color( $input['dialog_button_background'] );
			}

			if ( isset( $input['dialog_button_color'] ) ) {
				$new_input['dialog_button_color'] = sanitize_hex_color( $input['dialog_button_color'] );
			}

			if ( isset( $input['dialog_form_id'] ) ) {
				$new_input['dialog_form_id'] = absint( $input['dialog_form_id'] );
			}

			return $new_input;
		}

		/**
		 * Options page callback
		 */
		public function admin_menu_callback() {
			// Set class property
			$_options      = get_option( 'dialog_contact_form' );
			$this->options = wp_parse_args( $_options, $this->default_options );
			?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Dialog Contact Form Settings', 'dialog-contact-form' ); ?></h1>
                <form method="post" action="options.php">
					<?php
					// This prints out all hidden setting fields
					settings_fields( '_dcf_option_group' );
					do_settings_sections( '_dcf_settings_page' );
					submit_button();
					?>
                </form>
            </div>
			<?php
		}

		/**
		 * Register and add settings
		 */
		public function page_init() {
			register_setting(
				'_dcf_option_group',
				'dialog_contact_form',
				array( $this, 'sanitize' )
			);

			add_settings_section(
				'dcf_smpt_server_section',
				esc_html__( 'SMTP Server Settings', 'dialog-contact-form' ),
				array( $this, 'print_section_info' ),
				'_dcf_settings_page'
			);
			add_settings_section(
				'dcf_grecaptcha_section',
				esc_html__( 'Google reCAPTCHA', 'dialog-contact-form' ),
				array( $this, 'print_grecaptcha_section_info' ),
				'_dcf_settings_page'
			);
			add_settings_section(
				'dcf_message_section',
				esc_html__( 'Validation Messages', 'dialog-contact-form' ),
				array( $this, 'print_message_section_info' ),
				'_dcf_settings_page'
			);
			add_settings_section(
				'dcf_dialog_section',
				esc_html__( 'Dialog/Modal', 'dialog-contact-form' ),
				array( $this, 'print_dialog_section_info' ),
				'_dcf_settings_page'
			);

			add_settings_field(
				'mailer',
				esc_html__( 'Mailer', 'dialog-contact-form' ),
				array( $this, 'mailer_callback' ),
				'_dcf_settings_page',
				'dcf_smpt_server_section'
			);

			add_settings_field(
				'smpt_host',
				esc_html__( 'SMPT Host', 'dialog-contact-form' ),
				array( $this, 'smpt_host_callback' ),
				'_dcf_settings_page',
				'dcf_smpt_server_section'
			);

			add_settings_field(
				'smpt_username',
				esc_html__( 'SMPT Username', 'dialog-contact-form' ),
				array( $this, 'smpt_username_callback' ),
				'_dcf_settings_page',
				'dcf_smpt_server_section'
			);

			add_settings_field(
				'smpt_password',
				esc_html__( 'SMPT Password', 'dialog-contact-form' ),
				array( $this, 'smpt_password_callback' ),
				'_dcf_settings_page',
				'dcf_smpt_server_section'
			);

			add_settings_field(
				'smpt_port',
				esc_html__( 'SMPT Port', 'dialog-contact-form' ),
				array( $this, 'smpt_port_callback' ),
				'_dcf_settings_page',
				'dcf_smpt_server_section'
			);

			add_settings_field(
				'encryption',
				esc_html__( 'Encryption', 'dialog-contact-form' ),
				array( $this, 'smpt_secure_callback' ),
				'_dcf_settings_page',
				'dcf_smpt_server_section'
			);

			add_settings_field(
				'recaptcha_site_key',
				esc_html__( 'Site key', 'dialog-contact-form' ),
				array( $this, 'recaptcha_site_key_callback' ),
				'_dcf_settings_page',
				'dcf_grecaptcha_section'
			);

			add_settings_field(
				'recaptcha_secret_key',
				esc_html__( 'Secret key', 'dialog-contact-form' ),
				array( $this, 'recaptcha_secret_key_callback' ),
				'_dcf_settings_page',
				'dcf_grecaptcha_section'
			);

			add_settings_field(
				'recaptcha_lang',
				esc_html__( 'Language', 'dialog-contact-form' ),
				array( $this, 'recaptcha_lang_callback' ),
				'_dcf_settings_page',
				'dcf_grecaptcha_section'
			);

			add_settings_field(
				'recaptcha_theme',
				esc_html__( 'Theme', 'dialog-contact-form' ),
				array( $this, 'recaptcha_theme_callback' ),
				'_dcf_settings_page',
				'dcf_grecaptcha_section'
			);

			add_settings_field(
				'spam_message',
				esc_html__( 'Submission filtered as spam', 'dialog-contact-form' ),
				array( $this, 'spam_message_callback' ),
				'_dcf_settings_page',
				'dcf_message_section'
			);

			add_settings_field(
				'invalid_recaptcha',
				esc_html__( 'invalid reCAPTCHA', 'dialog-contact-form' ),
				array( $this, 'invalid_recaptcha_callback' ),
				'_dcf_settings_page',
				'dcf_message_section'
			);

			add_settings_field(
				'dialog_button_text',
				esc_html__( 'Dialog button text', 'dialog-contact-form' ),
				array( $this, 'dialog_button_text_callback' ),
				'_dcf_settings_page',
				'dcf_dialog_section'
			);
			add_settings_field(
				'dialog_button_background',
				esc_html__( 'Dialog button background', 'dialog-contact-form' ),
				array( $this, 'dialog_button_background_callback' ),
				'_dcf_settings_page',
				'dcf_dialog_section'
			);
			add_settings_field(
				'dialog_button_color',
				esc_html__( 'Dialog button color', 'dialog-contact-form' ),
				array( $this, 'dialog_button_color_callback' ),
				'_dcf_settings_page',
				'dcf_dialog_section'
			);
			add_settings_field(
				'dialog_form_id',
				esc_html__( 'Choose Form', 'dialog-contact-form' ),
				array( $this, 'dialog_form_id_callback' ),
				'_dcf_settings_page',
				'dcf_dialog_section'
			);
		}

		public function print_section_info() {
		}

		public function print_dialog_section_info() {
			printf(
				'<p>%s</p>',
				esc_html__( 'Configure fixed dialog/modal button at your site footer.', 'dialog-contact-form' )
			);
		}

		public function print_grecaptcha_section_info() {
			printf(
				'<p>%1$s</p><p>%2$s</p>',
				esc_html__( 'reCAPTCHA is a free service from Google to protect your website from spam and abuse.', 'dialog-contact-form' ),
				esc_html__( 'To use reCAPTCHA, you need to install an API key pair.', 'dialog-contact-form' )
			);
		}

		public function print_message_section_info() {
			printf(
				'<p>%s</p>',
				esc_html__( 'Define default validation message. This message can be overwrite from each form.', 'dialog-contact-form' )
			);
		}

		public function mailer_callback() {
			printf(
				'<label><input name="dialog_contact_form[mailer]" value="1" %s type="checkbox"> Send all emails via SMPT</label>',
				isset( $this->options['mailer'] ) && $this->options['mailer'] == 1 ? 'checked="checked"' : ''
			);
		}

		public function smpt_host_callback() {
			printf(
				'<input type="text" id="smpt_host" name="dialog_contact_form[smpt_host]" value="%s" />',
				isset( $this->options['smpt_host'] ) ? esc_attr( $this->options['smpt_host'] ) : ''
			);
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Specify your SMTP server hostname', 'dialog-contact-form' )
			);
		}

		public function smpt_username_callback() {
			printf(
				'<input type="text" id="smpt_username" name="dialog_contact_form[smpt_username]" value="%s" />',
				isset( $this->options['smpt_username'] ) ? esc_attr( $this->options['smpt_username'] ) : ''
			);
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Specify the username for your SMTP server', 'dialog-contact-form' )
			);
		}

		public function smpt_password_callback() {
			printf(
				'<input type="text" id="smpt_password" name="dialog_contact_form[smpt_password]" value="%s" />',
				isset( $this->options['smpt_password'] ) ? esc_attr( $this->options['smpt_password'] ) : ''
			);
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Specify the password for your SMTP server', 'dialog-contact-form' )
			);
		}

		public function smpt_port_callback() {
			printf(
				'<input type="text" id="smpt_port" name="dialog_contact_form[smpt_port]" value="%s" />',
				isset( $this->options['smpt_port'] ) ? absint( $this->options['smpt_port'] ) : ''
			);
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Specify SMTP port', 'dialog-contact-form' )
			);
		}

		public function smpt_secure_callback() {
			$encryption = array(
				'no'  => esc_html__( 'No encryption', 'dialog-contact-form' ),
				'tls' => esc_html__( 'Use TLS encryption', 'dialog-contact-form' ),
				'ssl' => esc_html__( 'Use SSL encryption', 'dialog-contact-form' ),
			);
			$_val       = isset( $this->options['encryption'] ) ? esc_attr( $this->options['encryption'] ) : 'no';
			foreach ( $encryption as $key => $value ) {
				$checked = ( $_val == $key ) ? 'checked="checked"' : '';
				printf(
					'<label><input type="radio" name="dialog_contact_form[encryption]" value="%s" %s> %s</label><br>',
					esc_attr( $key ),
					$checked,
					esc_attr( $value )
				);
			}
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'if you have SSL/TLS encryption available for that hostname, select it here', 'dialog-contact-form' )
			);
		}

		public function recaptcha_site_key_callback() {
			printf(
				'<input type="text" id="recaptcha_site_key" class="regular-text" name="dialog_contact_form[recaptcha_site_key]" value="%s" />',
				isset( $this->options['recaptcha_site_key'] ) ? esc_attr( $this->options['recaptcha_site_key'] ) : ''
			);
		}

		public function recaptcha_secret_key_callback() {
			printf(
				'<input type="text" id="recaptcha_secret_key" class="regular-text" name="dialog_contact_form[recaptcha_secret_key]" value="%s" />',
				isset( $this->options['recaptcha_secret_key'] ) ? esc_attr( $this->options['recaptcha_secret_key'] ) : ''
			);
		}

		public function recaptcha_theme_callback() {
			$themes = array(
				'light' => esc_html__( 'Light', 'dialog-contact-form' ),
				'dark'  => esc_html__( 'Dark', 'dialog-contact-form' ),
			);
			$_val   = isset( $this->options['recaptcha_theme'] ) ? esc_attr( $this->options['recaptcha_theme'] ) : 'light';
			foreach ( $themes as $key => $value ) {
				$checked = ( $_val == $key ) ? 'checked="checked"' : '';
				printf(
					'<label><input type="radio" name="dialog_contact_form[recaptcha_theme]" value="%s" %s> %s</label><br>',
					esc_attr( $key ),
					$checked,
					esc_attr( $value )
				);
			}
		}

		public function recaptcha_lang_callback() {
			$languages = dcf_google_recaptcha_lang();
			$_val      = isset( $this->options['recaptcha_lang'] ) ? esc_attr( $this->options['recaptcha_lang'] ) : 'en';
			echo '<select name="dialog_contact_form[recaptcha_lang]" class="regular-text">';
			foreach ( $languages as $key => $value ) {
				$selected = ( $_val == $key ) ? 'selected' : '';
				echo sprintf( '<option value="%1$s" %3$s>%2$s</option>',
					esc_attr( $key ),
					esc_attr( $value ),
					$selected
				);
			}
			echo '</select>';
		}

		public function spam_message_callback() {
			printf(
				'<input type="text" id="spam_message" class="regular-text" name="dialog_contact_form[spam_message]" value="%s" />',
				isset( $this->options['spam_message'] ) ? esc_attr( $this->options['spam_message'] ) : ''
			);
		}

		public function invalid_recaptcha_callback() {
			printf(
				'<input type="text" id="invalid_recaptcha" class="regular-text" name="dialog_contact_form[invalid_recaptcha]" value="%s" />',
				isset( $this->options['invalid_recaptcha'] ) ? esc_attr( $this->options['invalid_recaptcha'] ) : ''
			);
		}

		public function dialog_button_text_callback() {
			printf(
				'<input type="text" id="dialog_button_text" class="regular-text" name="dialog_contact_form[dialog_button_text]" value="%s" />',
				isset( $this->options['dialog_button_text'] ) ? esc_attr( $this->options['dialog_button_text'] ) : ''
			);
		}

		public function dialog_button_background_callback() {
			printf(
				'<input type="text" id="dialog_button_background" class="dcf-colorpicker" name="dialog_contact_form[dialog_button_background]" value="%s" />',
				isset( $this->options['dialog_button_background'] ) ? esc_attr( $this->options['dialog_button_background'] ) : ''
			);
		}

		public function dialog_button_color_callback() {
			printf(
				'<input type="text" id="dialog_button_color" class="dcf-colorpicker" name="dialog_contact_form[dialog_button_color]" value="%s" />',
				isset( $this->options['dialog_button_color'] ) ? esc_attr( $this->options['dialog_button_color'] ) : ''
			);
		}

		public function dialog_form_id_callback() {
			$contact_forms = get_posts( array(
				'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
				'posts_per_page' => - 1,
				'post_status'    => 'publish'
			) );

			if ( count( $contact_forms ) < 1 ) {
				printf(
					'<p>%s</p>',
					esc_html__( 'Yor did not add any form yet. Please add a form first.' )
				);
			}

			$contact_forms = array_map( function ( WP_Post $form ) {
				return array(
					'id'    => $form->ID,
					'title' => $form->post_title,
				);
			}, $contact_forms );

			$_val = isset( $this->options['dialog_form_id'] ) ? absint( $this->options['dialog_form_id'] ) : '';

			echo '<select name="dialog_contact_form[dialog_form_id]" class="regular-text">';
			echo '<option value="">' . esc_html__( 'Choose Form' ) . '</option>';
			foreach ( $contact_forms as $_form ) {
				$selected = ( $_val == $_form['id'] ) ? 'selected' : '';
				printf( '<option value="%s" %s>%s</option>', absint( $_form['id'] ), $selected, esc_attr( $_form['title'] ) );
			}
			echo '</select>';
		}
	}
}

Dialog_Contact_Form_Settings::init();
