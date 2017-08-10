<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'DialogContactFormActivation' ) ):

	class DialogContactFormActivation {

		public function __construct() {
			add_action( 'dialog_contact_form_activation', array( $this, 'add_default_form' ) );
		}

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
				update_post_meta( $post_id, '_contact_form_fields', $this->form_field() );
				update_post_meta( $post_id, '_contact_form_messages', $this->form_message() );
				update_post_meta( $post_id, '_contact_form_config', $this->form_config() );
				update_post_meta( $post_id, '_contact_form_mail', $this->form_mail() );

				$this->upgrade_to_version_2( $post_id );
			}

		}

		private function form_field() {
			return dcf_default_fields();
		}

		private function form_config() {
			return dcf_default_configuration();
		}

		private function form_message() {
			return dcf_validation_messages();
		}

		private function form_mail() {
			return dcf_default_mail_template();
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

endif;

new DialogContactFormActivation();