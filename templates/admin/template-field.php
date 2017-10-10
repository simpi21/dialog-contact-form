<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<template style="display: none" id="shaplaFieldTemplate">
    <div class="accordion">
        <div class="accordion-header"><?php esc_html_e( 'Untitled', 'dialog-contact-form' ); ?></div>
        <div class="accordion-content">
            <p>
                <button class="button deleteField"><?php esc_html_e( 'Delete this field', 'dialog-contact-form' ); ?></button>
            </p>
            <table class="form-table">
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Field Title', 'dialog-contact-form' ); ?></label></th>
                    <td>
                        <input name="field[field_title][]" type="text" value="" class="regular-text" required="required"
                               autocomplete="off">
                        <p class="description"><?php esc_html_e( 'Insert the title for the field.', 'dialog-contact-form' ); ?></p>
                    </td>
                </tr><!-- Field Title -->
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Field ID', 'dialog-contact-form' ) ?></label></th>
                    <td>
                        <input name="field[field_id][]" type="text" value="" class="regular-text" required="required"
                               autocomplete="off">
                        <p class="description"><?php esc_html_e( 'REQUIRED: Field identification name to be entered into email body.', 'dialog-contact-form' ); ?></p>
                        <p class="description"><?php esc_html_e( 'Note: Use only lowercase characters, hyphens and underscores.', 'dialog-contact-form' ); ?></p>
                    </td>
                </tr><!-- Field ID -->
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Field Type', 'dialog-contact-form' ); ?></label></th>
                    <td>
                        <select name="field[field_type][]" class="regular-text" required="required">
							<?php
							$fieldType = dcf_available_field_types();
							foreach ( $fieldType as $key => $value ) {
								echo sprintf( '<option value="%s">%s</option>', $key, $value );
							}
							?>
                        </select>
                        <p class="description"><?php esc_html_e( 'Select the type for this field.', 'dialog-contact-form' ); ?></p>
                    </td>
                </tr><!-- Field Type -->
                <tr class="col-addOptions">
                    <th scope="row"><label><?php esc_html_e( 'Add options', 'dialog-contact-form' ); ?></label></th>
                    <td>
                        <textarea class="regular-text" name="field[options][]" rows="8" cols="80"></textarea>
                        <p class="description"><?php esc_html_e( 'One option per line.', 'dialog-contact-form' ); ?></p>
                    </td>
                </tr><!-- Add options -->
                <tr class="col-numberOption">
                    <th scope="row"><label><?php esc_html_e( 'Number Option', 'dialog-contact-form' ); ?></label></th>
                    <td>
                        <label>
							<?php esc_html_e( 'Min Value:', 'dialog-contact-form' ); ?>
                            <input type="number" name="field[number_min]" value="" step="0.01" class="small-text">
                        </label>
                        <label>
							<?php esc_html_e( 'Max Value:', 'dialog-contact-form' ); ?>
                            <input type="number" name="field[number_max]" value="" step="0.01" class="small-text">
                        </label>
                        <label>
							<?php esc_html_e( 'Step:', 'dialog-contact-form' ); ?>
                            <input type="number" name="field[number_step]" value="" step="0.01" class="small-text">
                        </label>
                        <p class="description"><?php esc_html_e( 'This fields is optional but you can set min value, max value and
                            step. For allowing decimal values set step value (e.g. step="0.01" to allow decimals to two decimal places).', 'dialog-contact-form' ); ?></p>
                    </td>
                </tr><!-- Number Option -->
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Default Value', 'dialog-contact-form' ); ?></label></th>
                    <td>
                        <input name="field[field_value][]" type="text" value="" class="regular-text" autocomplete="off">
                        <p class="description"><?php esc_html_e( 'Define field default value.', 'dialog-contact-form' ); ?></p>
                    </td>
                </tr><!-- Default Value -->
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Field Class', 'dialog-contact-form' ); ?></label></th>
                    <td>
                        <input name="field[field_class][]" type="text" value="" class="regular-text" autocomplete="off">
                        <p class="description"><?php esc_html_e( 'Insert additional class(es) (separated by blank space) for more
                            personalization.', 'dialog-contact-form' ); ?></p>
                    </td>
                </tr><!-- Field Class -->
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Field Width', 'dialog-contact-form' ); ?></label></th>
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
								echo sprintf( '<option value="%s">%s</option>', $key, $value );
							}
							?>
                        </select>
                        <p class="description"><?php esc_html_e( 'Set field length.', 'dialog-contact-form' ); ?></p>
                    </td>
                </tr><!-- Field Width -->
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Validation', 'dialog-contact-form' ); ?></label></th>
                    <td>
						<?php
						$validationField = dcf_validation_rules();
						foreach ( $validationField as $key => $value ) {
							echo sprintf( '<label><input type="checkbox" class="input-validate" name="field[validation][][]" value="%s">%s </label>', $key, $value );
						}
						?>
                    </td>
                </tr><!-- Validation -->
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Placeholder Text', 'dialog-contact-form' ); ?></label>
                    </th>
                    <td>
                        <input name="field[placeholder][]" type="text" value="" class="regular-text" autocomplete="off">
                        <p class="description"><?php esc_html_e( 'Insert placeholder message.', 'dialog-contact-form' ); ?></p>
                    </td>
                </tr><!-- Placeholder Text -->
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Error Message', 'dialog-contact-form' ); ?></label></th>
                    <td>
                        <input name="field[error_message][]" type="text" value="" class="regular-text"
                               autocomplete="off">
                        <p class="description"><?php esc_html_e( 'Insert the error message for validation. The length of message must be 10
                            characters or more. Leave blank for default message.', 'dialog-contact-form' ); ?></p>
                    </td>
                </tr><!-- Error Message -->
            </table>
        </div>
    </div>
</template>
