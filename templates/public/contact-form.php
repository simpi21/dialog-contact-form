<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$success_message = isset( $GLOBALS['_dcf_mail_sent_ok'] ) ? $GLOBALS['_dcf_mail_sent_ok'] : null;
$error_message   = isset( $GLOBALS['_dcf_validation_error'] ) ? $GLOBALS['_dcf_validation_error'] : null;
$errors          = isset( $GLOBALS['_dcf_errors'] ) ? $GLOBALS['_dcf_errors'] : array();

// If there is no field, exist
if ( ! ( is_array( $fields ) && count( $fields ) > 0 ) ) {
	return;
}

$form = new Dialog_Contact_Form_Form( $id );
?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="dcf-form columns is-multiline"
      method="POST" accept-charset="UTF-8" enctype="multipart/form-data" novalidate>
    <div class="dcf-response">
        <div class="dcf-success">
			<?php
			if ( ! empty( $success_message ) ) {
				echo '<p>' . $success_message . '</p>';
			}
			?>
        </div>
        <div class="dcf-error">
			<?php
			if ( ! empty( $error_message ) ) {
				echo '<p>' . $error_message . '</p>';
			}
			?>
        </div>
    </div>
	<?php wp_nonce_field( '_dcf_submit_form', '_dcf_nonce' ); ?>
    <input type="hidden" name="_user_form_id" value="<?php echo $id; ?>">

	<?php
	foreach ( $fields as $_field ) {
		echo sprintf( '<div class="field column %s">', $_field['field_width'] );

		$form->label( $_field );

		echo '<p class="control">';

		switch ( $_field['field_type'] ) {
			case 'textarea':
				$form->textarea( $_field );
				break;
			case 'radio':
				$form->radio( $_field );
				break;
			case 'checkbox':
				$form->checkbox( $_field );
				break;
			case 'select':
				$form->select( $_field );
				break;
			case 'file':
				$form->file( $_field );
				break;
			case 'number':
				$form->number( $_field );
				break;
			default:
				$form->text( $_field );
				break;
		}

		// Show error message if any
		if ( isset( $errors[ $_field['field_name'] ][0] ) ) {
			echo '<span class="help is-danger">' . esc_attr( $errors[ $_field['field_name'] ][0] ) . '</span>';
		}

		echo '</p>';
		echo '</div>';
	}

	// If Google reCAPTCHA, add here
	$form->reCAPTCHA( $_options );

	// Submit button
	printf( '<div class="field column is-12"><p class="%s"><button type="submit" class="button dcf-submit">%s</button></p></div>',
		( isset( $config['btnAlign'] ) && $config['btnAlign'] == 'right' ) ? 'control level-right' : 'control level-left',
		esc_attr( $config['btnLabel'] )
	);
	?>
</form>
