<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div id="dcf_success_status" class="dcf_success_status"></div>
<div id="dcf_error_status" class="dcf_error_status"></div>

<form id="dialog_contact_form" autocomplete="off" novalidate="novalidate">

	<p class="fields fullname">
		<label for="fullname"><?php echo isset( $options['label_name'] ) ? esc_attr( $options['label_name'] ) : ''; ?></label>
		<input type="text" id="fullname" name="fullname" value="" placeholder="<?php echo isset($options['place_name']) ? esc_attr( $options['place_name'] ) : ''; ?>" >
	</p>

	<p class="fields email">
		<label for="email"><?php echo isset( $options['label_email'] ) ? esc_attr( $options['label_email'] ) : ''; ?></label>
		<input type="email" id="email" name="email" value="" placeholder="<?php echo isset( $options['place_email'] ) ? esc_attr( $options['place_email'] ) : ''; ?>">
	</p>

	<?php if(isset($options['field_web']) && $options['field_web'] == 'on' ): ?>
	<p class="fields website">
		<label for="website"><?php echo (isset($options['label_url'])) ? esc_attr( $options['label_url'] ) : ''; ?></label>
		<input type="url" id="website" name="website" value="" placeholder="<?php echo isset( $options['place_url'] ) ? esc_attr( $options['place_url'] ) : ''; ?>" >
	</p>
	<?php endif; ?>

	<?php if(isset($options['field_phone']) && $options['field_phone'] == 'on' ): ?>
	<p class="fields phone">
		<label for="phone"><?php echo (isset($options['label_phone'])) ? esc_attr( $options['label_phone'] ) : ''; ?></label>
		<input type="text" id="phone" name="phone" value="" placeholder="<?php echo isset($options['place_phone'] ) ? esc_attr( $options['place_phone'] ) : ''; ?>" >
	</p>
	<?php endif; ?>

	<?php if(isset($options['field_sub']) && $options['field_sub'] == 'on' ): ?>
	<p class="fields subject">
		<label for="subject"><?php echo (isset($options['label_sub'])) ? esc_attr( $options['label_sub'] ) : ''; ?></label>
		<input type="text" id="subject" name="subject" value="" placeholder="<?php echo isset($options['place_sub'] ) ? esc_attr( $options['place_sub'] ) : ''; ?>" >
	</p>
	<?php endif; ?>

	<p class="fields message">
		<label for="message"><?php echo (isset($options['label_msg'])) ? esc_attr( $options['label_msg'] ) : ''; ?></label>
		<textarea name="message" id="message" rows="5" placeholder="<?php echo isset($options['place_msg'] ) ? esc_attr( $options['place_msg'] ) : ''; ?>" required></textarea>
	</p>

	<?php if(isset($options['field_captcha']) && $options['field_captcha'] == 'on' ): ?>
	<p class="fields captcha">
		<label for="captcha"><?php echo (isset($options['label_capt'])) ? esc_attr( $options['label_capt'] ) : ''; ?></label>
		<input type="text" name="captcha" id="captcha" placeholder="<?php echo isset($options['place_capt'] ) ? esc_attr( $options['place_capt'] ) : ''; ?>" autocomplete="off" required>
	</p>

	<p class="fields captcha-img">
	  	<img id="dcf-captcha-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEAAAEALAAAAAABAAEAAAICTAEAOw==">
	  	<svg xmlns="http://www.w3.org/2000/svg" class="dcf-svg-loader" width="16" height="16" viewBox="0 0 24 28"><path d="M23.6 16.5c0 0 0 0.1 0 0.1-1.3 5.5-5.9 9.4-11.7 9.4-3 0-6-1.2-8.2-3.3l-2 2c-0.2 0.2-0.4 0.3-0.7 0.3-0.5 0-1-0.5-1-1v-7c0-0.5 0.5-1 1-1h7c0.5 0 1 0.5 1 1 0 0.3-0.1 0.5-0.3 0.7l-2.1 2.1c1.5 1.4 3.4 2.2 5.4 2.2 2.8 0 5.4-1.4 6.8-3.8 0.4-0.6 0.6-1.2 0.8-1.8 0.1-0.2 0.2-0.4 0.5-0.4h3c0.3 0 0.5 0.2 0.5 0.5zM24 4v7c0 0.5-0.5 1-1 1h-7c-0.5 0-1-0.5-1-1 0-0.3 0.1-0.5 0.3-0.7l2.2-2.2c-1.5-1.4-3.4-2.1-5.5-2.1-2.8 0-5.4 1.4-6.8 3.8-0.4 0.6-0.6 1.2-0.8 1.8-0.1 0.2-0.2 0.4-0.5 0.4h-3.1c-0.3 0-0.5-0.2-0.5-0.5v-0.1c1.3-5.5 6-9.4 11.7-9.4 3.1 0 6 1.2 8.3 3.3l2-2c0.2-0.2 0.4-0.3 0.7-0.3 0.5 0 1 0.5 1 1z"/></svg>
	</p>
	<?php endif; ?>

	<p class="fields submit">
	  	<input type="submit" name="send_mail" id="send_mail" value="<?php echo isset($options['label_submit'] ) ? esc_attr( $options['label_submit'] ) : ''; ?>">
	  	<span class="dcf-ajax-loader">
	  		<svg xmlns="http://www.w3.org/2000/svg" class="dcf-spin" width="16" height="16" viewBox="0 0 24 28"><path d="M23.6 16.5c0 0 0 0.1 0 0.1-1.3 5.5-5.9 9.4-11.7 9.4-3 0-6-1.2-8.2-3.3l-2 2c-0.2 0.2-0.4 0.3-0.7 0.3-0.5 0-1-0.5-1-1v-7c0-0.5 0.5-1 1-1h7c0.5 0 1 0.5 1 1 0 0.3-0.1 0.5-0.3 0.7l-2.1 2.1c1.5 1.4 3.4 2.2 5.4 2.2 2.8 0 5.4-1.4 6.8-3.8 0.4-0.6 0.6-1.2 0.8-1.8 0.1-0.2 0.2-0.4 0.5-0.4h3c0.3 0 0.5 0.2 0.5 0.5zM24 4v7c0 0.5-0.5 1-1 1h-7c-0.5 0-1-0.5-1-1 0-0.3 0.1-0.5 0.3-0.7l2.2-2.2c-1.5-1.4-3.4-2.1-5.5-2.1-2.8 0-5.4 1.4-6.8 3.8-0.4 0.6-0.6 1.2-0.8 1.8-0.1 0.2-0.2 0.4-0.5 0.4h-3.1c-0.3 0-0.5-0.2-0.5-0.5v-0.1c1.3-5.5 6-9.4 11.7-9.4 3.1 0 6 1.2 8.3 3.3l2-2c0.2-0.2 0.4-0.3 0.7-0.3 0.5 0 1 0.5 1 1z"/></svg>
	  	</span>
	</p>
</form>
