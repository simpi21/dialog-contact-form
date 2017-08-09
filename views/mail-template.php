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
$fields     = get_post_meta( $post->ID, '_contact_form_fields', true );
$fields     = is_array( $fields ) ? $fields : array();
$field_name = array_column( $fields, 'field_name' );
if ( ! isset( $_GET['action'] ) && count( $field_name ) === 0 ) {
	$field_name = array( 'your_name', 'your_email', 'subject', 'your_message' );
}
$name_ph = array_map( function ( $n ) {
	return "[" . $n . "]";
}, $field_name );
$name_ph = "<code class='mailtag code'>" . implode( "</code><code class='mailtag code'>", $name_ph ) . "</code>";
echo $name_ph;
?>
<table class="form-table">
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Receiver(s)', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="mail[receiver]" type="text" value="<?php echo esc_attr( $mail['receiver'] ); ?>"
                   class="regular-text">
            <p class="description"><?php esc_html_e( 'Define the emails used (separeted by comma) to receive emails.', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Receiver(s) -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Sender Email', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="mail[senderEmail]" type="email" value="<?php echo esc_attr( $mail['senderEmail'] ); ?>"
                   class="regular-text" required="required">
            <p class="description"><?php esc_html_e( 'Define from what email send the message.', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Sender Email -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Sender Name', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="mail[senderName]" type="text" value="<?php echo esc_attr( $mail['senderName'] ); ?>"
                   class="regular-text" required="required">
            <p class="description"><?php esc_html_e( 'Define the name of email that send the message.', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Sender Name -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Message Subject', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="mail[subject]" type="text" value="<?php echo esc_attr( $mail['subject'] ); ?>"
                   class="regular-text" required="required">
            <p class="description"><?php esc_html_e( 'Define the subject of the email sent to you.', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Subject -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Message Body', 'dialog-contact-form' ); ?></label></th>
        <td>
            <textarea name="mail[body]" cols="30" rows="10" class="widefat"
                      required="required"><?php echo esc_textarea( $mail['body'] ); ?></textarea>
        </td>
    </tr><!-- Body -->
</table>
