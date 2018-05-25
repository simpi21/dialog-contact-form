<?php

namespace DialogContactForm\Supports;

class Metabox {

	/**
	 * Generate text field
	 *
	 * @param array $args
	 */
	public static function text( array $args ) {
		list( $name, $value, $input_id ) = self::field_common( $args );
		$class = isset( $args['input_class'] ) ? esc_attr( $args['input_class'] ) : 'dcf-input-text';
		$type  = isset( $args['type'] ) ? esc_attr( $args['type'] ) : 'text';

		echo self::field_before( $args );
		echo sprintf( '<input type="' . $type . '" class="' . $class . '" value="%1$s" id="' . $input_id . '" name="%3$s">',
			$value, $args['id'], $name );
		echo self::field_after();
	}

	public static function number( array $args ) {
		$args['type'] = 'number';
		self::text( $args );
	}

	/**
	 * Generate text field
	 *
	 * @param array $args
	 */
	public static function date( array $args ) {
		list( $name, $value, $input_id ) = self::field_common( $args );
		$class = isset( $args['input_class'] ) ? esc_attr( $args['input_class'] ) : 'dcf-input-text dcf-date-picker';

		echo self::field_before( $args );
		echo sprintf( '<input type="text" class="' . $class . '" value="%1$s" id="' . $input_id . '" name="%3$s" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">',
			$value, $args['id'], $name );
		echo self::field_after();
	}

	/**
	 * Generate textarea field
	 *
	 * @param array $args
	 */
	public static function textarea( array $args ) {
		list( $name, $value, $input_id ) = self::field_common( $args );
		$cols = isset( $args['cols'] ) ? $args['cols'] : 35;
		$rows = isset( $args['rows'] ) ? $args['rows'] : 2;

		$class = empty( $args['input_class'] ) ? 'dcf-input-textarea' : 'dcf-input-textarea ' . esc_attr( $args['input_class'] );

		echo self::field_before( $args );
		echo sprintf(
			'<textarea class="' . $class . '" id="' . $input_id . '" name="%3$s" cols="%4$d" rows="%5$d">%1$s</textarea>',
			esc_textarea( $value ),
			$args['id'],
			$name,
			$cols,
			$rows
		);
		echo self::field_after();
	}

	/**
	 * Generate select field
	 *
	 * @param $args
	 */
	public static function select( $args ) {
		list( $name, $value, $input_id ) = self::field_common( $args );

		$multiple = isset( $args['multiple'] ) ? 'multiple' : '';
		$class    = isset( $args['input_class'] ) ? esc_attr( $args['input_class'] ) : 'select2 dcf-input-text';

		echo self::field_before( $args );

		echo sprintf( '<select name="%1$s" id="%2$s" class="' . $class . '" %3$s>', $name, $input_id, $multiple );
		foreach ( $args['options'] as $key => $option ) {
			if ( is_array( $value ) ) {
				$selected = in_array( $key, $value ) ? ' selected="selected"' : '';
			} else {
				$selected = ( $value == $key ) ? ' selected="selected"' : '';
			}
			echo '<option value="' . $key . '" ' . $selected . '>' . $option . '</option>';
		}
		echo '</select>';

		echo self::field_after();
	}

	/**
	 * Generate select field
	 *
	 * @param $args
	 */
	public static function file_size( $args ) {
		list( $name, $value, $input_id ) = self::field_common( $args );

		$multiple = isset( $args['multiple'] ) ? 'multiple' : '';
		$class    = isset( $args['input_class'] ) ? esc_attr( $args['input_class'] ) : 'select2 dcf-input-text';

		echo self::field_before( $args );

		$args['options'] = self::get_upload_file_size_options();

		echo sprintf( '<select name="%1$s" id="%2$s" class="' . $class . '" %3$s>', $name, $input_id, $multiple );
		foreach ( $args['options'] as $key => $option ) {
			$selected = ( $value == $key ) ? ' selected="selected"' : '';
			echo '<option value="' . $key . '" ' . $selected . '>' . $option . '</option>';
		}
		echo '</select>';

		echo self::field_after();
	}

