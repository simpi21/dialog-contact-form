<?php

namespace DialogContactForm\Fields;

class Acceptance extends Text {

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $metabox_fields = array(
		// Content
		'field_type',
		'field_title',
		'required_field',
		'field_width',
		// Advance
		'field_id',
		'field_class',
		// Optional
		'acceptance_text',
		'checked_by_default',
	);

}