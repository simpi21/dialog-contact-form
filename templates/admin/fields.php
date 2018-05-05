<?php

use DialogContactForm\Supports\Metabox;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<button id="addFormField" class="button button-default">
	<?php esc_html_e( 'Add Field', 'dialog-contact-form' ); ?>
</button>
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

	foreach ( $_fields as $_field_number => $_field ) {
		$is_required_field = 'off';
		if ( is_array( $_field['validation'] ) && in_array( 'required', $_field['validation'] ) ) {
			$is_required_field = 'on';
		}
		if ( isset( $_field['required_field'] ) && in_array( $_field['required_field'], array( 'on', 'off' ) ) ) {
			$is_required_field = $_field['required_field'];
		}
		?>
        <div data-id="closed" class="dcf-toggle dcf-toggle--normal">
            <span class="dcf-toggle-title">
                <?php esc_html_e( $_field['field_title'] ); ?>
            </span>
            <div class="dcf-toggle-inner">
                <div class="dcf-toggle-content">
                    <p>
                        <button class="button deleteField">
							<?php esc_html_e( 'Delete this field', 'dialog-contact-form' ); ?>
                        </button>
                    </p>
					<?php
					Metabox::text( array(
						'id'          => 'field_title',
						'group'       => 'field',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'input_class' => 'dcf-input-text dcf-field-title',
						'label'       => __( 'Field Title', 'dialog-contact-form' ),
						'description' => __( 'Insert the title for the field.', 'dialog-contact-form' ),
						'default'     => $_field['field_title'],
					) );
					Metabox::text( array(
						'id'          => 'field_id',
						'group'       => 'field',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'input_class' => 'dcf-input-text dcf-field-id',
						'label'       => __( 'Field ID', 'dialog-contact-form' ),
						'description' => __( 'REQUIRED: Field identification name to be entered into email body. Note: Use only lowercase characters, hyphens and underscores.', 'dialog-contact-form' ),
						'default'     => $_field['field_id'],
					) );
					Metabox::select( array(
						'id'          => 'field_type',
						'group'       => 'field',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'label'       => __( 'Field Type', 'dialog-contact-form' ),
						'description' => __( 'Select the type for this field.', 'dialog-contact-form' ),
						'input_class' => 'select2 dcf-field-type dcf-input-text',
						'options'     => dcf_available_field_types(),
						'default'     => $_field['field_type'],
					) );
					Metabox::textarea( array(
						'id'          => 'options',
						'group'       => 'field',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'label'       => __( 'Add options', 'dialog-contact-form' ),
						'description' => __( 'One option per line.', 'dialog-contact-form' ),
						'group_class' => 'dcf-input-group col-addOptions',
						'default'     => $_field['options'],
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
					Metabox::number_options( array(
						'group'       => 'field',
						'group_class' => 'dcf-input-group col-numberOption',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'label'       => __( 'Number Option', 'dialog-contact-form' ),
						'description' => __( 'This fields is optional but you can set min value, max value and step. For allowing decimal values set step value (e.g. step="0.01" to allow decimals to two decimal places).', 'dialog-contact-form' ),
						'condition'   => array(
							'action' => 'show',
							'rules'  => array(
								array(
									'meta_key'   => '_contact_form_fields',
									'meta_value' => 'number',
									'compare'    => '=',
								)
							),
						),
					) );
					Metabox::text( array(
						'id'          => 'field_value',
						'group'       => 'field',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'label'       => __( 'Default Value', 'dialog-contact-form' ),
						'description' => __( 'Define field default value.', 'dialog-contact-form' ),
						'default'     => $_field['field_value'],
					) );
					Metabox::buttonset( array(
						'id'          => 'required_field',
						'group'       => 'field',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'label'       => __( 'Required Field', 'dialog-contact-form' ),
						'description' => __( 'Check this option to mark the field required. A form will not submit unless all required fields are provided.', 'dialog-contact-form' ),
						'default'     => $is_required_field,
						'options'     => array(
							'off' => esc_html__( 'No', 'dialog-contact-form' ),
							'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
						),
					) );
					Metabox::text( array(
						'id'          => 'field_class',
						'group'       => 'field',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'label'       => __( 'Field Class', 'dialog-contact-form' ),
						'description' => __( 'Insert additional class(es) (separated by blank space) for more personalization.', 'dialog-contact-form' ),
						'default'     => $_field['field_class'],
					) );
					Metabox::select( array(
						'id'          => 'field_width',
						'group'       => 'field',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'label'       => __( 'Field Width', 'dialog-contact-form' ),
						'description' => __( 'Set field length.', 'dialog-contact-form' ),
						'default'     => $_field['field_width'],
						'options'     => array(
							'is-12' => esc_html__( 'Full', 'dialog-contact-form' ),
							'is-9'  => esc_html__( 'Three Quarters', 'dialog-contact-form' ),
							'is-8'  => esc_html__( 'Two Thirds', 'dialog-contact-form' ),
							'is-6'  => esc_html__( 'Half', 'dialog-contact-form' ),
							'is-4'  => esc_html__( 'One Third', 'dialog-contact-form' ),
							'is-3'  => esc_html__( 'One Quarter', 'dialog-contact-form' ),
						),
					) );
					Metabox::checkbox( array(
						'id'       => 'validation',
						'group'    => 'field',
						'position' => $_field_number,
						'meta_key' => '_contact_form_fields',
						'label'    => __( 'Validation', 'dialog-contact-form' ),
						'options'  => dcf_validation_rules(),
						'default'  => $_field['validation'],
					) );
					Metabox::text( array(
						'id'          => 'placeholder',
						'group'       => 'field',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'input_class' => 'dcf-input-text dcf-field-placeholder',
						'label'       => __( 'Placeholder Text', 'dialog-contact-form' ),
						'description' => __( 'Insert placeholder message.', 'dialog-contact-form' ),
						'default'     => $_field['placeholder'],
					) );
					Metabox::text( array(
						'id'          => 'error_message',
						'group'       => 'field',
						'position'    => $_field_number,
						'meta_key'    => '_contact_form_fields',
						'label'       => __( 'Error Message', 'dialog-contact-form' ),
						'description' => __( 'Insert the error message for validation. The length of message must be 10 characters or more. Leave blank for default message.', 'dialog-contact-form' ),
						'default'     => $_field['error_message'],
					) );
					?>
                </div>
            </div>
        </div>
	<?php } ?>
</div>