	/**
	 * Generate select field
	 *
	 * @param $args
	 */
	public static function mime_type( $args ) {
		list( $name, $value, $input_id ) = self::field_common( $args );

		$multiple = isset( $args['multiple'] ) ? 'multiple' : '';
		$class    = isset( $args['input_class'] ) ? esc_attr( $args['input_class'] ) : 'select2 dcf-input-text';

		echo self::field_before( $args );

		$args['options'] = self::allowed_mime_types();

		echo sprintf( '<select name="%1$s" id="%2$s" class="' . $class . '" %3$s>', $name, $input_id, $multiple );
		foreach ( $args['options'] as $key => $option ) {
			if ( is_array( $value ) ) {
				$selected = in_array( $key, $value ) ? ' selected="selected"' : '';
			} else {
				$selected = ( $value == $key ) ? ' selected="selected"' : '';
			}
			echo '<option value="' . $key . '" ' . $selected . '>' . $option . '</option>';
		}
		echo '</select>';

		echo self::field_after();
	}

	/**
	 * Generate buttonset field
	 *
	 * @param array $config
	 */
	public static function buttonset( array $config ) {
		list( $name, $value, $input_id ) = self::field_common( $config );
		$input_class = empty( $config['input_class'] ) ? 'switch-input' : 'switch-input ' . $config['input_class'];

		echo self::field_before( $config );

		echo '<div class="buttonset">';
		foreach ( $config['options'] as $key => $option ) {
			$input_id    = $input_id . '_' . $key;
			$checked     = ( $value == $key ) ? ' checked="checked"' : '';
			$label_class = ( $value == $key ) ? 'switch-label switch-label-on' : 'switch-label switch-label-off';
			echo '<input class="' . $input_class . '" id="' . $input_id . '" type="radio" value="' . $key . '"
                       name="' . $name . '" ' . $checked . '>';
			echo '<label class="' . $label_class . '" for="' . $input_id . '">' . $option . '</label>';
		}
		echo '</div>';

		echo self::field_after();
	}

	/**
	 * Generate checkbox field
	 *
	 * @param array $args
	 */
	public static function checkbox( array $args ) {
		list( $name, $value, $input_id ) = self::field_common( $args );

		echo self::field_before( $args );

		if ( isset( $args['options'] ) ) {
			$name = $name . '[]';
			foreach ( $args['options'] as $key => $option ) {
				$input_id = $input_id . '_' . $key;
				$value    = is_array( $value ) ? $value : array();
				$checked  = in_array( $key, $value ) ? 'checked="checked"' : '';
				echo sprintf(
					'<label><input type="checkbox" class="input-validate" name="%1$s" value="%2$s" %4$s>%3$s </label>',
					$name, $key, $option, $checked
				);
			}
		} else {
			$label   = isset( $args['label'] ) ? $args['label'] : '';
			$checked = ( 'on' == $value ) ? ' checked="checked"' : '';
			echo sprintf( '<input type="hidden" name="%1$s" value="off">', $name );
			echo sprintf( '<label for="%2$s"><input type="checkbox" ' . $checked . ' value="on" id="%2$s" name="%1$s">%3$s</label>',
				$name, $args['id'], $label );
		}

		echo self::field_after();
	}

	/**
	 * Options for input type number
	 *
	 * @param array $config
	 */
	public static function number_options( array $config ) {
		$config['id'] = 'number_min';
		list( $number_min_name, $number_min_value, $number_min_input_id ) = self::field_common( $config );
		$config['id'] = 'number_max';
		list( $number_max_name, $number_max_value, $number_max_input_id ) = self::field_common( $config );
		$config['id'] = 'number_step';
		list( $number_step_name, $number_step_value, $number_step_input_id ) = self::field_common( $config );

		echo self::field_before( $config );

		?>
        <label>
			<?php esc_html_e( 'Min Value:', 'dialog-contact-form' ); ?>
            <input type="number" name="<?php esc_attr_e( $number_min_name ); ?>"
                   value="<?php esc_attr_e( $number_min_value ); ?>"
                   step="0.01" class="small-text"></label>
        <label>
			<?php esc_html_e( 'Max Value:', 'dialog-contact-form' ); ?>
            <input type="number" name="<?php esc_attr_e( $number_max_name ); ?>"
                   value="<?php esc_attr_e( $number_max_value ); ?>"
                   step="0.01" class="small-text"></label>
        <label>
			<?php esc_html_e( 'Step:', 'dialog-contact-form' ); ?>
            <input type="number" name="<?php esc_attr_e( $number_step_name ); ?>"
                   value="<?php esc_attr_e( $number_step_value ); ?>"
                   step="0.01" class="small-text"></label>
		<?php

		echo self::field_after();
	}

