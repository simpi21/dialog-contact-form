<?php
/*
 * Plugin Name: 	Dialog Contact Form
 * Plugin URI: 		http://wordpress.org/plugins/dialog-contact-form/
 * Description: 	A very simple AJAX contact form with captcha validation.
 * Version: 		1.2.1
 * Author: 			Sayful Islam
 * Author URI: 		http://www.sayfulit.com
 * Text Domain: 	dialog-contact-form
 * Domain Path: 	/languages/
 * License: 		GPL2
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( !class_exists('Dialog_Contact_Form')):

class Dialog_Contact_Form {

    private $plugin_name    = 'dialog-contact-form';
    private $plugin_version = '1.2.1';
    private $plugin_path;
    private $plugin_url;
    private $options;

	protected static $instance = null;

	public function __construct(){

		// For Development
		$this->plugin_version = time();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts') );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style') );
		add_shortcode( 'dialog_contact_form', array( $this, 'shortcode') );

		add_action( 'wp_ajax_dialog_contact_form', array( $this, 'process_form' ) );
		add_action( 'wp_ajax_nopriv_dialog_contact_form', array( $this, 'process_form' ) );
		
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

		$this->options = self::get_options();

		add_action('wp_footer', array( $this, 'dialog') );

		if ( self::is_session_started() === FALSE ) session_start();

		$this->includes();
	}

	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function includes()
	{
		include_once $this->plugin_path() . '/includes/Dialog_Contact_Form_Admin.php';
		include_once $this->plugin_path() . '/includes/Dialog_Contact_Form_Captcha.php';

		new Dialog_Contact_Form_Admin( $this->options, $this->plugin_path() );
		new Dialog_Contact_Form_Captcha( $this->plugin_path() );
	}

	public function enqueue_scripts() {
		
		if ( ! $this->should_load_style() ) {
			return;
		}

		wp_enqueue_style( $this->plugin_name, $this->plugin_url() . '/assets/css/style.css', array(), $this->plugin_version, 'all' );
		wp_enqueue_script( $this->plugin_name, $this->plugin_url() . '/assets/js/script.js', array( 'jquery' ), $this->plugin_version, true );
    	
        wp_localize_script( $this->plugin_name, 'DialogContactForm', array(
            'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
            'nonce' 		=> wp_create_nonce( 'dialog_contact_form_ajax' ),
        ));
	}

	private function should_load_style()
	{
		global $post;
		$options = self::get_options();

		if ( 'show' == $options['display_dialog'] ) {
			return true;
		}

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'dialog_contact_form') ) {
			return true;
		}

		return false;
	}

	public function admin_style( $hook ){

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( $this->plugin_name . '-admin', $this->plugin_url() . '/assets/css/admin.css' );
		wp_enqueue_script( $this->plugin_name . '-admin', $this->plugin_url() . '/assets/js/admin.js', array( 'wp-color-picker' ), false, true );
	}

	public function action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'options-general.php?page=dialogcf_options_page' ) . '">' . __( 'Settings', 'dialog-contact-form' ) . '</a>'
		);

		return array_merge( $plugin_links, $links );
	}

	public static function get_options(){
		$options_array = array(
	      	'email' 			=> get_option( 'admin_email' ),
	      	'field_web' 		=> '',
	      	'field_phone' 		=> '',
	      	'field_sub' 		=> '',
	      	'field_captcha' 	=> '',
	      	'display_dialog' 	=> 'show',
	      	'label_name' 		=> __('Name *', 'dialog-contact-form'),
	      	'label_email' 		=> __('Email *', 'dialog-contact-form'),
	      	'label_url' 		=> __('Website', 'dialog-contact-form'),
	      	'label_phone' 		=> __('Phone', 'dialog-contact-form'),
	      	'label_sub' 		=> __('Subject', 'dialog-contact-form'),
	      	'label_msg' 		=> __('Message *', 'dialog-contact-form'),
	      	'label_capt' 		=> __('Enter captcha code *', 'dialog-contact-form'),
	      	'label_submit' 		=> __('Send Message', 'dialog-contact-form'),
	      	'place_name' 		=> __('Name', 'dialog-contact-form'),
	      	'place_email' 		=> __('mail@example.com', 'dialog-contact-form'),
	      	'place_url' 		=> __('http://example.com', 'dialog-contact-form'),
	      	'place_phone' 		=> __('xxx-xxxx-xxxx', 'dialog-contact-form'),
	      	'place_sub' 		=> __('Please enter the subject of your message here.', 'dialog-contact-form'),
	      	'place_msg' 		=> __('Please enter your message here.', 'dialog-contact-form'),
	      	'place_capt' 		=> __('Enter captcha code', 'dialog-contact-form'),
	      	'err_name' 			=> __('Name should be at least 3 characters.', 'dialog-contact-form' ),
	      	'err_email' 		=> __('Email is invalid.', 'dialog-contact-form' ),
	      	'err_url' 			=> __('URL is invalid.', 'dialog-contact-form' ),
	      	'err_message' 		=> __('Message should be at least 15 characters.', 'dialog-contact-form' ),
	      	'err_captcha' 		=> __('Captcha code is incorrect.', 'dialog-contact-form' ),
	      	'msg_success' 		=> __('Your message was sent successfully. Thanks.', 'dialog-contact-form' ),
	      	'msg_fail' 			=> __('Please check the error below.', 'dialog-contact-form' ),
	      	'msg_subject' 		=> sprintf(__('Someone sent you a message from %1$s', 'dialog-contact-form'), get_bloginfo('name') ),
	      	'msg_body' 			=> sprintf(__('This mail is sent via contact form %1$s', 'dialog-contact-form' ), get_bloginfo('name') ),
	      	'dialog_button' 	=> __('Leave a message', 'dialog-contact-form' ),
	      	'dialog_title' 		=> __('Contact us', 'dialog-contact-form' ),
	      	'dialog_width' 		=> 400,
	      	'dialog_color' 		=> '#ea632d',
	    );
		$options = wp_parse_args( get_option( 'dialogcf_options' ), $options_array);
	   	return $options;
	}

	public function process_form()
	{
		// Check if nonce is set
		if ( ! isset($_POST['nonce'] ) ) {
			return;
		}

		// Check if nonce is valid
		if ( ! wp_verify_nonce( $_POST['nonce'], 'dialog_contact_form_ajax' ) ) {
			return;
		}

		$options 	= $this->options;

		$hasError 	= false;
		$errorTxt 	= array();

		$form_data = isset($_POST['formData']) ? $_POST['formData'] : null;
		parse_str($form_data, $data);

		$phone		= (isset($data['phone'])) ? sanitize_text_field($data['phone']) : '';
		$website    = (isset($data['website'])) ? esc_url($data['website']) : '';
		$msgsubject = (isset($data['subject'])) ? sanitize_text_field($data['subject']) : '';

		// Validate fullname with PHP
		if ( strlen($data['fullname']) < 3 ) {
			$errorTxt[] = $options['err_name'];
	        $hasError = true;
		}else {
			$fullname = sanitize_text_field($data['fullname']);
		}

		// Validate email address with PHP
		if(!is_email($data['email'])){
			$errorTxt[] = $options['err_email'];
	        $hasError = true;
		} else {
			$email = sanitize_email($data['email']);
		}

		// Validate website with PHP
		if(isset($options['field_web']) && $options['field_web'] == 'on' ){
			if(!empty($website)){
				if (!filter_var($data['website'], FILTER_VALIDATE_URL)) {
					$errorTxt[] = $options['err_url'];
		        	$hasError = true;
				}
			}
		}

		// Validate message with PHP
		if ( strlen($data['message']) < 15 ) {
			$errorTxt[] = $options['err_message'];
	        $hasError = true;
		} else {
			$message = esc_textarea($data['message']);
		}

		// Validate Captcha code with PHP
		if(isset($options['field_captcha']) && $options['field_captcha'] == 'on' ){

			$captcha = sanitize_text_field($data['captcha']);

			if($captcha != $_SESSION['dialog_contact_form']){
				$errorTxt[] = $options['err_captcha'];
		        $hasError = true;
			}
		}

		// If there is any error, show error message
		if ( $hasError ) {
			http_response_code(406);
			wp_send_json_error( $errorTxt );
		}

		// If there is no error, send the message
		if ( ! $hasError ) {
			$to = isset($options['email'] ) ? $options['email'] : get_option( 'admin_email' );

	        $subject 	= (isset($options['msg_subject'])) ? $options['msg_subject'] : '';
	        $website 	= (isset($website)) ? $website : '';
	        $phone 		= (isset($phone)) ? $phone : '';
	        $msgsubject = (isset($msgsubject)) ? $msgsubject : '';

	        $body  = "Name: $fullname \nEmail: $email \nWebsite: $website \nPhone: $phone \n\nSubject: $msgsubject \n\nMessage: $message \n\n";
	        $body .= "--\n";
	        $body .= $options['msg_body']."\n";
	        $body .= home_url();

			$headers = 'From: '.$fullname.' <'.$email.'>' . "\r\n" . 'Reply-To: ' . $email;

			wp_mail($to, $subject, $body, $headers);

			http_response_code(200);
			wp_send_json_success( esc_attr( $options['msg_success'] ) );
		}

		wp_die();
	}

	public function shortcode(){

		$options = self::get_options();

		ob_start();
		include_once $this->plugin_path() . '/templates/form.php';
		return ob_get_clean();

	}

	function dialog(){
		$options = self::get_options();
		if ( 'show' != $options['display_dialog'] ){
			return;
		}
		include_once $this->plugin_path() . '/templates/modal.php';
	}

	/**
	 * Check if session is already started
	 * 
	 * @return boolean
	 */
	private static function is_session_started(){
	    if ( php_sapi_name() !== 'cli' ) {
	        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
	            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
	        } else {
	            return session_id() === '' ? FALSE : TRUE;
	        }
	    }
	    return FALSE;
	}

    /**
     * Plugin path.
     *
     * @return string Plugin path
     */
    private function plugin_path() {
        if ( $this->plugin_path ) return $this->plugin_path;

        return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Plugin url.
     *
     * @return string Plugin url
     */
    private function plugin_url() {
        if ( $this->plugin_url ) return $this->plugin_url;

        return $this->plugin_url = untrailingslashit( plugin_dir_url( __FILE__ ) );
    }
}
endif;

add_action( 'plugins_loaded', array( 'Dialog_Contact_Form', 'get_instance' ) );

function dialog_contact_form_activation_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'options-general.php?page=dialogcf_options_page' ) ) );
    }
}
add_action( 'activated_plugin', 'dialog_contact_form_activation_redirect' );