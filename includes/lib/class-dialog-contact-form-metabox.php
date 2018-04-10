<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Dialog_Contact_Form_Metabox' ) ) {
	class Dialog_Contact_Form_Metabox {

		/**
		 * Generate text field
		 *
		 * @param array $args
		 */
		public static function text( array $args ) {
			list( $name, $value, $input_id ) = self::field_common( $args );
			$class = isset( $args['input_class'] ) ? esc_attr( $args['input_class'] ) : 'dcf-input-text';

			echo self::field_before( $args );
			echo sprintf( '<input type="text" class="' . $class . '" value="%1$s" id="' . $input_id . '" name="%3$s">', $value, $args['id'], $name );
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
				$selected = ( $value == $key ) ? ' selected="selected"' : '';
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
				echo '<label class="' . $label_class . '" for="' . $input_id . '">' . $option . '</label></input>';
			}
			echo '</div>';

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
					$name  = sprintf( '%s[%s][%s]', $group, $args['position'], $args['id'] );
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

			$html = sprintf( '<div class="dcf-input-group" id="field-%s">', $options['id'] );
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
			return '</div></div>';
		}
	}
}