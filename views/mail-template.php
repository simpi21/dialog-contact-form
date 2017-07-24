<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $post;
$defaults = dcf_default_configuration();
$_config  = get_post_meta( $post->ID, '_contact_form_config', true );
$config   = wp_parse_args( $_config, $defaults );

?>
<table class="form-table">
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Receiver(s)', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="config[receiver]" type="text" value="<?php echo esc_attr( $config['receiver'] ); ?>"
                   class="regular-text">
            <p class="description"><?php esc_html_e( 'Define the emails used (separeted by comma) to receive emails.', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Receiver(s) -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Sender Email', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="config[senderEmail]" type="email" value="<?php echo esc_attr( $config['senderEmail'] ); ?>"
                   class="regular-text" required="required">
            <p class="description"><?php esc_html_e( 'Define from what email send the message.', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Sender Email -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Sender Name', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="config[senderName]" type="text" value="<?php echo esc_attr( $config['senderName'] ); ?>"
                   class="regular-text" required="required">
            <p class="description"><?php esc_html_e( 'Define the name of email that send the message.', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Sender Name -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Message Subject', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="config[subject]" type="text" value="<?php echo esc_attr( $config['subject'] ); ?>"
                   class="regular-text" required="required">
            <p class="description"><?php esc_html_e( 'Define the subject of the email sent to you.', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Subject -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Message Body', 'dialog-contact-form' ); ?></label></th>
        <td>
            <textarea name="config[body]" cols="30" rows="10" class="widefat"
                      required="required"><?php echo esc_textarea( $config['body'] ); ?></textarea>
            <p class="description"><?php printf(
					esc_html__( 'Define the body of the email sent to you. In %1$sMessage Subject%2$s and
                %1$sMessage Body%2$s fields, you can use these mail-tags:', 'dialog-contact-form' ),
					'<strong>',
					'</strong>'
				) ?></p>
			<?php
			$fields     = get_post_meta( $post->ID, '_contact_form_fields', true );
			$fields     = is_array( $fields ) ? $fields : array();
			$field_name = array_column( $fields, 'field_name' );
			$name_ph    = array_map( function ( $n ) {
				return "%" . $n . "%";
			}, $field_name );
			$name_ph    = "<code class='mailtag code'>" . implode( "</code><code class='mailtag code'>", $name_ph ) . "</code>";
			echo $name_ph;
			?>
        </td>
    </tr><!-- Body -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Position of the field title', 'dialog-contact-form' ); ?></label></th>
        <td>
            <select name="config[labelPosition]" class="regular-text">
				<?php
				$labelPosition = array(
					'label'       => esc_html__( 'Label', 'dialog-contact-form' ),
					'placeholder' => esc_html__( 'Placeholder', 'dialog-contact-form' ),
					'both'        => esc_html__( 'Both label and placeholder', 'dialog-contact-form' ),
				);
				foreach ( $labelPosition as $key => $value ) {
					$selected = ( $config['labelPosition'] == $key ) ? 'selected' : '';
					echo sprintf( '<option value="%1$s" %3$s>%2$s</option>',
						esc_attr( $key ),
						esc_attr( $value ),
						$selected
					);
				}
				?>
            </select>
            <p class="description"><?php esc_html_e( 'Select the position of the field title', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Position of the field title -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Submit Button Label', 'dialog-contact-form' ); ?></label></th>
        <td>
            <input name="config[btnLabel]" type="text" value="<?php echo esc_attr( $config['btnLabel'] ); ?>"
                   class="regular-text">
            <p class="description"><?php esc_html_e( 'Define the label of submit button.', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Submit Button Label -->
    <tr>
        <th scope="row"><label><?php esc_html_e( 'Submit Button Alignment', 'dialog-contact-form' ); ?></label></th>
        <td>
            <select name="config[btnAlign]" class="regular-text" required="required">
				<?php
				$btnAlign = array(
					'left'  => esc_html__( 'Left', 'dialog-contact-form' ),
					'right' => esc_html__( 'Right', 'dialog-contact-form' ),
				);
				foreach ( $btnAlign as $key => $value ) {
					$selected = ( $config['btnAlign'] == $key ) ? 'selected' : '';
					echo sprintf( '<option value="%1$s" %3$s>%2$s</option>',
						esc_attr( $key ),
						esc_attr( $value ),
						$selected
					);
				}
				?>
            </select>
            <p class="description"><?php esc_html_e( 'Set the alignment of submit button.', 'dialog-contact-form' ); ?></p>
        </td>
    </tr><!-- Submit Button Alignment -->
</table>
