<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'DialogContactFormSMPT' ) ):

	class DialogContactFormSMPT {

		private $plugin_name = 'dcf-settings';

		/**
		 * Holds the values to be used in the fields callbacks
		 */
		private $options;

		protected static $instance = null;

		/**
		 * Main DialogContactFormSMPT Instance
		 * Ensures only one instance of DialogContactFormSMPT is loaded or can be loaded.
		 *
		 * @return DialogContactFormSMPT - Main instance
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
		}

		/**
		 * Configure PHPMailer for sending email over SMPT
		 *
		 * @param PHPMailer $phpmailer
		 */
		public function phpmailer_config( PHPMailer $phpmailer ) {

			$_smpt = get_option( '_dcf_smpt' );
			if ( ! $_smpt ) {
				return;
			}

			if ( ! isset( $_smpt['mailer'] ) ) {
				return;
			}

			if ( ! isset( $_smpt['smpt_host'], $_smpt['smpt_username'], $_smpt['smpt_password'], $_smpt['smpt_port'] ) ) {
				return;
			}

			$phpmailer->isSMTP();
			$phpmailer->SMTPAuth = true;
			$phpmailer->Host     = esc_attr( $_smpt['smpt_host'] );
			$phpmailer->Port     = absint( $_smpt['smpt_port'] );
			$phpmailer->Username = esc_attr( $_smpt['smpt_username'] );
			$phpmailer->Password = esc_attr( $_smpt['smpt_password'] );

			// Additional settings…
			if ( in_array( $_smpt['encryption'], array( 'ssl', 'tls' ) ) ) {
				$phpmailer->SMTPSecure = esc_attr( $_smpt['encryption'] );
			}

			$phpmailer->From     = esc_attr( $_smpt['smpt_from'] );
			$phpmailer->FromName = esc_attr( $_smpt['smpt_from_name'] );
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
		 * Options page callback
		 */
		public function admin_menu_callback() {
			// Set class property
			$this->options = get_option( '_dcf_smpt' );
			?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Dialog Contact Form Settings', 'dialog-contact-form' ); ?></h1>
                <form method="post" action="options.php">
					<?php
					// This prints out all hidden setting fields
					settings_fields( '_shapla_smpt_option_group' );
					do_settings_sections( '_shapla_smpt_settings_page' );
					submit_button();
					?>
                </form>
            </div>
			<?php
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
				$new_input['smpt_port'] = absint( $input['smpt_port'] );
			}

			if ( isset( $input['encryption'] ) ) {
				$new_input['encryption'] = sanitize_text_field( $input['encryption'] );
			}

			if ( isset( $input['smpt_from'] ) ) {
				$new_input['smpt_from'] = sanitize_email( $input['smpt_from'] );
			}

			if ( isset( $input['smpt_from_name'] ) ) {
				$new_input['smpt_from_name'] = sanitize_text_field( $input['smpt_from_name'] );
			}

			return $new_input;
		}

		/**
		 * Register and add settings
		 */
		public function page_init() {
			register_setting(
				'_shapla_smpt_option_group',
				'_dcf_smpt',
				array( $this, 'sanitize' )
			);

			add_settings_section(
				'setting_section_id',
				esc_html__( 'SMTP Server Settings', 'dialog-contact-form' ),
				array( $this, 'print_section_info' ),
				'_shapla_smpt_settings_page'
			);
			add_settings_section(
				'additional_section_id',
				esc_html__( 'Additional Mail Settings', 'dialog-contact-form' ),
				array( $this, 'print_section_info' ),
				'_shapla_smpt_settings_page'
			);

			add_settings_field(
				'mailer',
				esc_html__( 'Mailer', 'dialog-contact-form' ),
				array( $this, 'mailer_callback' ),
				'_shapla_smpt_settings_page',
				'setting_section_id'
			);

			add_settings_field(
				'smpt_host',
				esc_html__( 'SMPT Host', 'dialog-contact-form' ),
				array( $this, 'smpt_host_callback' ),
				'_shapla_smpt_settings_page',
				'setting_section_id'
			);

			add_settings_field(
				'smpt_username',
				esc_html__( 'SMPT Username', 'dialog-contact-form' ),
				array( $this, 'smpt_username_callback' ),
				'_shapla_smpt_settings_page',
				'setting_section_id'
			);

			add_settings_field(
				'smpt_password',
				esc_html__( 'SMPT Password', 'dialog-contact-form' ),
				array( $this, 'smpt_password_callback' ),
				'_shapla_smpt_settings_page',
				'setting_section_id'
			);

			add_settings_field(
				'smpt_port',
				esc_html__( 'SMPT Port', 'dialog-contact-form' ),
				array( $this, 'smpt_port_callback' ),
				'_shapla_smpt_settings_page',
				'setting_section_id'
			);

			add_settings_field(
				'encryption',
				esc_html__( 'Encryption', 'dialog-contact-form' ),
				array( $this, 'smpt_secure_callback' ),
				'_shapla_smpt_settings_page',
				'setting_section_id'
			);

			add_settings_field(
				'smpt_from',
				esc_html__( 'Sender Email', 'dialog-contact-form' ),
				array( $this, 'smpt_from_callback' ),
				'_shapla_smpt_settings_page',
				'additional_section_id'
			);

			add_settings_field(
				'smpt_from_name',
				esc_html__( 'Sender Name', 'dialog-contact-form' ),
				array( $this, 'smpt_from_name_callback' ),
				'_shapla_smpt_settings_page',
				'additional_section_id'
			);
		}

		public function print_section_info() {
		}

		public function mailer_callback() {
			printf(
				'<label><input name="_dcf_smpt[mailer]" value="1" %s type="checkbox"> Send all emails via SMPT</label>',
				isset( $this->options['mailer'] ) ? 'checked="checked"' : ''
			);
		}

		public function smpt_host_callback() {
			printf(
				'<input type="text" id="smpt_host" name="_dcf_smpt[smpt_host]" value="%s" />',
				isset( $this->options['smpt_host'] ) ? esc_attr( $this->options['smpt_host'] ) : ''
			);
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Specify your SMTP server hostname', 'dialog-contact-form' )
			);
		}

		public function smpt_username_callback() {
			printf(
				'<input type="text" id="smpt_username" name="_dcf_smpt[smpt_username]" value="%s" />',
				isset( $this->options['smpt_username'] ) ? esc_attr( $this->options['smpt_username'] ) : ''
			);
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Specify the username for your SMTP server', 'dialog-contact-form' )
			);
		}

		public function smpt_password_callback() {
			printf(
				'<input type="text" id="smpt_password" name="_dcf_smpt[smpt_password]" value="%s" />',
				isset( $this->options['smpt_password'] ) ? esc_attr( $this->options['smpt_password'] ) : ''
			);
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Specify the password for your SMTP server', 'dialog-contact-form' )
			);
		}

		public function smpt_port_callback() {
			printf(
				'<input type="text" id="smpt_port" name="_dcf_smpt[smpt_port]" value="%s" />',
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
					'<label><input type="radio" name="_dcf_smpt[encryption]" value="%s" %s> %s</label><br>',
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

		public function smpt_from_callback() {
			$siteurl     = get_option( 'siteurl' );
			$senderEmail = str_replace( array( 'https://', 'http://', 'www.' ), '', $siteurl );
			$senderEmail = sprintf( 'noreply@%s', $senderEmail );

			printf(
				'<input type="email" id="smpt_from" name="_dcf_smpt[smpt_from]" value="%s" />',
				isset( $this->options['smpt_from'] ) ? esc_attr( $this->options['smpt_from'] ) : $senderEmail
			);
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Specify the from email address for outgoing email.', 'dialog-contact-form' )
			);
		}

		public function smpt_from_name_callback() {
			printf(
				'<input type="text" id="smpt_from_name" name="_dcf_smpt[smpt_from_name]" value="%s" />',
				isset( $this->options['smpt_from_name'] ) ? esc_attr( $this->options['smpt_from_name'] ) : get_option( 'blogname' )
			);
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Specify the from name for outgoing email.', 'dialog-contact-form' )
			);
		}
	}

endif;

DialogContactFormSMPT::init();
