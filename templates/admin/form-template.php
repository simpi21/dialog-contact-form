<?php

use DialogContactForm\TemplateManager;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$templates = TemplateManager::init();
?>
<div id="modal-form-template" class="modal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <div class="modal-card-head">
            <p class="modal-card-title">
				<?php esc_html_e( 'Select a Template', 'dialog-contact-form' ); ?>
            </p>
            <button class="modal-close" data-dismiss="modal"></button>
        </div>
        <div class="modal-card-body">
            <div class="dcf-columns">
                <div class="dcf-templates">
					<?php
					/** @var \DialogContactForm\Abstracts\Abstract_Form_Template $template */
					foreach ( $templates as $template ) {
						echo '<div class="dcf-template">';
						echo '<h3>' . $template->get_title() . '</h3>';
						echo '<p>' . $template->get_description() . '</p>';
						echo '</div>';
					}
					?>
                </div>
            </div>
        </div>
    </div>
</div>