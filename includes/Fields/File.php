<?php

namespace DialogContactForm\Fields;

use DialogContactForm\Abstracts\Abstract_Field;

class File extends Abstract_Field {

	/**
	 * Render field html for frontend display
	 *
	 * @param $field
	 *
	 * @return string
	 */
	public function render( $field ) {
		$this->setField( $field );

		$accept   = '';
		$multiple = '';
		$html     = sprintf( '<input id="%1$s" class="%2$s" name="%3$s" type="file" %4$s %5$s %6$s>',
			$this->get_id(),
			$this->get_class( 'file' ),
			$this->get_name(),
			$multiple,
			$accept,
			$this->get_required()
		);

		return $html;
	}

	/**
	 * Validate field value
	 *
	 * @param mixed $value
	 */
	public function validate( $value ) {
		// TODO: Implement validate() method.
	}

	/**
	 * Sanitize field value
	 *
	 * @param mixed $value
	 */
	public function sanitize( $value ) {
		// TODO: Implement sanitize() method.
	}

	/**
	 * Get field value
	 *
	 * @return mixed
	 */
	protected function get_value() {
		// TODO: Implement get_value() method.
	}
}
