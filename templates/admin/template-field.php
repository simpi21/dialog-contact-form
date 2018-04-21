<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$_field_number = 100;
?>

<template style="display: none" id="shaplaFieldTemplate">
    <div data-id="closed" class="dcf-toggle dcf-toggle--normal">
            <span class="dcf-toggle-title">
                <?php esc_html_e( 'Untitled', 'dialog-contact-form' ); ?>
            </span>
        <div class="dcf-toggle-inner">
            <div class="dcf-toggle-content">
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
					'input_class' => 'dcf-input-text dcf-field-title',
					'label'       => __( 'Field Title', 'dialog-contact-form' ),
					'description' => __( 'Insert the title for the field.', 'dialog-contact-form' ),
				) );
				Dialog_Contact_Form_Metabox::text( array(
					'id'          => 'field_id',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'input_class' => 'dcf-input-text dcf-field-id',
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
					'input_class' => 'select2 dcf-field-type dcf-input-text',
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
				Dialog_Contact_Form_Metabox::number_options( array(
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
				Dialog_Contact_Form_Metabox::checkbox( array(
					'id'       => 'validation',
					'group'    => 'field',
					'position' => $_field_number,
					'meta_key' => '_contact_form_fields',
					'label'    => __( 'Validation', 'dialog-contact-form' ),
					'options'  => dcf_validation_rules(),
				) );
				Dialog_Contact_Form_Metabox::text( array(
					'id'          => 'placeholder',
					'group'       => 'field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'input_class' => 'dcf-input-text dcf-field-placeholder',
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
				?>
            </div>
        </div>
    </div>
</template>
