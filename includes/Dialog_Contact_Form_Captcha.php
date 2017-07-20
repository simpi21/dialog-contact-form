<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists('Dialog_Contact_Form_Captcha') ):

class Dialog_Contact_Form_Captcha
{
	private $plugin_path;
	private $font;

	public function __construct( $plugin_path )
	{
		$this->plugin_path 	= $plugin_path;
		$this->font 		= $this->plugin_path . '/assets/fonts/ArchitectsDaughter.ttf';

		if ( $this->is_session_started() === FALSE ) session_start();

		add_action( 'wp_ajax_dialog_contact_form_captcha', array( $this, 'create_captcha' ) );
		add_action( 'wp_ajax_nopriv_dialog_contact_form_captcha', array( $this, 'create_captcha' ) );
	}

	public function create_captcha()
	{
		// Check if nonce is set
		if ( ! isset($_POST['nonce'] ) ) {
			wp_die();
		}

		// Check if nonce is valid
		if ( ! wp_verify_nonce( $_POST['nonce'], 'dialog_contact_form_ajax' ) ) {
			wp_die();
		}
		
		$width 			='120';
		$height 		='40';
		$characters 	='6';

		$code = $this->generateCode($characters);
		/* font size will be 75% of the image height */
		$font_size = $height * 0.55;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, 255, 255, 255);
		$text_color = imagecolorallocate($image, 20, 40, 100);
		$noise_color = imagecolorallocate($image, 190, 199, 224);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ )
		{
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) 
		{
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
		/* output captcha image to browser */
		// header('Content-Type: image/jpeg');
		ob_start ();
		imagejpeg($image);
		$image_data = ob_get_contents ();
		ob_end_clean ();
		imagedestroy($image);
		$_SESSION['dialog_contact_form'] = $code;
		echo base64_encode($image_data);
		wp_die();
	}

	/**
	 * Generate random characters by given length
	 * 
	 * @param  integer $characters
	 * @return string
	 */
	private function generateCode( $characters = 6 ) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}

	/**
	 * Check if session is started
	 * 
	 * @return boolean
	 */
	private function is_session_started(){
	    if ( php_sapi_name() !== 'cli' ) {
	        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
	            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
	        } else {
	            return session_id() === '' ? FALSE : TRUE;
	        }
	    }
	    return FALSE;
	}
}

endif;