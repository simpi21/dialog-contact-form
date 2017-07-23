<?php

if ( ! class_exists( 'DialogContactFormActivation' ) ):

	class DialogContactFormActivation {

		public function __construct() {
			add_action( 'dialog_contact_form_activation', array( $this, 'add_default_form' ) );
		}

		public function add_default_form() {

			$contact_forms = get_posts( array(
				'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
				'posts_per_page' => 5,
				'post_status'    => 'publish'
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
			}

		}

		private function form_field() {
			return array(
				array(
					'field_title'   => esc_html__( 'Your Name', 'dialog-contact-form' ),
					'field_name'    => 'your_name',
					'field_id'      => 'your_name',
					'field_type'    => 'text',
					'options'       => '',
					'number_min'    => '',
					'number_max'    => '',
					'number_step'   => '',
					'field_value'   => '',
					'field_class'   => '',
					'field_width'   => 'is-6',
					'validation'    => array( 'required' ),
					'placeholder'   => '',
					'error_message' => '',
				),
				array(
					'field_title'   => esc_html__( 'Your Email', 'dialog-contact-form' ),
					'field_name'    => 'your_email',
					'field_id'      => 'your_email',
					'field_type'    => 'email',
					'options'       => '',
					'number_min'    => '',
					'number_max'    => '',
					'number_step'   => '',
					'field_value'   => '',
					'field_class'   => '',
					'field_width'   => 'is-6',
					'validation'    => array( 'required', 'email' ),
					'placeholder'   => '',
					'error_message' => '',
				),
				array(
					'field_title'   => esc_html__( 'Subject', 'dialog-contact-form' ),
					'field_name'    => 'subject',
					'field_id'      => 'subject',
					'field_type'    => 'text',
					'options'       => '',
					'number_min'    => '',
					'number_max'    => '',
					'number_step'   => '',
					'field_value'   => '',
					'field_class'   => '',
					'field_width'   => 'is-12',
					'validation'    => array( 'required' ),
					'placeholder'   => '',
					'error_message' => '',
				),
				array(
					'field_title'   => esc_html__( 'Your Message', 'dialog-contact-form' ),
					'field_name'    => 'your_message',
					'field_id'      => 'your_message',
					'field_type'    => 'textarea',
					'options'       => '',
					'number_min'    => '',
					'number_max'    => '',
					'number_step'   => '',
					'field_value'   => '',
					'field_class'   => '',
					'field_width'   => 'is-12',
					'validation'    => array( 'required' ),
					'placeholder'   => '',
					'error_message' => '',
				),
			);
		}

		private function form_config() {
			return dcf_default_configuration();
		}

		private function form_message() {
			return dcf_validation_messages();
		}
	}

endif;

new DialogContactFormActivation();