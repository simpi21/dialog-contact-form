<?php
/**
 * Plugin Name: Dialog Contact Form
 * Plugin URI: http://wordpress.org/plugins/dialog-contact-form/
 * Description: Just another WordPress contact form plugin. Simple but flexible.
 * Version: 2.2.1
 * Author: Sayful Islam
 * Author URI: https://sayfulislam.com
 * Requires at least: 4.4
 * Tested up to: 4.9
 * Text Domain: dialog-contact-form
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Dialog_Contact_Form' ) ) {

	final class Dialog_Contact_Form {

		/**
		 * @var object
		 */
		private static $instance;

		/**
		 * Plugin name slug
		 *
		 * @var string
		 */
		private $plugin_name = 'dialog-contact-form';

		/**
		 * Plugin custom post type
		 *
		 * @var string
		 */
		private $post_type = 'dialog-contact-form';

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		private $version = '2.2.1';

		/**
		 * Holds various class instances
		 *
		 * @var array
		 */
		private $container = array();


		/**
		 * Minimum PHP version required
		 *
		 * @var string
		 */
		private $min_php = '5.3';

		/**
		 * @return Dialog_Contact_Form
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * DialogContactForm constructor.
		 */
		public function __construct() {

			// define constants
			$this->define_constants();

			if ( ! $this->is_supported_php() ) {
				register_activation_hook( __FILE__, array( $this, 'auto_deactivate' ) );
				add_action( 'admin_notices', array( $this, 'php_version_notice' ) );

				return;
			}

			// Include Classes
			$this->include_classes();

			// include files
			$this->include_files();

			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivate' ) );

			// initialize the classes
			add_action( 'plugins_loaded', array( $this, 'init_classes' ) );
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 30 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'admin_footer', array( $this, 'form_template' ), 0 );
			add_action( 'phpmailer_init', array( $this, 'phpmailer_config' ) );

			do_action( 'dialog_contact_form_init', $this );
		}

		/**
		 * Magic getter to bypass referencing plugin.
		 *
		 * @param string $property
		 *
		 * @return mixed
		 */
		public function __get( $property ) {
			if ( array_key_exists( $property, $this->container ) ) {
				return $this->container[ $property ];
			}

			return $this->{$property};
		}

		/**
		 * Define plugin constants
		 */
		private function define_constants() {
			$this->define( 'DIALOG_CONTACT_FORM', $this->plugin_name );
			$this->define( 'DIALOG_CONTACT_FORM_POST_TYPE', $this->post_type );
			$this->define( 'DIALOG_CONTACT_FORM_VERSION', $this->version );
			$this->define( 'DIALOG_CONTACT_FORM_FILE', __FILE__ );
			$this->define( 'DIALOG_CONTACT_FORM_PATH', dirname( DIALOG_CONTACT_FORM_FILE ) );
			$this->define( 'DIALOG_CONTACT_FORM_INCLUDES', DIALOG_CONTACT_FORM_PATH . '/includes' );
			$this->define( 'DIALOG_CONTACT_FORM_TEMPLATES', DIALOG_CONTACT_FORM_PATH . '/templates' );
			$this->define( 'DIALOG_CONTACT_FORM_URL', plugins_url( '', DIALOG_CONTACT_FORM_FILE ) );
			$this->define( 'DIALOG_CONTACT_FORM_ASSETS', DIALOG_CONTACT_FORM_URL . '/assets' );
			$this->define( 'DIALOG_CONTACT_FORM_UPLOAD_DIR', 'dcf-attachments' );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param string $name Constant name.
		 * @param string|bool $value Constant value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Includes plugin files
		 */
		private function include_files() {
			include_once DIALOG_CONTACT_FORM_INCLUDES . '/functions.php';
		}

		/**
		 * Include classes
		 */
		private function include_classes() {
			spl_autoload_register( function ( $class ) {

				if ( class_exists( $class ) ) {
					return;
				}

				// project-specific namespace prefix
				$prefix = 'DialogContactForm\\';

				// base directory for the namespace prefix
				$base_dir = DIALOG_CONTACT_FORM_INCLUDES . DIRECTORY_SEPARATOR;

				// does the class use the namespace prefix?
				$len = strlen( $prefix );
				if ( strncmp( $prefix, $class, $len ) !== 0 ) {
					// no, move to the next registered autoloader
					return;
				}

				// get the relative class name
				$relative_class = substr( $class, $len );

				// replace the namespace prefix with the base directory, replace namespace
				// separators with directory separators in the relative class name, append
				// with .php
				$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

				// if the file exists, require it
				if ( file_exists( $file ) ) {
					require_once $file;
				}
			} );
		}

		/**
		 * Instantiate the required classes
		 *
		 * @return void
		 */
		public function init_classes() {

			if ( $this->is_request( 'admin' ) ) {
				$this->container['admin']    = \DialogContactForm\Admin::init();
				$this->container['entries']  = \DialogContactForm\Entries\EntryManager::init();
				$this->container['settings'] = \DialogContactForm\Settings::init();
			}

			$this->container['submission'] = \DialogContactForm\Submission::init();
			$this->container['shortcode']  = \DialogContactForm\Shortcode::init();
			$this->container['gutenblock'] = \DialogContactForm\GutenbergBlock::init();
		}

		/**
		 * Load plugin textdomain
		 */
		public function load_plugin_textdomain() {
			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'dialog-contact-form' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'dialog-contact-form', $locale );

			// Setup paths to current locale file
			$mofile_global = WP_LANG_DIR . '/dialog-contact-form/' . $mofile;

			// Look in global /wp-content/languages/dialog-contact-form folder
			if ( file_exists( $mofile_global ) ) {
				load_textdomain( $this->plugin_name, $mofile_global );
			}
		}

		/**
		 * Load admin scripts
		 *
		 * @param $hook
		 */
		public function admin_scripts( $hook ) {
			global $post_type;
			if ( ( $post_type != DIALOG_CONTACT_FORM_POST_TYPE ) && ( 'dialog-contact-form_page_dcf-settings' != $hook ) ) {
				return;
			}

			$suffix = ( defined( "SCRIPT_DEBUG" ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_style( $this->plugin_name . '-admin',
				DIALOG_CONTACT_FORM_ASSETS . '/css/admin.css',
				array( 'wp-color-picker' ), DIALOG_CONTACT_FORM_VERSION, 'all' );

			wp_enqueue_script( 'wp-color-picker-alpha',
				DIALOG_CONTACT_FORM_ASSETS . '/lib/wp-color-picker-alpha/wp-color-picker-alpha' . $suffix . '.js',
				array( 'wp-color-picker' ), '2.1.3', true );

			wp_enqueue_script( $this->plugin_name . '-admin',
				DIALOG_CONTACT_FORM_ASSETS . '/js/admin' . $suffix . '.js',
				array(
					'jquery',
					'jquery-ui-tabs',
					'jquery-ui-sortable',
					'jquery-ui-accordion',
					'wp-color-picker-alpha'
				),
				DIALOG_CONTACT_FORM_VERSION, true );
		}

		/**
		 * Load plugin front-end scripts
		 */
		public function frontend_scripts() {
			global $is_IE;

			$suffix = ( defined( "SCRIPT_DEBUG" ) && SCRIPT_DEBUG ) ? '' : '.min';

			$enabled_style = get_dialog_contact_form_option( 'default_style', 'enable' );
			$hl            = get_dialog_contact_form_option( 'recaptcha_lang', 'en' );
			$hl            = in_array( $hl, array_keys( dcf_google_recaptcha_lang() ) ) ? $hl : 'en';
			$captcha_url   = add_query_arg( array( 'hl' => $hl ), 'https://www.google.com/recaptcha/api.js' );

			if ( 'disable' != $enabled_style ) {
				wp_enqueue_style( $this->plugin_name,
					DIALOG_CONTACT_FORM_ASSETS . '/css/style.css',
					array(), DIALOG_CONTACT_FORM_VERSION, 'all' );
			}

			// Polyfill for IE
			if ( $is_IE ) {
				wp_enqueue_script( $this->plugin_name . '-polyfill',
					DIALOG_CONTACT_FORM_ASSETS . '/js/polyfill' . $suffix . '.js',
					array(), null, false );
			}

			wp_enqueue_script( $this->plugin_name,
				DIALOG_CONTACT_FORM_ASSETS . '/js/form' . $suffix . '.js',
				array(), DIALOG_CONTACT_FORM_VERSION, true );

			wp_register_script( 'dialog-contact-form-recaptcha', $captcha_url, '', null, true );

			wp_localize_script( $this->plugin_name, 'DialogContactForm', $this->localize_script() );
		}

		/**
		 * Load field template on admin
		 */
		public function form_template() {
			global $post_type;
			if ( $post_type != DIALOG_CONTACT_FORM_POST_TYPE ) {
				return;
			}

			include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/template-field.php';
		}

		/**
		 * Register plugin activation action for later use, and
		 * Flush rewrite rules on plugin activation
		 * @return void
		 */
		public function plugin_activation() {
			\DialogContactForm\Activation::init();
			do_action( 'dialog_contact_form_activation' );
			flush_rewrite_rules();
		}

		/**
		 * Flush rewrite rules on plugin deactivation
		 * @return void
		 */
		public function plugin_deactivate() {
			do_action( 'dialog_contact_form_deactivate' );
			flush_rewrite_rules();
		}

		/**
		 * Configure PHPMailer for sending email over SMTP
		 *
		 * @param PHPMailer $mailer
		 */
		public function phpmailer_config( &$mailer ) {

			$options = get_option( 'dialog_contact_form' );

			if ( ! isset( $options['mailer'] ) ) {
				return;
			}

			if ( ! in_array( $options['mailer'], array( 'yes', 'on', '1', 1, true, 'true' ), true ) ) {
				return;
			}

			$host       = ! empty( $options['smpt_host'] ) ? sanitize_text_field( $options['smpt_host'] ) : '';
			$username   = ! empty( $options['smpt_username'] ) ? sanitize_text_field( $options['smpt_username'] ) : '';
			$password   = ! empty( $options['smpt_password'] ) ? sanitize_text_field( $options['smpt_password'] ) : '';
			$port       = ! empty( $options['smpt_port'] ) ? absint( $options['smpt_port'] ) : '';
			$encryption = ! empty( $options['encryption'] ) ? sanitize_text_field( $options['encryption'] ) : '';

			if ( empty( $host ) || empty( $username ) || empty( $password ) || empty( $port ) ) {
				return;
			}

			$mailer->isSMTP();
			$mailer->SMTPAuth = true;
			$mailer->Host     = $host;
			$mailer->Port     = $port;
			$mailer->Username = $username;
			$mailer->Password = $password;

			// Additional settings…
			if ( in_array( $encryption, array( 'ssl', 'tls' ) ) ) {
				$mailer->SMTPSecure = $encryption;
			}
		}

		/**
		 * Show notice about PHP version
		 *
		 * @return void
		 */
		function php_version_notice() {

			if ( $this->is_supported_php() || ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$error = __( 'Your installed PHP Version is: ', 'dialog-contact-form' ) . PHP_VERSION . '. ';
			$error .= sprintf( __( 'The Dialog Contact Form plugin requires PHP version %s or greater.', 'dialog-contact-form' ), $this->min_php );
			?>
            <div class="error">
                <p><?php printf( $error ); ?></p>
            </div>
			<?php
		}

		/**
		 * Bail out if the php version is lower than
		 *
		 * @return void
		 */
		function auto_deactivate() {
			if ( $this->is_supported_php() ) {
				return;
			}

			deactivate_plugins( plugin_basename( __FILE__ ) );

			$error = '<h1>' . __( 'An Error Occurred', 'dialog-contact-form' ) . '</h1>';
			$error .= '<h2>' . __( 'Your installed PHP Version is: ', 'dialog-contact-form' ) . PHP_VERSION . '</h2>';
			$error .= '<p>' . sprintf( __( 'The Dialog Contact Form plugin requires PHP version %s or greater', 'dialog-contact-form' ), $this->min_php ) . '</p>';
			$error .= '<p>' . sprintf( __( 'The version of your PHP is %s unsupported and old %s. ', 'dialog-contact-form' ),
					'<a href="http://php.net/supported-versions.php" target="_blank"><strong>',
					'</strong></a>'
				);
			$error .= __( 'You should update your PHP software or contact your host regarding this matter.', 'dialog-contact-form' ) . '</p>';

			wp_die( $error, __( 'Plugin Activation Error', 'dialog-contact-form' ), array( 'back_link' => true ) );
		}

		/**
		 * Get dynamic variables that will pass to javaScript variables
		 *
		 * @return array
		 */
		private function localize_script() {
			$messages  = dcf_validation_messages();
			$options   = get_dialog_contact_form_option();
			$_messages = array();
			foreach ( $messages as $key => $message ) {
				$_messages[ $key ] = ! empty( $options[ $key ] ) ? $options[ $key ] : $message;
			}

			$variables = array(
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'dialog_contact_form_ajax' ),
				'selector'     => 'dcf-form',
				'fieldClass'   => 'dcf-has-error',
				'errorClass'   => 'dcf-error-message',
				'loadingClass' => 'is-loading',
			);

			return array_merge( $variables, $_messages );
		}

		/**
		 * What type of request is this?
		 *
		 * @param  string $type admin, ajax, cron or frontend.
		 *
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}

			return false;
		}

		/**
		 * Check if the PHP version is supported
		 *
		 * @param null $min_php
		 *
		 * @return bool
		 */
		private function is_supported_php( $min_php = null ) {

			$min_php = $min_php ? $min_php : $this->min_php;

			if ( version_compare( PHP_VERSION, $min_php, '<=' ) ) {
				return false;
			}

			return true;
		}
	}
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
Dialog_Contact_Form::init();
