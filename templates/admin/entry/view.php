<?php

use DialogContactForm\Supports\Entry;

$entry     = new Entry();
$data      = $entry->get( $id );
$meta_data = array();

if ( isset( $data['meta_data'] ) && is_array( $data['meta_data'] ) ) {
	$meta_data = $data['meta_data'];
	unset( $data['meta_data'] );
}

$form_id = isset( $meta_data['form_id'] ) ? $meta_data['form_id'] : 0;
$fields  = get_post_meta( $form_id, '_contact_form_fields', true );

?>
<table class="form-table">
	<?php foreach ( $fields as $field ) { ?>
        <tr>
            <th scope="row"><?php echo $field['field_title']; ?></th>
            <td>
				<?php
				$value = isset( $data[ $field['field_name'] ] ) ? $data[ $field['field_name'] ] : null;
				if ( is_string( $value ) ) {
					echo wpautop( $value );
				} elseif ( is_numeric( $value ) ) {
					if ( is_float( $value ) ) {
						echo floatval( $value );
					} else {
						echo intval( $value );
					}
				} elseif ( is_array( $value ) ) {
					echo implode( '<br>', $value );
				}
				?>
            </td>
        </tr>
	<?php } ?>
</table>
