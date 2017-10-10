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
        <th scope="row"><label><?php esc_html_e( 'Invalid email', 'dialog-contact-form' ); ?></label></th>
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
        <th scope="row"><label><?php esc_html_e( 'Too short', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_too_short]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_too_short'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid number', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_number]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_number'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Number too short', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[number_too_small]" type="text"
                   value="<?php echo esc_attr( $messages['number_too_small'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Number too large', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[number_too_large]" type="text"
                   value="<?php echo esc_attr( $messages['number_too_large'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid integer', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_int]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_int'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid alphabetic letters', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_alpha]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_alpha'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid alphanumeric characters', 'dialog-contact-form' ); ?></label>
        </th>
        <td>
            <input name="messages[invalid_alnum]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_alnum'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php esc_html_e( 'Invalid alphanumeric characters, dashes and underscores', 'dialog-contact-form' ); ?></label>
        </th>
        <td>
            <input name="messages[invalid_alnumdash]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_alnumdash'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid date', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_date]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_date'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid IP address', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_ip]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_ip'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid user login', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_user_login]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_user_login'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid username', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_username]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_username'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid user email', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_user_email]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_user_email'] ); ?>" class="widefat">
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Invalid reCAPTCHA', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="messages[invalid_recaptcha]" type="text"
                   value="<?php echo esc_attr( $messages['invalid_recaptcha'] ); ?>" class="widefat">
        </td>
    </tr>
</table>
