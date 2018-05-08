<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;
use DialogContactForm\Supports\Mailer;

class EmailNotification extends Abstract_Action {

	/**
	 * Email constructor.
	 */
	public function __construct() {
		$this->id       = 'email_notification';
		$this->title    = __( 'Email Notification', 'dialog-contact-form' );
		$this->settings = array_merge( $this->settings, $this->settings() );
	}

	/**
	 * Process action
	 *
	 * @param int $form_id
	 * @param array $data
	 *
	 * @return bool
	 */
	public function process( $form_id, $data ) {
		$placeholder = array(
			'[system:admin_email]' => get_option( 'admin_email' ),
			'[system:blogname]'    => get_option( 'blogname' ),
			'[system:siteurl]'     => get_option( 'siteurl' ),
			'[system:home]'        => get_option( 'home' ),
		);

		$attachments = $data['attachments'];
		$fields      = get_post_meta( $form_id, '_contact_form_fields', true );
		$mail        = get_post_meta( $form_id, '_contact_form_mail', true );

		$all_fields = array();
		foreach ( $fields as $field ) {
			if ( 'file' == $field['field_type'] ) {
				continue;
			}
			$value = isset( $data[ $field['field_name'] ] ) ? $data[ $field['field_name'] ] : '';

			// Join array elements with a new line string
			if ( is_array( $value ) ) {
				$value = implode( PHP_EOL, $value );
			}

			$all_fields[ $field['field_name'] ] = array(
				'label' => $field['field_title'],
				'value' => $value,
			);

			$placeholder[ "[" . $field['field_name'] . "]" ] = $value;
		}

		$subject = $mail['subject'];
		$subject = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $subject );

		$body = $mail['body'];
		if ( false !== strpos( $body, '[all_fields_table]' ) ) {
			ob_start();
			include_once DIALOG_CONTACT_FORM_TEMPLATES . '/emails/email-notification.php';
			$message = ob_get_clean();
		} else {
			$body    = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $body );
			$body    = str_replace( array( "\r\n", "\r", "\n" ), "<br>", $body );
			$message = stripslashes( wp_kses_post( $body ) );
		}

		$receiver = $mail['receiver'];
		$receiver = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $receiver );
		$receiver = ( false !== strpos( $receiver, ',' ) ) ? explode( ',', $receiver ) : $receiver;

		$senderEmail = $mail['senderEmail'];
		$senderEmail = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $senderEmail );

		$senderName = esc_attr( $mail['senderName'] );
		$senderName = str_replace( array_keys( $placeholder ), array_values( $placeholder ), $senderName );

		$mailer = new Mailer();
		$mailer->setReceiver( $receiver );
		$mailer->setSubject( $subject );
		$mailer->setMessage( $message );
		$mailer->setFrom( $senderEmail, $senderName );
		$mailer->setReplyTo( $senderEmail, $senderName );
		$mailer->setContentType( 'html' );
		$mailer->setAttachments( $attachments );

		return $mailer->send();
	}

	/**
	 * @return array
	 */
	private function settings() {
		$defaults = array(
			'receiver'    => '[system:admin_email]',
			'senderEmail' => '[your_email]',
			'senderName'  => '[your_name]',
			'subject'     => '[system:blogname] : [subject]',
			'body'        => "[all_fields_table]",
		);

		return array(
			'receiver'    => array(
				'type'        => 'text',
				'id'          => 'receiver',
				'group'       => 'email_notification',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Receiver(s)', 'dialog-contact-form' ),
				'description' => __( 'Define the emails used (separated by comma) to receive emails.', 'dialog-contact-form' ),
				'default'     => $defaults['receiver'],
			),
			'senderEmail' => array(
				'type'        => 'text',
				'id'          => 'senderEmail',
				'group'       => 'email_notification',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Sender Email', 'dialog-contact-form' ),
				'description' => __( 'Define from what email send the message.', 'dialog-contact-form' ),
				'default'     => $defaults['senderEmail'],
			),
			'senderName'  => array(
				'type'        => 'text',
				'id'          => 'senderName',
				'group'       => 'email_notification',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Sender Name', 'dialog-contact-form' ),
				'description' => __( 'Define the sender name that send the message.', 'dialog-contact-form' ),
				'default'     => $defaults['senderName'],
			),
			'subject'     => array(
				'type'        => 'text',
				'id'          => 'subject',
				'group'       => 'email_notification',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Message Subject', 'dialog-contact-form' ),
				'description' => __( 'Define the subject of the email sent to you.', 'dialog-contact-form' ),
				'default'     => $defaults['subject'],
			),
			'body'        => array(
				'type'        => 'textarea',
				'id'          => 'body',
				'group'       => 'email_notification',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Message Body', 'dialog-contact-form' ),
				'description' => sprintf(
					__( 'Use mail-tags or enter %s for including all fields.', 'dialog-contact-form' ),
					'<strong>[all_fields_table]</strong>'
				),
				'rows'        => 10,
				'input_class' => 'widefat',
				'sanitize'    => array( 'DialogContactForm\\Supports\\Sanitize', 'html' ),
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

	/**
	 * Save action settings
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function save( $post_id, $post ) {
		if ( ! empty( $_POST['email_notification'] ) ) {
			$sanitize_data = $this->sanitize_settings( $_POST['email_notification'] );

			update_post_meta( $post_id, '_contact_form_mail', $sanitize_data );
		} else {
			delete_post_meta( $post_id, '_contact_form_mail' );
		}
	}
}