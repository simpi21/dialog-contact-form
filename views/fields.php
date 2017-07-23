<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<button id="addFormField" class="button button-default">Add Field</button>
<div id="shaplaFieldList">

	<?php
	global $post;
	$_fields = get_post_meta( $post->ID, '_contact_form_fields', true );
	$_fields = is_array( $_fields ) ? $_fields : array();

	if ( count( $_fields ) > 0 ):

		$_field_number = 0;
		foreach ( $_fields as $_field ): ?>

            <div class="accordion">
                <div class="accordion-header"><?php esc_html_e( $_field['field_title'] ); ?></div>
                <div class="accordion-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Field Title', 'dialog-contact-form' ); ?></label>
                            </th>
                            <td>
                                <input name="field[field_title][]" type="text"
                                       value="<?php echo esc_attr( $_field['field_title'] ); ?>" class="regular-text">
                                <p class="description"><?php esc_html_e( 'Insert the title for the field.', 'dialog-contact-form' ); ?></p>
                            </td>
                        </tr><!-- Field Title -->
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Field ID', 'dialog-contact-form' ) ?></label></th>
                            <td>
                                <input name="field[field_id][]" type="text"
                                       value="<?php echo esc_attr( $_field['field_id'] ); ?>" class="regular-text"
                                       required="required">
                                <p class="description"><?php esc_html_e( 'REQUIRED: Field identification name to be entered into email body.', 'dialog-contact-form' ); ?></p>
                                <p class="description"><?php esc_html_e( 'Note: Use only lowercase characters, hyphens and underscores.', 'dialog-contact-form' ); ?></p>
                            </td>
                        </tr><!-- Field ID -->
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Field Type', 'dialog-contact-form' ); ?></label>
                            </th>
                            <td>
                                <select name="field[field_type][]" class="regular-text" required="required">
									<?php
									$fieldType = dcf_available_field_types();
									foreach ( $fieldType as $key => $value ) {
										$selected = ( $key == $_field['field_type'] ) ? 'selected' : '';
										echo sprintf( '<option value="%s" %s>%s</option>', $key, $selected, $value );
									}
									?>
                                </select>
                                <p class="description"><?php esc_html_e( 'Select the type for this field.', 'dialog-contact-form' ); ?></p>
                            </td>
                        </tr><!-- Field Type -->
                        <tr class="col-addOptions" style="<?php if ( in_array( $_field['field_type'], array(
							'checkbox',
							'radio',
							'select'
						) ) ) {
							echo 'display: table-row';
						} ?>">
                            <th scope="row"><label><?php esc_html_e( 'Add options', 'dialog-contact-form' ); ?></label>
                            </th>
                            <td>
                                <textarea name="field[options][]" rows="8"
                                          class="regular-text"><?php echo esc_textarea( $_field['options'] ); ?></textarea>
                                <p class="description"><?php esc_html_e( 'One option per line.', 'dialog-contact-form' ); ?></p>
                            </td>
                        </tr><!-- Add options -->
                        <tr class="col-numberOption" style="<?php if ( $_field['field_type'] == 'number' ) {
							echo 'display: table-row';
						} ?>">
                            <th scope="row">
                                <label><?php esc_html_e( 'Number Option', 'dialog-contact-form' ); ?></label></th>
                            <td>
                                <label>
									<?php esc_html_e( 'Min Value:', 'dialog-contact-form' ); ?>
                                    <input type="number" name="field[number_min][]"
                                           value="<?php echo esc_attr( $_field['number_min'] ); ?>"
                                           step="0.01" class="small-text"></label>
                                <label>
									<?php esc_html_e( 'Max Value:', 'dialog-contact-form' ); ?>
                                    <input type="number" name="field[number_max][]"
                                           value="<?php echo esc_attr( $_field['number_max'] ); ?>"
                                           step="0.01" class="small-text"></label>
                                <label>
									<?php esc_html_e( 'Step:', 'dialog-contact-form' ); ?>
                                    <input type="number" name="field[number_step][]"
                                           value="<?php echo esc_attr( $_field['number_step'] ); ?>"
                                           step="0.01" class="small-text"></label>
                                <p class="description"><?php esc_html_e( 'This fields is optional but you can set min value, max value and
                            step. For allowing decimal values set step value (e.g. step="0.01" to allow decimals to two decimal places).', 'dialog-contact-form' ); ?></p>
                            </td>
                        </tr><!-- Number Option -->
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Default Value', 'dialog-contact-form' ); ?></label></th>
                            <td>
                                <input name="field[field_value][]" type="text"
                                       value="<?php echo esc_attr( $_field['field_value'] ); ?>" class="regular-text">
                                <p class="description"><?php esc_html_e( 'Define field default value.', 'dialog-contact-form' ); ?></p>
                            </td>
                        </tr><!-- Default Value -->
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Field Class', 'dialog-contact-form' ); ?></label>
                            </th>
                            <td>
                                <input name="field[field_class][]" type="text"
                                       value="<?php echo esc_attr( $_field['field_class'] ); ?>" class="regular-text">
                                <p class="description"><?php esc_html_e( 'Insert additional class(es) (separated by blank space) for more
                            personalization.', 'dialog-contact-form' ); ?></p>
                            </td>
                        </tr><!-- Class -->
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Field Width', 'dialog-contact-form' ); ?></label>
                            </th>
                            <td>
                                <select name="field[field_width][]" class="regular-text" required="required">
									<?php
									$fieldWidth = array(
										'is-12' => esc_html__( 'Full', 'dialog-contact-form' ),
										'is-9'  => esc_html__( 'Three Quarters', 'dialog-contact-form' ),
										'is-8'  => esc_html__( 'Two Thirds', 'dialog-contact-form' ),
										'is-6'  => esc_html__( 'Half', 'dialog-contact-form' ),
										'is-4'  => esc_html__( 'One Third', 'dialog-contact-form' ),
										'is-3'  => esc_html__( 'One Quarter', 'dialog-contact-form' ),
									);
									foreach ( $fieldWidth as $key => $value ) {
										$selected = ( $key == $_field['field_width'] ) ? 'selected' : '';
										echo sprintf( '<option value="%s" %s>%s</option>', $key, $selected, $value );
									}
									?>
                                </select>
                                <p class="description"><?php esc_html_e( 'Set field length.', 'dialog-contact-form' ); ?></p>
                            </td>
                        </tr><!-- Width -->
                        <tr>
                            <th scope="row"><label>Validation</label></th>
                            <td>
								<?php
								$validationField = dcf_validation_rules();
								foreach ( $validationField as $key => $value ) {
									$checked = in_array( $key, $_field['validation'] ) ? 'checked' : '';
									echo sprintf( '<label><input type="checkbox" class="input-validate" name="field[validation][%3$s][]" value="%1$s" %4$s>%2$s </label>',
										$key, $value, $_field_number, $checked );
								}
								?>
                            </td>
                        </tr><!-- Required -->
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Placeholder Text', 'dialog-contact-form' ); ?></label></th>
                            <td>
                                <input name="field[placeholder][]" type="text"
                                       value="<?php echo esc_attr( $_field['placeholder'] ); ?>" class="regular-text">
                                <p class="description"><?php esc_html_e( 'Insert placeholder message.', 'dialog-contact-form' ); ?></p>
                            </td>
                        </tr><!-- Placeholder -->
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Error Message', 'dialog-contact-form' ); ?></label></th>
                            <td>
                                <input name="field[error_message][]" type="text"
                                       value="<?php echo esc_attr( $_field['error_message'] ); ?>" class="regular-text">
                                <p class="description"><?php esc_html_e( 'Insert the error message for validation. The length of message must be 10
                            characters or more. Leave blank for default message.', 'dialog-contact-form' ); ?></p>
                            </td>
                        </tr><!-- Error Message -->
                    </table>
                    <p>
                        <button class="button deleteField"><?php esc_html_e( 'Delete this field', 'dialog-contact-form' ); ?></button>
                    </p>
                </div>
            </div>

			<?php $_field_number ++; endforeach; endif; ?>
</div>