	/**
	 * Options for input type number
	 *
	 * @param array $config
	 */
	public static function pages_list( array $config ) {
		list( $name, $value, $input_id ) = self::field_common( $config );

		$multiple = isset( $config['multiple'] ) ? 'multiple' : '';
		$class    = isset( $config['input_class'] ) ? esc_attr( $config['input_class'] ) : 'select2 dcf-input-text';

		echo self::field_before( $config );

		wp_dropdown_pages( array(
			'id'                => $input_id,
			'class'             => $class,
			'name'              => $name,
			'selected'          => $value,
			'value_field'       => 'ID',
			'echo'              => 1,
			'show_option_none'  => esc_attr__( '-- Select a page --', 'dialog-contact-form' ),
			'option_none_value' => '0',
		) );

		echo self::field_after();
	}

	/**
	 * Generate field name and field value
	 *
	 * @param $args
	 *
	 * @return array
	 */
	private static function field_common( $args ) {
		global $post;
		// Meta Name
		$group    = isset( $args['group'] ) ? $args['group'] : 'dialog_contact_form';
		$multiple = isset( $args['multiple'] ) ? '[]' : '';
		$name     = sprintf( '%s[%s]%s', $group, $args['id'], $multiple );

		// Meta Value
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$meta    = get_post_meta( $post->ID, $args['id'], true );
		$value   = ! empty( $meta ) ? $meta : $default;

		// ID
		$id = sprintf( '%s_%s', $group, $args['id'] );

		if ( isset( $args['meta_key'] ) ) {
			$meta  = get_post_meta( $post->ID, $args['meta_key'], true );
			$value = ! empty( $meta[ $args['id'] ] ) ? $meta[ $args['id'] ] : $default;

			if ( isset( $args['position'] ) ) {
				$id    = sprintf( '%s_%s_%s', $group, $args['id'], $args['position'] );
				$name  = sprintf( '%s[%s][%s]%s', $group, $args['position'], $args['id'], $multiple );
				$value = ! empty( $meta[ $args['position'] ][ $args['id'] ] ) ? $meta[ $args['position'] ][ $args['id'] ] : $default;
			}
		}

		if ( $value == 'zero' ) {
			$value = 0;
		}

		return array( $name, $value, $id );
	}

	/**
	 * Generate field before template
	 *
	 * @param $options
	 *
	 * @return string
	 */
	private static function field_before( $options ) {
		$group    = isset( $options['group'] ) ? $options['group'] : 'dialog_contact_form';
		$input_id = sprintf( '%s_%s', $group, $options['id'] );

		if ( isset( $options['position'], $options['meta_key'] ) ) {
			$input_id = sprintf( '%s_%s_%s', $group, $options['id'], $options['position'] );
		}

		$group_class = isset( $options['group_class'] ) ? $options['group_class'] : 'dcf-input-group';

		$html = sprintf( '<div class="%s" id="field-%s">', esc_attr( $group_class ), $input_id );
		$html .= sprintf( '<div class="dcf-input-label">' );
		$html .= sprintf( '<label for="%1$s">%2$s</label>', $input_id, $options['label'] );
		if ( ! empty( $options['description'] ) ) {
			$html .= sprintf( '<p class="dcf-input-desc">%s</p>', $options['description'] );
		}
		$html .= '</div>';
		$html .= sprintf( '<div class="dcf-input-field">' );

		return $html;
	}

	/**
	 * Generate field after template
	 *
	 * @return string
	 */
	private static function field_after() {
		return '</div></div>' . PHP_EOL;
	}

	/**
	 * creates array of upload sizes based on server limits
	 * to use in the file_sizes control
	 * @return array
	 */
	private static function get_upload_file_size_options() {
		$max_file_size = wp_max_upload_size() / pow( 1024, 2 ); //MB

		$sizes = array();
		for ( $file_size = 1; $file_size <= $max_file_size; $file_size ++ ) {
			$sizes[ $file_size ] = $file_size . 'MB';
		}

		return $sizes;
	}

	/**
	 * Get allowed mime types list
	 *
	 * @return array
	 */
	private static function allowed_mime_types() {
		$mime_types         = array();
		$allowed_mime_types = get_allowed_mime_types();
		foreach ( $allowed_mime_types as $extension => $allowed_mime_type ) {
			$mime_types[ $extension ] = $extension;
		}

		return $mime_types;
	}
}
