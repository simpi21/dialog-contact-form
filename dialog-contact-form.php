<?php
/**
 * Plugin Name: Dialog Contact Form
 * Plugin URI: http://wordpress.org/plugins/dialog-contact-form/
 * Description: Just another WordPress contact form plugin. Simple but flexible.
 * Version: 2.1.0
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
		protected static $instance;

		/**
		 * Plugin name slug
		 *
		 * @var string
		 */
		protected $plugin_name = 'dialog-contact-form';

		/**
		 * Plugin custom post type
		 *
		 * @var string
		 */
		protected $post_type = 'dialog-contact-form';

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		protected $version = '2.1.0';

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

			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivate' ) );

			// include files
			$this->include_files();

			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 30 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'admin_footer', array( $this, 'form_template' ), 0 );

			do_action( 'dialog_contact_form_init' );
		}

		/**
		 * Define constants
		 */
		private function define_constants() {
			define( 'DIALOG_CONTACT_FORM', $this->plugin_name );
			define( 'DIALOG_CONTACT_FORM_POST_TYPE', $this->post_type );
			define( 'DIALOG_CONTACT_FORM_VERSION', $this->version );
			define( 'DIALOG_CONTACT_FORM_FILE', __FILE__ );
			define( 'DIALOG_CONTACT_FORM_PATH', dirname( DIALOG_CONTACT_FORM_FILE ) );
			define( 'DIALOG_CONTACT_FORM_INCLUDES', DIALOG_CONTACT_FORM_PATH . '/includes' );
			define( 'DIALOG_CONTACT_FORM_TEMPLATES', DIALOG_CONTACT_FORM_PATH . '/templates' );
			define( 'DIALOG_CONTACT_FORM_URL', plugins_url( '', DIALOG_CONTACT_FORM_FILE ) );
			define( 'DIALOG_CONTACT_FORM_ASSETS', DIALOG_CONTACT_FORM_URL . '/assets' );
			define( 'DIALOG_CONTACT_FORM_UPLOAD_DIR', 'dcf-attachments' );
		}

		/**
		 * Includes plugin files
		 */
		private function include_files() {
			include_once DIALOG_CONTACT_FORM_INCLUDES . '/functions-dialog-contact-form.php';
			include_once DIALOG_CONTACT_FORM_INCLUDES . '/class-dialog-contact-form-validator.php';
			include_once DIALOG_CONTACT_FORM_INCLUDES . '/class-dialog-contact-form-settings.php';
			include_once DIALOG_CONTACT_FORM_INCLUDES . '/class-dialog-contact-form-post-type.php';
			include_once DIALOG_CONTACT_FORM_INCLUDES . '/class-dialog-contact-form-meta-boxes.php';
			include_once DIALOG_CONTACT_FORM_INCLUDES . '/class-dialog-contact-form-process-request.php';
			include_once DIALOG_CONTACT_FORM_INCLUDES . '/class-dialog-contact-form-shortcode.php';
			include_once DIALOG_CONTACT_FORM_INCLUDES . '/class-dialog-contact-form-activation.php';
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

			wp_enqueue_style(
				$this->plugin_name . '-admin',
				DIALOG_CONTACT_FORM_ASSETS . '/css/admin.css',
				array( 'wp-color-picker' ),
				DIALOG_CONTACT_FORM_VERSION,
				'all'
			);
			wp_enqueue_script(
				$this->plugin_name . '-admin',
				DIALOG_CONTACT_FORM_ASSETS . '/js/admin' . $suffix . '.js',
				array(
					'jquery',
					'jquery-ui-sortable',
					'wp-color-picker'
				),
				DIALOG_CONTACT_FORM_VERSION,
				true
			);
		}

		/**
		 * Load plugin front-end scripts
		 */
		public function frontend_scripts() {
			$suffix = ( defined( "SCRIPT_DEBUG" ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_style( $this->plugin_name,
				DIALOG_CONTACT_FORM_ASSETS . '/css/style.css',
				array(), DIALOG_CONTACT_FORM_VERSION, 'all' );

			wp_enqueue_script( $this->plugin_name,
				DIALOG_CONTACT_FORM_ASSETS . '/js/form' . $suffix . '.js',
				array(), DIALOG_CONTACT_FORM_VERSION, true );

			wp_localize_script( $this->plugin_name, 'DialogContactForm', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'dialog_contact_form_ajax' ),
			) );
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
