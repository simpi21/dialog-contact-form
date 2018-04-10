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

	if ( ! isset( $_GET['action'] ) && count( $_fields ) === 0 ) {
		$_fields = dcf_default_fields();
	}

	if ( count( $_fields ) < 1 ) {
		return;
	}

	foreach ( $_fields as $_field_number => $_field ) { ?>

        <div class="accordion">
            <div class="accordion-header"><?php esc_html_e( $_field['field_title'] ); ?></div>
            <div class="accordion-content">
                <p>
                    <button class="button deleteField">
						<?php esc_html_e( 'Delete this field', 'dialog-contact-form' ); ?>
                    </button>
                </p>
				<?php
				Dialog_Contact_Form_Metabox::text( array(
					'id'          => 'field_title',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Field Title', 'dialog-contact-form' ),
					'description' => __( 'Insert the title for the field.', 'dialog-contact-form' ),
				) );
				Dialog_Contact_Form_Metabox::text( array(
					'id'          => 'field_id',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Field ID', 'dialog-contact-form' ),
					'description' => __( 'REQUIRED: Field identification name to be entered into email body. Note: Use only lowercase characters, hyphens and underscores.', 'dialog-contact-form' ),
				) );
				Dialog_Contact_Form_Metabox::select( array(
					'id'          => 'field_type',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Field Type', 'dialog-contact-form' ),
					'description' => __( 'Select the type for this field.', 'dialog-contact-form' ),
					'options'     => dcf_available_field_types(),
				) );
				Dialog_Contact_Form_Metabox::textarea( array(
					'id'          => 'options',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Add options', 'dialog-contact-form' ),
					'description' => __( 'One option per line.', 'dialog-contact-form' ),
					'group_class' => 'dcf-input-group col-addOptions',
					'rows'        => 8,
					'condition'   => array(
						'action' => 'show',
						'rules'  => array(
							array(
								'meta_key'   => '_contact_form_fields',
								'meta_value' => array( 'radio', 'select', 'checkbox' ),
								'compare'    => 'IN',
							)
						),
					),
				) );
				Dialog_Contact_Form_Metabox::text( array(
					'id'          => 'field_value',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Default Value', 'dialog-contact-form' ),
					'description' => __( 'Define field default value.', 'dialog-contact-form' ),
				) );
				Dialog_Contact_Form_Metabox::text( array(
					'id'          => 'field_class',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Field Class', 'dialog-contact-form' ),
					'description' => __( 'Insert additional class(es) (separated by blank space) for more personalization.', 'dialog-contact-form' ),
				) );
				Dialog_Contact_Form_Metabox::select( array(
					'id'          => 'field_width',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Field Width', 'dialog-contact-form' ),
					'description' => __( 'Set field length.', 'dialog-contact-form' ),
					'options'     => array(
						'is-12' => esc_html__( 'Full', 'dialog-contact-form' ),
						'is-9'  => esc_html__( 'Three Quarters', 'dialog-contact-form' ),
						'is-8'  => esc_html__( 'Two Thirds', 'dialog-contact-form' ),
						'is-6'  => esc_html__( 'Half', 'dialog-contact-form' ),
						'is-4'  => esc_html__( 'One Third', 'dialog-contact-form' ),
						'is-3'  => esc_html__( 'One Quarter', 'dialog-contact-form' ),
					),
				) );
				Dialog_Contact_Form_Metabox::text( array(
					'id'          => 'placeholder',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Placeholder Text', 'dialog-contact-form' ),
					'description' => __( 'Insert placeholder message.', 'dialog-contact-form' ),
				) );
				Dialog_Contact_Form_Metabox::text( array(
					'id'          => 'error_message',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Error Message', 'dialog-contact-form' ),
					'description' => __( 'Insert the error message for validation. The length of message must be 10 characters or more. Leave blank for default message.', 'dialog-contact-form' ),
				) );
				Dialog_Contact_Form_Metabox::checkbox( array(
					'id'       => 'validation',
					'group'    => 'field',
					'position' => $_field_number,
					'meta_key' => '_contact_form_fields',
					'label'    => __( 'Validation', 'dialog-contact-form' ),
					'options'  => dcf_validation_rules(),
				) );
				?>
                <table class="form-table">
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
                </table>
            </div>
        </div>

	<?php } ?>
</div>
