<?php

use DialogContactForm\Supports\Metabox;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $post;
$defaults = dcf_default_mail_template();
$mail     = get_post_meta( $post->ID, '_contact_form_mail', true );
$mail     = wp_parse_args( $mail, $defaults );

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

?>
<div data-id="closed" class="dcf-toggle dcf-toggle--normal">
    <span class="dcf-toggle-title">
                <?php esc_attr_e( 'Email Notification', 'dialog-contact-form' ); ?>
            </span>
    <div class="dcf-toggle-inner">
        <div class="dcf-toggle-content">
            <p class="description"><?php esc_html_e( 'In the following fields, you can use these mail-tags:', 'dialog-contact-form' ); ?></p>
			<?php

			$name_placeholders = "<code class='mailtag code'>" . implode( "</code><code class='mailtag code'>", $name_placeholders ) . "</code>";
			echo $name_placeholders . '<br><hr>';

			Metabox::text( array(
				'id'          => 'receiver',
				'group'       => 'mail',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Receiver(s)', 'dialog-contact-form' ),
				'description' => __( 'Define the emails used (separeted by comma) to receive emails.', 'dialog-contact-form' ),
				'default'     => $mail['receiver'],
			) );
			Metabox::text( array(
				'id'          => 'senderEmail',
				'group'       => 'mail',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Sender Email', 'dialog-contact-form' ),
				'description' => __( 'Define from what email send the message.', 'dialog-contact-form' ),
				'default'     => $mail['senderEmail'],
			) );
			Metabox::text( array(
				'id'          => 'senderName',
				'group'       => 'mail',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Sender Name', 'dialog-contact-form' ),
				'description' => __( 'Define the sender name that send the message.', 'dialog-contact-form' ),
				'default'     => $mail['senderName'],
			) );
			Metabox::text( array(
				'id'          => 'subject',
				'group'       => 'mail',
				'meta_key'    => '_contact_form_mail',
				'label'       => __( 'Message Subject', 'dialog-contact-form' ),
				'description' => __( 'Define the subject of the email sent to you.', 'dialog-contact-form' ),
				'default'     => $mail['subject'],
			) );
			Metabox::textarea( array(
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
				'default'     => $mail['body'],
			) );
			?>
        </div>
    </div>
</div>
