<?php

namespace DialogContactForm\Fields;

class Password extends Text {
	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = 'password';

	/**
	 * Render field html for frontend display
	 *
	 * @param $field
	 *
	 * @return string
	 */
	public function render( $field ) {
		$this->field = $field;

		$html = sprintf( '<input id="%1$s" class="%2$s" name="%3$s" type="%4$s" %5$s %6$s>',
			$this->get_id(),
			$this->get_class( 'input' ),
			$this->get_name(),
			$this->get_type(),
			$this->get_placeholder(),
			$this->get_required()
		);

		return $html;
	}
}