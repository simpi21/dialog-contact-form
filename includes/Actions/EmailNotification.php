<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Action;
use DialogContactForm\Supports\Mailer;
use DialogContactForm\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EmailNotification extends Action {

	/**
	 * Hold value from WordPress option
	 *
	 * @var array
	 */
	private static $system_fields = array();

	/**
	 * Email constructor.
	 */
	public function __construct() {
		$this->priority      = 20;
		$this->id            = 'email_notification';
		$this->title         = __( 'Email Notification', 'dialog-contact-form' );
		$this->meta_group    = 'email_notification';
		$this->meta_key      = '_contact_form_mail';
		$this->settings      = array_merge( $this->settings, $this->settings() );
		self::$system_fields = array(
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
	 * @param \DialogContactForm\Config $config
	 * @param array $data
	 *
	 * @return bool
	 */
	public static function process( $config, $data ) {
		$attachments     = array();
		$mail            = get_post_meta( $config->getFormId(), '_contact_form_mail', true );
		$hide_from_email = apply_filters( 'dialog_contact_form/email/hidden_fields',
			array( 'hidden', 'html', 'divider' ) );

		$form_fields = array();
		foreach ( $config->getFormFields() as $field ) {
			if ( in_array( $field['field_type'], $hide_from_email ) ) {
				continue;
			}

			$value = isset( $data[ $field['field_name'] ] ) ? $data[ $field['field_name'] ] : '';

			if ( 'file' === $field['field_type'] && is_array( $value ) ) {
				foreach ( $value as $attachment ) {
					if ( ! empty( $attachment['attachment_path'] ) ) {
						$attachments[] = $attachment['attachment_path'];
					}
				}
				continue;
			}

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

		$_keys   = array_merge( array_keys( self::$system_fields ),
			Utils::array_column( $form_fields, 'placeholder' ) );
		$_values = array_merge( array_values( self::$system_fields ),
			Utils::array_column( $form_fields, 'value' ) );

		$subject = $mail['subject'];
		$subject = str_replace( $_keys, $_values, $subject );

		$body    = $mail['body'];
		$body    = str_replace( $_keys, $_values, $body );
		$body    = str_replace( array( "\r\n", "\r", "\n" ), "<br>", $body );
		$body    = str_replace( '[all_fields_table]', self::all_fields_table( $form_fields ), $body );
		$message = self::get_email_head();
		$message .= stripslashes( wp_kses_post( $body ) );
		$message .= self::get_email_footer();

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
		return array(
			'receiver'    => array(
				'type'        => 'text',
				'id'          => 'receiver',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Receiver(s)', 'dialog-contact-form' ),
				'description' => __( 'Define the emails used (separated by comma) to receive emails.',
					'dialog-contact-form' ),
				'default'     => '[system:admin_email]',
			),
			'senderEmail' => array(
				'type'        => 'text',
				'id'          => 'senderEmail',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Sender Email', 'dialog-contact-form' ),
				'description' => __( 'Define from what email send the message.', 'dialog-contact-form' ),
				'default'     => '[your_email]',
			),
			'senderName'  => array(
				'type'        => 'text',
				'id'          => 'senderName',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Sender Name', 'dialog-contact-form' ),
				'description' => __( 'Define the sender name that send the message.', 'dialog-contact-form' ),
				'default'     => '[your_name]',
			),
			'subject'     => array(
				'type'        => 'text',
				'id'          => 'subject',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Message Subject', 'dialog-contact-form' ),
				'description' => __( 'Define the subject of the email sent to you.', 'dialog-contact-form' ),
				'default'     => '[system:blogname] : [subject]',
			),
			'body'        => array(
				'type'        => 'textarea',
				'id'          => 'body',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Message Body', 'dialog-contact-form' ),
				'description' => sprintf(
					__( 'Use mail-tags or enter %s for including all fields.', 'dialog-contact-form' ),
					'<strong>[all_fields_table]</strong>'
				),
				'rows'        => 10,
				'input_class' => 'widefat',
				'sanitize'    => array( 'DialogContactForm\\Supports\\Sanitize', 'html' ),
				'default'     => '[all_fields_table]',
			),
		);
	}

	/**
	 * Generate table using user submitted data
	 *
	 * @param array $form_fields
	 *
	 * @return string
	 */
	private static function all_fields_table( $form_fields ) {
		ob_start();
		?>
        <table style="width: auto; min-width: 300px; max-width: 600px; margin: 0 auto; padding: 0;" align="center"
               width="600"
               cellpadding="0" cellspacing="0">
			<?php foreach ( $form_fields as $all_field ) {
				$value = str_replace( array( "\r\n", "\r", "\n" ), "<br>", $all_field['value'] );
				?>
                <tr>
                    <td style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;background: #F2F4F6; font-weight: bold; padding: 8px 10px;">
						<?php echo $all_field['label']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;padding: 8px 10px 35px;">
						<?php echo $value; ?>
                    </td>
                </tr>
			<?php } ?>
        </table>
		<?php
		return ob_get_clean();
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
		$fields           = get_post_meta( $post->ID, '_contact_form_fields', true );
		$fields           = is_array( $fields ) ? $fields : array();
		$black_list_types = array( 'file', 'html', 'hidden' );

		$name_placeholders = array();
		foreach ( $fields as $field ) {
			if ( empty( $field['field_name'] ) ) {
				continue;
			}
			if ( in_array( $field['field_type'], $black_list_types ) ) {
				continue;
			}
			$name_placeholders[] = '[' . $field['field_name'] . ']';
		}

		$html = '<p class="description">';
		$html .= esc_html__( 'In the following fields, you can use these system option tags:', 'dialog-contact-form' );
		$html .= '</p>';
		$html .= '<p>';
		foreach ( self::$system_fields as $_field_placeholder => $value ) {
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

	private static function get_email_head() {
		ob_start();
		include_once DIALOG_CONTACT_FORM_TEMPLATES . '/emails/email-head.php';

		return ob_get_clean();
	}

	private static function get_email_footer() {
		ob_start();
		include_once DIALOG_CONTACT_FORM_TEMPLATES . '/emails/email-footer.php';

		return ob_get_clean();
	}
}
