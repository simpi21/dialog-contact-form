<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( $fields ): ?>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="dcf-form columns is-multiline"
          method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
        <div class="dcf-response">
            <div class="dcf-success"></div>
            <div class="dcf-error"></div>
        </div>
		<?php wp_nonce_field( '_dcf_submit_form', '_dcf_nonce' ); ?>
        <input type="hidden" name="_user_form_id" value="<?php echo intval( $id ); ?>">

		<?php
		$errors = DialogContactFormSession::flash( '_dcf_errors' );

		foreach ( $fields as $_field ):

			$has_error = false;
			if ( isset( $errors[ $_field['field_name'] ][0] ) ) {
				$has_error = true;
			}

			$is_required   = false;
			$required_abbr = '';
			$required_attr = '';
			if ( in_array( 'required', $_field['validation'] ) ) {
				$is_required   = true;
				$required_abbr = sprintf(
					'&nbsp;<abbr class="dcf-required" title="%s">*</abbr>',
					esc_html__( 'Required', 'dialog-contact-form' )
				);
				$required_attr = ' required';
			}

			echo sprintf( '<div class="field column %s">', $_field['field_width'] );

			if ( ( $_field['field_type'] != 'hidden' ) && ( $config['labelPosition'] != 'placeholder' ) ) {
				echo sprintf(
					'<label for="%2$s-%1$s" class="label">%3$s%4$s</label>',
					intval( $id ),
					esc_attr( $_field['field_id'] ),
					esc_attr( $_field['field_title'] ),
					$required_abbr
				);
			}

			echo '<p class="control">';

			if ( $config['labelPosition'] == 'label' ) {
				$placeholder = '';
			} else {
				$placeholder = empty( $_field['placeholder'] ) ? '' : sprintf( ' placeholder="%s"', esc_attr( $_field['placeholder'] ) );
			}
			$options = empty( $_field['options'] ) ? array() : explode( PHP_EOL, $_field['options'] );

			if ( $_field['field_type'] == 'textarea' ):
				printf(
					'<textarea id="%2$s-%1$s" name="%3$s" class="%6$s"%4$s%7$s>%5$s</textarea>',
					intval( $id ),
					esc_attr( $_field['field_id'] ),
					esc_attr( $_field['field_name'] ),
					$placeholder,
					empty( $_POST[ $_field['field_name'] ] ) ? null : esc_textarea( $_POST[ $_field['field_name'] ] ),
					$has_error ? 'textarea is-danger' : 'textarea',
					$required_attr
				);

            elseif ( $_field['field_type'] == 'radio' ):
				$value = empty( $_POST[ $_field['field_name'] ] ) ? null : esc_attr( $_POST[ $_field['field_name'] ] );
				foreach ( $options as $option ) {
					$checked = ( $value == $option ) ? ' checked' : '';
					printf(
						'<label class="radio"><input type="radio" name="%1$s" value="%2$s"%3$s%4$s> %2$s</label>',
						esc_attr( $_field['field_name'] ),
						esc_attr( $option ),
						$checked,
						$required_attr
					);
				}

            elseif ( $_field['field_type'] == 'checkbox' ):
				$value = empty( $_POST[ $_field['field_name'] ] ) ? null : esc_attr( $_POST[ $_field['field_name'] ] );
				foreach ( $options as $option ) {
					$checked = ( $value == $option ) ? ' checked' : '';
					printf(
						'<label class="checkbox"><input type="checkbox" name="%1$s" value="%2$s"%3$s%4$s> %2$s</label>',
						esc_attr( $_field['field_name'] ),
						esc_attr( $option ),
						$checked,
						$required_attr
					);
				}

            elseif ( $_field['field_type'] == 'select' ):
				$value = empty( $_POST[ $_field['field_name'] ] ) ? null : esc_attr( $_POST[ $_field['field_name'] ] );
				echo sprintf( '<div class="%s">', $has_error ? 'select is-danger' : 'select' );
				echo sprintf(
					'<select id="%2$s-%1$s" name="%3$s"%4$s>',
					intval( $id ),
					esc_attr( $_field['field_id'] ),
					esc_attr( $_field['field_name'] ),
					$required_attr
				);
				if ( ! empty( $_field['placeholder'] ) ) {
					echo sprintf( '<option value="">%s</option>', esc_attr( $_field['placeholder'] ) );
				}
				foreach ( $options as $option ) {
					$selected = ( $value == $option ) ? ' selected' : '';
					printf(
						'<option value="%1$s"%2$s>%1$s</option>',
						esc_attr( $option ),
						$selected
					);
				}
				echo '</select>';
				echo '</div>';

            elseif ( $_field['field_type'] == 'number' ):
				$min  = empty( $_field['number_min'] ) ? '' : sprintf( ' min="%s"', floatval( $_field['number_min'] ) );
				$max  = empty( $_field['number_max'] ) ? '' : sprintf( ' max="%s"', floatval( $_field['number_max'] ) );
				$step = empty( $_field['number_step'] ) ? '' : sprintf( ' step="%s"', floatval( $_field['number_step'] ) );
				printf(
					'<input id="%2$s-%1$s" name="%3$s" type="number" class="%9$s" value="%4$s"%5$s%6$s%7$s%8$s%10$s>',
					intval( $id ),
					esc_attr( $_field['field_id'] ),
					esc_attr( $_field['field_name'] ),
					empty( $_POST[ $_field['field_name'] ] ) ? null : esc_textarea( $_POST[ $_field['field_name'] ] ),
					$placeholder,
					$min,
					$max,
					$step,
					$has_error ? 'input is-danger' : 'input',
					$required_attr
				);

            elseif ( $_field['field_type'] == 'file' ):
				$accept   = '';
				$multiple = '';
				printf(
					'<input id="%2$s-%1$s" name="%3$s" type="file" class="file"%4$s%5$s%6$s>',
					intval( $id ),
					esc_attr( $_field['field_id'] ),
					esc_attr( $_field['field_name'] ),
					$multiple,
					$accept,
					$required_attr
				);

            elseif ( in_array( $_field['field_type'], array(
				'text',
				'email',
				'url',
				'search',
				'password',
				'hidden',
				'date',
				'time'
			) ) ):
				printf(
					'<input id="%2$s-%1$s" name="%3$s" type="%4$s" class="%7$s" %5$s value="%6$s"%8$s>',
					intval( $id ),
					esc_attr( $_field['field_id'] ),
					esc_attr( $_field['field_name'] ),
					esc_attr( $_field['field_type'] ),
					$placeholder,
					empty( $_POST[ $_field['field_name'] ] ) ? null : esc_textarea( $_POST[ $_field['field_name'] ] ),
					$has_error ? 'input is-danger' : 'input',
					$required_attr
				);
			else:
				printf(
					'<input id="%2$s-%1$s" name="%3$s" type="text" class="%6$s" %4$s value="%5$s"%7$s>',
					intval( $id ),
					esc_attr( $_field['field_id'] ),
					esc_attr( $_field['field_name'] ),
					$placeholder,
					empty( $_POST[ $_field['field_name'] ] ) ? null : esc_textarea( $_POST[ $_field['field_name'] ] ),
					$has_error ? 'input is-danger' : 'input',
					$required_attr
				);
			endif;
			// Show error message if any
			if ( isset( $errors[ $_field['field_name'] ][0] ) ) {
				echo '<span class="help is-danger">' . esc_attr( $errors[ $_field['field_name'] ][0] ) . '</span>';
			}
			echo '</p>';
			echo '</div>';
		endforeach;

		// Submit button
		printf( '<div class="field column is-12"><p class="%s"><button type="submit" class="button dcf-submit">%s</button></p></div>',
			( isset( $config['btnAlign'] ) && $config['btnAlign'] == 'right' ) ? 'control level-right' : 'control level-left',
			esc_attr( $config['btnLabel'] )
		);
		?>
    </form>
<?php endif; ?>
