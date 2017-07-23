<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$defaults = dcf_validation_messages();

global $post;
$_messages = get_post_meta( $post->ID, '_contact_form_messages', true );
$messages  = wp_parse_args( $_messages, $defaults );
?>
<table class="form-table">
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Message sent successfully', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[mail_sent_ok]" type="text"
                   value="<?php echo esc_attr( $messages['mail_sent_ok'] ); ?>" class="widefat">
            <p class="description"><?php esc_html_e( 'Message was sent successfully', 'dialog-contact-form' ); ?></p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Message failed to sent', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[mail_sent_ng]" type="text"
                   value="<?php echo esc_attr( $messages['mail_sent_ng'] ); ?>" class="widefat">
            <p class="description"><?php esc_html_e( 'Message failed to send', 'dialog-contact-form' ); ?></p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Validation errors occurred', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[validation_error]" type="text"
                   value="<?php echo esc_attr( $messages['validation_error'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Required field', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_required]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_required'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid Email', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_email]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_email'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid URL', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_url]" type="text" value="<?php echo esc_attr( $messages['invalid_url'] ); ?>"
                   class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Exceed max length', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_too_long]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_too_long'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Too Short', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_too_short]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_too_short'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Number Too Short', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[number_too_small]" type="text"
                   value="<?php echo esc_attr( $messages['number_too_small'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Number Too Large', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[number_too_large]" type="text"
                   value="<?php echo esc_attr( $messages['number_too_large'] ); ?>" class="widefat">
        </td>
    </tr>
</table>
