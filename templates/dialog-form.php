<div id="modal-<?php echo absint( $options['dialog_form_id'] ); ?>" class="modal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title"><?php echo esc_html( get_the_title( $options['dialog_form_id'] ) ); ?></p>
            <button class="delete" data-dismiss="modal"></button>
        </header>
        <section class="modal-card-body">
            <div class="content">
				<?php echo $shortcode; ?>
            </div>
        </section>
    </div>
</div>