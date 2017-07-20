<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<style type="text/css">
	#dcf-open-dialog {
		background-color: <?php echo esc_attr( $options['dialog_color'] ); ?>;
	}
</style>
<div id="dialogContactForm" class="dcf-modal" style="display: none;">
	<div class="dcf-modal-content" style="max-width: <?php echo esc_attr( $options['dialog_width'] ); ?>px">
		<div class="dcf-modal-header">
			<span class="dcf-modal-close" onclick="document.getElementById('dialogContactForm').style.display='none'">
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" class="dcf-modal-close-img" width="20" height="20" viewBox="0 0 20 20"><path d="M14.95 6.46l-3.54 3.54 3.54 3.54-1.41 1.41-3.54-3.53-3.53 3.53-1.42-1.42 3.53-3.53-3.53-3.53 1.42-1.42 3.53 3.53 3.54-3.53z"></path></svg>
			</span>
			<h4><?php echo esc_attr( $options['dialog_title'] ); ?></h4>
		</div>
		<div class="dcf-modal-body">
			<?php echo do_shortcode( '[dialog_contact_form]' ); ?>
		</div>
	</div>
</div>

<span class="dcf-open-dialog" onclick="document.getElementById('dialogContactForm').style.display='block'">
	<svg xmlns="http://www.w3.org/2000/svg" width="25" height="16" viewBox="0 0 25 16"><path d="M21.9 13.1 21.1 13.9 15.1 8.8 12.5 11 9.9 8.8 3.9 13.9 3.1 13.1 9 8 3.1 2.9 3.9 2.1 12.5 9.5 21.1 2.1 21.9 2.9 16 8 21.9 13.1ZM2 0C0.9 0 0 0.9 0 2L0 14C0 15.1 0.9 16 2 16l21 0c1.1 0 2-0.9 2-2l0-12C25 0.9 24.1 0 23 0L2 0Z"/ fill="#ffffff"></svg>
	<span><?php echo esc_attr( $options['dialog_button'] ); ?></span>
</span>

<script type="text/javascript">
	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
	    if (event.target == document.getElementById('dialogContactForm')) {
	        document.getElementById('dialogContactForm').style.display = "none";
	    }
	}
</script>