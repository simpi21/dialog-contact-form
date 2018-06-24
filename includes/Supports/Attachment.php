<?php

namespace DialogContactForm\Supports;

use DialogContactForm\Fields\File;
use DialogContactForm\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Attachment {

	/**
	 * @return array
	 */
	private static function get_validation_messages() {
		$options  = Utils::get_option();
		$default  = Utils::validation_messages();
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
	 * @param array $field
	 *
	 * @return string
	 */
	private static function get_file_error( $file, $field ) {
		$is_required = self::is_required( $field );

		$messages = self::get_validation_messages();

		// If file is required and no file uploaded, return require message
		if ( $file->getError() === UPLOAD_ERR_NO_FILE ) {
			if ( $is_required ) {
				return $messages['required_file'];
			} else {
				return '';
			}
		}

		$file_field = new File();
		$file_field->setField( $field );

		// check file size here.
		if ( $file->getSize() > $file_field->get_max_file_size() ) {
			return $messages['file_too_large'];
		}

		// Get file mime type for uploaded file
		$file_info = new \finfo( FILEINFO_MIME_TYPE );
		$mime_type = $file_info->file( $file->getFile() );

		// Get file extension from allowed mime types
		$ext = array_search( $mime_type, $file_field->get_allowed_mime_types(), true );

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

		$files    = UploadedFile::getUploadedFiles();
		$file     = isset( $files[ $field['field_name'] ] ) ? $files[ $field['field_name'] ] : false;
		$messages = self::get_validation_messages();

		if ( is_array( $file ) && ! self::is_multiple( $field ) ) {
			return array( $messages['unsupported_file_multi'] );
		}

		if ( self::is_required( $field ) && false === $file ) {
			return array( $messages['required_file'] );
		}

		$message = array();
		if ( $file instanceof UploadedFile ) {
			$message[] = self::get_file_error( $file, $field );
		}

		if ( is_array( $file ) ) {
			foreach ( $file as $_file ) {
				if ( $_file instanceof UploadedFile ) {
					$message[] = self::get_file_error( $_file, $field );
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
		$files       = UploadedFile::getUploadedFiles();

		foreach ( $fields as $field ) {
			if ( 'file' !== $field['field_type'] ) {
				continue;
			}

			$file = isset( $files[ $field['field_name'] ] ) ? $files[ $field['field_name'] ] : false;

			if ( $file instanceof UploadedFile ) {
				$attachments[ $field['field_name'] ][] = self::upload_individual_file( $file );
			}
			if ( is_array( $file ) ) {
				foreach ( $file as $_file ) {
					if ( ! $_file instanceof UploadedFile ) {
						continue;
					}
					$attachments[ $field['field_name'] ][] = self::upload_individual_file( $_file );
				}
			}
		}

		return array_filter( $attachments );
	}

	/**
	 * Upload attachment
	 *
	 * @param UploadedFile $file
	 *
	 * @return array
	 */
	private static function upload_individual_file( $file ) {
		$upload_dir = wp_upload_dir();

		$attachment_dir = join( DIRECTORY_SEPARATOR, array( $upload_dir['basedir'], DIALOG_CONTACT_FORM_UPLOAD_DIR ) );
		$attachment_url = join( DIRECTORY_SEPARATOR, array( $upload_dir['baseurl'], DIALOG_CONTACT_FORM_UPLOAD_DIR ) );

		// Make attachment directory in upload directory if not already exists
		if ( ! file_exists( $attachment_dir ) ) {
			wp_mkdir_p( $attachment_dir );
		}

		// Check if attachment directory is writable
		if ( ! wp_is_writable( $attachment_dir ) ) {
			return array();
		}

		// Check file has no error
		if ( $file->getError() !== UPLOAD_ERR_OK ) {
			return array();
		}

		// Upload file
		try {
			$filename = wp_unique_filename( $attachment_dir, $file->getClientFilename() );
			$new_file = $file->moveUploadedFile( $attachment_dir, $filename );
			// Set correct file permissions.
			$stat  = stat( dirname( $new_file ) );
			$perms = $stat['mode'] & 0000666;
			@ chmod( $new_file, $perms );

			// Insert the attachment.
			$attachment    = array(
				'guid'           => join( DIRECTORY_SEPARATOR, array( $attachment_url, $filename ) ),
				'post_title'     => preg_replace( '/\.[^.]+$/', '', $file->getClientFilename() ),
				'post_status'    => 'inherit',
				'post_mime_type' => $file->getClientMediaType(),
			);
			$attachment_id = wp_insert_attachment( $attachment, $new_file );

			// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			// Generate the metadata for the attachment, and update the database record.
			$attach_data = wp_generate_attachment_metadata( $attachment_id, $new_file );
			wp_update_attachment_metadata( $attachment_id, $attach_data );

			if ( ! is_wp_error( $attachment_id ) ) {
				$attachment['attachment_path'] = $new_file;
				$attachment['attachment_id']   = $attachment_id;
			}

			return $attachment;

		} catch ( \Exception $exception ) {
			return array();
		}
	}

	/**
	 * Check if current field is required
	 *
	 * @param $field
	 *
	 * @return bool
	 */
	private static function is_required( $field ) {
		if ( isset( $field['required_field'] ) && 'on' == $field['required_field'] ) {
			return true;
		}

		// Backward compatibility
		if ( isset( $field['validation'] ) && is_array( $field['validation'] )
		     && in_array( 'required', $field['validation'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if field support multiple file upload
	 *
	 * @param array $field
	 *
	 * @return bool
	 */
	private static function is_multiple( $field ) {
		return ( isset( $field['multiple'] ) && 'on' === $field['multiple'] );
	}
}