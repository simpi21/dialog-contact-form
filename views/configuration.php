<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $post;
$defaults = [
	'labelPosition' => 'both',
	'btnAlign'      => 'left',
	'btnLabel'      => esc_html__( 'Send', 'dialog-contact-form' ),
	'formType'      => 'internal',
];
$_config  = get_post_meta( $post->ID, '_contact_form_config', true );
$config   = wp_parse_args( $_config, $defaults );

?>
<p>
    <label for="labelPosition">
        <strong><?php esc_html_e( 'Position of the field title', 'dialog-contact-form' ); ?></strong>
    </label>
    <select name="config[labelPosition]" id="labelPosition" class="widefat">
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
    <span class="description"><?php esc_html_e( 'Select the position of the field title', 'dialog-contact-form' ); ?></span>
</p>
<p>
    <label><strong><?php esc_html_e( 'Submit Button Alignment', 'dialog-contact-form' ); ?></strong></label>
    <select name="config[btnAlign]" class="widefat" required="required">
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
    <span class="description"><?php esc_html_e( 'Set the alignment of submit button.', 'dialog-contact-form' ); ?></span>
</p>
<p>
    <label><strong><?php esc_html_e( 'Submit Button Label', 'dialog-contact-form' ); ?></strong></label>
    <input name="config[btnLabel]" type="text" value="<?php echo esc_attr( $config['btnLabel'] ); ?>" class="widefat">
    <span class="description"><?php esc_html_e( 'Define the label of submit button.', 'dialog-contact-form' ); ?></span>
</p>
<p>
    <label><strong><?php esc_html_e( 'Form Type', 'dialog-contact-form' ); ?></strong></label>
    <select name="config[formType]" class="widefat" required="required">
		<?php
		$btnAlign = array(
			'internal' => esc_html__( 'Internal', 'dialog-contact-form' ),
			'popup'    => esc_html__( 'External popup window', 'dialog-contact-form' ),
		);
		foreach ( $btnAlign as $key => $value ) {
			$selected = ( $config['formType'] == $key ) ? 'selected' : '';
			echo sprintf( '<option value="%1$s" %3$s>%2$s</option>',
				esc_attr( $key ),
				esc_attr( $value ),
				$selected
			);
		}
		?>
    </select>
</p>