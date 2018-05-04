<?php

namespace DialogContactForm;

class Activation {

	/**
	 * @var object
	 */
	protected static $instance;

	/**
	 * @return Activation
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Dialog_Contact_Form_Activation constructor.
	 */
	public function __construct() {
		add_action( 'dialog_contact_form_activation', array( $this, 'add_default_form' ) );
	}

	/**
	 * Add sample form on plugin activation
	 */
	public function add_default_form() {
		$contact_forms = get_posts( array(
			'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
			'posts_per_page' => 5,
			'post_status'    => 'any'
		) );

		if ( count( $contact_forms ) > 0 ) {
			return;
		}

		$post_title = esc_html__( 'Contact Form 1', 'dialog-contact-form' );

		$post_id = wp_insert_post( array(
			'post_title'     => $post_title,
			'post_status'    => 'publish',
			'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		) );

		if ( is_int( $post_id ) ) {
			update_post_meta( $post_id, '_contact_form_fields', dcf_default_fields() );
			update_post_meta( $post_id, '_contact_form_messages', dcf_validation_messages() );
			update_post_meta( $post_id, '_contact_form_config', dcf_default_configuration() );
			update_post_meta( $post_id, '_contact_form_mail', dcf_default_mail_template() );

			$this->upgrade_to_version_2( $post_id );
		}
	}


	/**
	 * Upgrade to version 2 from version 1
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function upgrade_to_version_2( $post_id = 0 ) {
		$old_option = get_option( 'dialogcf_options' );
		if ( ! isset( $old_option['display_dialog'], $old_option['dialog_color'] ) ) {
			return false;
		}

		if ( 'show' != $old_option['display_dialog'] ) {
			return false;
		}

		$option                             = dcf_default_options();
		$option['dialog_button_background'] = $old_option['dialog_color'];
		$option['dialog_form_id']           = $post_id;

		return update_option( 'dialog_contact_form', $option );
	}
}
