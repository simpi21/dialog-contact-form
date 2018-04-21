<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $post;
$defaults = dcf_default_mail_template();
$mail     = get_post_meta( $post->ID, '_contact_form_mail', true );
$mail     = wp_parse_args( $mail, $defaults );

?>
    <h1><?php esc_attr_e( 'Mail', 'dialog-contact-form' ); ?></h1>
    <p class="description"><?php esc_html_e( 'In the following fields, you can use these mail-tags::', 'dialog-contact-form' ); ?></p>
<?php
$fields = get_post_meta( $post->ID, '_contact_form_fields', true );
$fields = is_array( $fields ) ? $fields : array();
if ( ! isset( $_GET['action'] ) && count( $fields ) < 1 ) {
	$fields = dcf_default_fields();
}

$name_ph = array();
foreach ( $fields as $field ) {
	if ( 'file' == $field['field_type'] ) {
		continue;
	}
	$name_ph[] = $field['field_name'];
}

$name_ph = "<code class='mailtag code'>" . implode( "</code><code class='mailtag code'>", $name_ph ) . "</code>";
echo $name_ph . '<br><hr>';

Dialog_Contact_Form_Metabox::text( array(
	'id'          => 'receiver',
	'group'       => 'mail',
	'meta_key'    => '_contact_form_mail',
	'label'       => __( 'Receiver(s)', 'dialog-contact-form' ),
	'description' => __( 'Define the emails used (separeted by comma) to receive emails.', 'dialog-contact-form' ),
	'default'     => $mail['receiver'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'          => 'senderEmail',
	'group'       => 'mail',
	'meta_key'    => '_contact_form_mail',
	'label'       => __( 'Sender Email', 'dialog-contact-form' ),
	'description' => __( 'Define from what email send the message.', 'dialog-contact-form' ),
	'default'     => $mail['senderEmail'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'          => 'senderName',
	'group'       => 'mail',
	'meta_key'    => '_contact_form_mail',
	'label'       => __( 'Sender Email', 'dialog-contact-form' ),
	'description' => __( 'Define the name of email that send the message.', 'dialog-contact-form' ),
	'default'     => $mail['senderName'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'          => 'subject',
	'group'       => 'mail',
	'meta_key'    => '_contact_form_mail',
	'label'       => __( 'Message Subject', 'dialog-contact-form' ),
	'description' => __( 'Define the subject of the email sent to you.', 'dialog-contact-form' ),
	'default'     => $mail['subject'],
) );
Dialog_Contact_Form_Metabox::textarea( array(
	'id'          => 'body',
	'group'       => 'mail',
	'meta_key'    => '_contact_form_mail',
	'label'       => __( 'Message Body', 'dialog-contact-form' ),
	'default'     => $mail['body'],
	'rows'        => 10,
	'input_class' => 'widefat',
) );
