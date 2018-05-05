<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;

class Email extends Abstract_Action {

	/**
	 * Email constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->id       = 'email';
		$this->title    = __( 'Email Notification', 'dialog-contact-form' );
		$this->settings = array_merge( $this->settings, $this->settings() );
	}

	/**
	 * Process action
	 */
	public function process( $action_id, $form_id, $data ) {
		// TODO: Implement process() method.
	}

	/**
	 * @return array
	 */
	private function settings() {
		$defaults = array(
			'receiver'    => get_option( 'admin_email' ),
			'senderEmail' => '[your_email]',
			'senderName'  => '[your_name]',
			'subject'     => get_option( 'blogname' ) . ': [subject]',
			'body'        => "[all_fields_table]",
		);

		return array(
			'receiver'    => array(
				'type'        => 'text',
				'id'          => 'receiver',
				'group'       => 'mail',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Receiver(s)', 'dialog-contact-form' ),
				'description' => __( 'Define the emails used (separated by comma) to receive emails.', 'dialog-contact-form' ),
				'default'     => $defaults['receiver'],
			),
			'senderEmail' => array(
				'type'        => 'text',
				'id'          => 'senderEmail',
				'group'       => 'mail',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Sender Email', 'dialog-contact-form' ),
				'description' => __( 'Define from what email send the message.', 'dialog-contact-form' ),
				'default'     => $defaults['senderEmail'],
			),
			'senderName'  => array(
				'type'        => 'text',
				'id'          => 'senderName',
				'group'       => 'mail',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Sender Name', 'dialog-contact-form' ),
				'description' => __( 'Define the sender name that send the message.', 'dialog-contact-form' ),
				'default'     => $defaults['senderName'],
			),
			'subject'     => array(
				'type'        => 'text',
				'id'          => 'subject',
				'group'       => 'mail',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Message Subject', 'dialog-contact-form' ),
				'description' => __( 'Define the subject of the email sent to you.', 'dialog-contact-form' ),
				'default'     => $defaults['subject'],
			),
			'body'        => array(
				'type'        => 'textarea',
				'id'          => 'body',
				'group'       => 'mail',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Message Body', 'dialog-contact-form' ),
				'description' => sprintf(
					__( 'Use mail-tags or enter %s for including all fields.', 'dialog-contact-form' ),
					'<strong>[all_fields_table]</strong>'
				),
				'rows'        => 10,
				'input_class' => 'widefat',
				'default'     => $defaults['body'],
			),
		);
	}

	/**
	 * @return string
	 */
	public function get_description() {
		/** @var \WP_Post $post */
		global $post;
		if ( ! $post instanceof \WP_Post ) {
			return '';
		}
		$fields = get_post_meta( $post->ID, '_contact_form_fields', true );
		$fields = is_array( $fields ) ? $fields : array();
		if ( ! isset( $_GET['action'] ) && count( $fields ) < 1 ) {
			$fields = dcf_default_fields();
		}

		$name_placeholders = array();
		foreach ( $fields as $field ) {
			if ( 'file' == $field['field_type'] ) {
				continue;
			}
			$name_placeholders[] = '[' . $field['field_name'] . ']';
		}

		$html = '<p class="description">';
		$html .= esc_html__( 'In the following fields, you can use these mail-tags:', 'dialog-contact-form' );
		$html .= '</p>';
		foreach ( $name_placeholders as $name_placeholder ) {
			$html .= '<code class="mailtag code">' . esc_attr( $name_placeholder ) . '</code>';
		}
		$html .= '<br><hr>';

		return $html;
	}
}