<?php

namespace DialogContactForm\Supports;

class Attachment {

	/**
	 * @return array
	 */
	private static function get_validation_messages() {
		$options  = get_dialog_contact_form_option();
		$default  = dcf_validation_messages();
		$messages = array();
		foreach ( $default as $key => $message ) {
			$messages[ $key ] = ! empty( $options[ $key ] ) ? $options[ $key ] : $message;
		}

		return $messages;
	}

	/**
	 * Get error message for each uploaded files
	 *
	 * @param \DialogContactForm\Supports\UploadedFile $file
	 * @param bool $is_required
	 *
	 * @return string
	 */
	private static function get_file_error( $file, $is_required ) {
		$messages = self::get_validation_messages();

		// If file is required and no file uploaded, return require message
		if ( $file->getError() === UPLOAD_ERR_NO_FILE ) {
			if ( $is_required ) {
				return $messages['required_file'];
			} else {
				return '';
			}
		}

		// check file size here.
		if ( $file->getSize() > wp_max_upload_size() ) {
			return $messages['file_too_large'];
		}

		// Get file mime type for uploaded file
		$file_info = new \finfo( FILEINFO_MIME_TYPE );
		$mime_type = $file_info->file( $file->getFile() );

		// Get file extension from allowed mime types
		$ext = array_search( $mime_type, get_allowed_mime_types(), true );

		// Check if uploaded file mime type is allowed
		if ( false === strpos( $ext, $file->getClientExtension() ) ) {
			return $messages['invalid_file_format'];
		}

		return '';
	}

	/**
	 * Validate file field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function validate( $field ) {

		$is_required = false;
		if ( isset( $field['required_field'] ) && 'on' == $field['required_field'] ) {
			$is_required = true;
		}

		// Backward compatibility
		$validate_rules = is_array( $field['validation'] ) ? $field['validation'] : array();
		if ( in_array( 'required', $validate_rules ) ) {
			$is_required = true;
		}

		$files = UploadedFile::getUploadedFiles();
		$file  = isset( $files[ $field['field_name'] ] ) ? $files[ $field['field_name'] ] : false;

		$message = array();
		if ( $file instanceof UploadedFile ) {
			$message[] = self::get_file_error( $file, $is_required );
		}

		if ( is_array( $file ) ) {
			foreach ( $file as $_file ) {
				if ( $_file instanceof UploadedFile ) {
					$message[] = self::get_file_error( $_file, $is_required );
				}
			}
		}

		return array_filter( $message );
	}


	/**
	 * Upload attachments
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public static function upload( $fields ) {
		$attachments = array();

		// Check if current form has any file field
		if ( ! in_array( 'file', array_column( $fields, 'field_type' ) ) ) {
			return $attachments;
		}

		$upload_dir = wp_upload_dir();

		$attachment_dir = join( DIRECTORY_SEPARATOR, array( $upload_dir['basedir'], DIALOG_CONTACT_FORM_UPLOAD_DIR ) );

		// Make attachment directory in upload directory if not already exists
		if ( ! file_exists( $attachment_dir ) ) {
			wp_mkdir_p( $attachment_dir );
		}

		$files = UploadedFile::getUploadedFiles();

		$files_list = array();
		foreach ( $fields as $field ) {
			if ( 'file' != $field['field_type'] ) {
				continue;
			}

			$file = isset( $files[ $field['field_name'] ] ) ? $files[ $field['field_name'] ] : false;
			if ( $file instanceof UploadedFile ) {
				$files_list[] = $file;
			}
			if ( is_array( $file ) ) {
				foreach ( $file as $_file ) {
					if ( $_file instanceof UploadedFile ) {
						$files_list[] = $_file;
					}
				}
			}
		}

		/** @var \DialogContactForm\Supports\UploadedFile $file */
		foreach ( $files_list as $file ) {

			if ( $file->getError() !== UPLOAD_ERR_OK ) {
				continue;
			}

			// Validate once again
			if ( self::get_file_error( $file, true ) ) {
				continue;
			}

			// Upload file
			try {
				$filename = wp_unique_filename( $attachment_dir, $file->getClientFilename() );
				$new_file = $file->moveUploadedFile( $attachment_dir, $filename );
				// Set correct file permissions.
				$stat  = stat( dirname( $new_file ) );
				$perms = $stat['mode'] & 0000666;
				@ chmod( $new_file, $perms );

				// Save uploaded file path for later use
				$attachments[] = $new_file;
			} catch ( \Exception $exception ) {

			}
		}

		return $attachments;
	}

	/**
	 * Remove attachment
	 *
	 * @param array|string $attachments
	 *
	 * @return bool
	 */
	public static function remove( $attachments ) {
		if ( is_string( $attachments ) && file_exists( $attachments ) ) {
			return unlink( $attachments );
		}

		foreach ( $attachments as $attachment ) {
			if ( file_exists( $attachment ) ) {
				unlink( $attachment );
			}
		}

		return true;
	}
}