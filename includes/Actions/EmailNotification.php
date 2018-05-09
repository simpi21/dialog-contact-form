<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;
use DialogContactForm\Supports\Mailer;

class EmailNotification extends Abstract_Action {

	/**
	 * Hold value from WordPress option
	 *
	 * @var array
	 */
	private $system_fields = array();

	/**
	 * Email constructor.
	 */
	public function __construct() {
		$this->id            = 'email_notification';
		$this->title         = __( 'Email Notification', 'dialog-contact-form' );
		$this->settings      = array_merge( $this->settings, $this->settings() );
		$this->system_fields = array(
			'[system:admin_email]' => get_option( 'admin_email' ),
			'[system:blogname]'    => get_option( 'blogname' ),
			'[system:siteurl]'     => get_option( 'siteurl' ),
			'[system:home]'        => get_option( 'home' ),
		);
	}

	/**
	 * Get email sender email
	 *
	 * @param string $senderEmail
	 *
	 * @return string
	 */
	private static function getSenderEmail( $senderEmail ) {
		if ( is_string( $senderEmail ) && filter_var( $senderEmail, FILTER_VALIDATE_EMAIL ) !== false ) {
			return sanitize_email( $senderEmail );
		}

		// Get the site domain and get rid of www.
		$site_name = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $site_name, 0, 4 ) == 'www.' ) {
			$site_name = substr( $site_name, 4 );
		}

		return 'noreply@' . $site_name;
	}

	/**
	 * Get email sender name
	 *
	 * @param string $senderName
	 *
	 * @return string
	 */
	private static function getSenderName( $senderName ) {
		if ( is_string( $senderName ) ) {
			return sanitize_text_field( $senderName );
		}

		return '';
	}

	/**
	 * Get email receiver
	 *
	 * @param array|string $receiver
	 *
	 * @return array|string
	 */
	private static function getReceiver( $receiver ) {
		$receiver = ( false !== strpos( $receiver, ',' ) ) ? explode( ',', $receiver ) : $receiver;

		if ( is_string( $receiver ) && filter_var( $receiver, FILTER_VALIDATE_EMAIL ) !== false ) {
			return sanitize_email( $receiver );
		}

		$receivers = array();
		if ( is_array( $receiver ) ) {
			foreach ( $receiver as $_receiver ) {
				if ( is_string( $_receiver ) && filter_var( $_receiver, FILTER_VALIDATE_EMAIL ) !== false ) {
					$receivers[] = sanitize_email( $_receiver );
				}
			}
		}

		if ( count( $receivers ) > 0 ) {
			return $receivers;
		}

		return get_option( 'admin_email' );
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
		$attachments = $data['attachments'];
		$fields      = get_post_meta( $form_id, '_contact_form_fields', true );
		$mail        = get_post_meta( $form_id, '_contact_form_mail', true );

		$form_fields = array();
		foreach ( $fields as $field ) {
			if ( 'file' == $field['field_type'] ) {
				continue;
			}
			$value = isset( $data[ $field['field_name'] ] ) ? $data[ $field['field_name'] ] : '';

			// Join array elements with a new line string
			if ( is_array( $value ) ) {
				$value = implode( PHP_EOL, $value );
			}

			$form_fields[] = array(
				'label'       => $field['field_title'],
				'value'       => $value,
				'placeholder' => '[' . $field['field_name'] . ']',
			);
		}

		$_keys   = array_merge( array_keys( $this->system_fields ), array_column( $form_fields, 'placeholder' ) );
		$_values = array_merge( array_values( $this->system_fields ), array_column( $form_fields, 'value' ) );

		$subject = $mail['subject'];
		$subject = str_replace( $_keys, $_values, $subject );

		$body = $mail['body'];
		if ( false !== strpos( $body, '[all_fields_table]' ) ) {
			ob_start();
			include_once DIALOG_CONTACT_FORM_TEMPLATES . '/emails/email-notification.php';
			$message = ob_get_clean();
		} else {
			$body    = str_replace( $_keys, $_values, $body );
			$body    = str_replace( array( "\r\n", "\r", "\n" ), "<br>", $body );
			$message = stripslashes( wp_kses_post( $body ) );
		}

		$receiver = $mail['receiver'];
		$receiver = str_replace( $_keys, $_values, $receiver );
		$receiver = self::getReceiver( $receiver );

		$senderEmail = $mail['senderEmail'];
		$senderEmail = str_replace( $_keys, $_values, $senderEmail );
		$senderEmail = self::getSenderEmail( $senderEmail );

		$senderName = esc_attr( $mail['senderName'] );
		$senderName = str_replace( $_keys, $_values, $senderName );
		$senderName = self::getSenderName( $senderName );

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
		$html .= esc_html__( 'In the following fields, you can use these system option tags:', 'dialog-contact-form' );
		$html .= '</p>';
		$html .= '<p>';
		foreach ( $this->system_fields as $_field_placeholder => $value ) {
			$html .= '<code class="mailtag code">' . esc_attr( $_field_placeholder ) . '</code>';
		}
		$html .= '</p>';
		$html .= '<p class="description">';
		$html .= esc_html__( 'You can also use these field tags:', 'dialog-contact-form' );
		$html .= '</p>';
		$html .= '<p>';
		foreach ( $name_placeholders as $name_placeholder ) {
			$html .= '<code class="mailtag code">' . esc_attr( $name_placeholder ) . '</code>';
		}
		$html .= '</p>';
		$html .= '<hr>';

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