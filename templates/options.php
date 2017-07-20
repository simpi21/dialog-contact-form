<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="wrap">
	<h2><?php _e('Dialog Contact Form', 'dialog-contact-form'); ?></h2>
	<hr>
	<p><?php printf( __('To use contact form as dialog, just choose "Show Dialog" from option. If you want to use contact form for page, just copy this shortcode %s and paste where you want to show it.', 'dialog-contact-form'), '<code>[dialog_contact_form]</code>') ?></p>
	<hr>

	<form method="post" action="options.php">

		<?php 
			$options = $this->options;
			settings_fields( 'dialog-contact-form_options' );
		?>
	    <table class="form-table">
	        <tr valign="top">
	        	<th scope="row">
	        		<label><?php _e('Mail Receiver Email', 'dialog-contact-form'); ?></label>
	        	</th>
	        	<td>
	        		<input type="email" class="regular-text ltr" name="dialog-contact-form_options[email]" value="<?php esc_attr_e($options['email']); ?>">
	        	</td>
	        </tr>
	         
	        <tr valign="top">
	        	<th scope="row">
	        		<label><?php _e('Show Form Fields', 'dialog-contact-form'); ?></label>
	        	</th>
		        <td>
		        	<label for="field_web">
		        		<input type="checkbox" id="field_web" name="dialog-contact-form_options[field_web]" value="on" <?php checked( $options['field_web'], 'on' ); ?>><?php _e('Website', 'dialog-contact-form'); ?>
		        	</label>
		        	<label for="field_phone">
		        		<input type="checkbox" id="field_phone" name="dialog-contact-form_options[field_phone]" value="on" <?php checked( $options['field_phone'], 'on' ); ?>><?php _e('Phone', 'dialog-contact-form'); ?>
		        	</label>
		        	<label for="field_sub">
		        		<input type="checkbox" id="field_sub" name="dialog-contact-form_options[field_sub]" value="on" <?php checked( $options['field_sub'], 'on' ); ?>><?php _e('Subject', 'dialog-contact-form'); ?>
		        	</label>
		        	<label for="field_captcha">
		        		<input type="checkbox" id="field_captcha" name="dialog-contact-form_options[field_captcha]" value="on" <?php checked( $options['field_captcha'], 'on' ); ?>><?php _e('Captcha', 'dialog-contact-form'); ?>
		        	</label>
		        </td>
	        </tr>
	         
	        <tr valign="top">
	        	<th scope="row">
	        		<label><?php _e('Show or Hide Dialog Form', 'dialog-contact-form'); ?></label>
	        	</th>
		        <td>
		        	<input type="radio" name="dialog-contact-form_options[display_dialog]" value="show" <?php checked( $options['display_dialog'], 'show' ); ?> ><?php _e('Show Dialog', 'dialog-contact-form'); ?><br>
		        	<input type="radio" name="dialog-contact-form_options[display_dialog]" value="hide" <?php checked( $options['display_dialog'], 'hide' ); ?> ><?php _e('Hide Dialog', 'dialog-contact-form'); ?><br>
		        </td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row">
	        		<label><?php _e('Form Label Text', 'dialog-contact-form'); ?></label>
	        	</th>
		        <td class="dcf_label">
		        	<label for="label_name"><?php _e('Label for Name', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[label_name]" id="label_name" value="<?php if(isset($options['label_name'])) echo $options['label_name']; ?>" class="regular-text"><br>
		        	
		        	<label for="label_email"><?php _e('Label for Email', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[label_email]" id="label_email" value="<?php if(isset($options['label_email'])) echo $options['label_email']; ?>" class="regular-text"><br>
		        	
		        	<label for="label_url"><?php _e('Label for Website', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[label_url]" id="label_url" value="<?php if(isset($options['label_url'])) echo $options['label_url']; ?>" class="regular-text"><br>
		        	
		        	<label for="label_phone"><?php _e('Label for Phone', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[label_phone]" id="label_phone" value="<?php if(isset($options['label_phone'])) echo $options['label_phone']; ?>" class="regular-text"><br>

		        	<label for="label_sub"><?php _e('Label for Subject', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[label_sub]" id="label_sub" value="<?php if(isset($options['label_sub'])) echo $options['label_sub']; ?>" class="regular-text"><br>

		        	<label for="label_msg"><?php _e('Label for Message', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[label_msg]" id="label_msg" value="<?php if(isset($options['label_msg'])) echo $options['label_msg']; ?>" class="regular-text"><br>

		        	<label for="label_capt"><?php _e('Label for Captcha', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[label_capt]" id="label_capt" value="<?php if(isset($options['label_capt'])) echo $options['label_capt']; ?>" class="regular-text"><br>

		        	<label for="label_submit"><?php _e('Label for Submit button', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[label_submit]" id="label_submit" value="<?php if(isset($options['label_submit'])) echo $options['label_submit']; ?>" class="regular-text"><br>
		        </td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row">
	        		<label><?php _e('Form Placeholder Text', 'dialog-contact-form'); ?></label>
	        	</th>
	        	<td class="dcf_label">
		        	<label for="place_name"><?php _e('Placeholder text for Name', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[place_name]" id="place_name" value="<?php if(isset($options['place_name'])) echo $options['place_name']; ?>" class="regular-text"><br>
		        	
		        	<label for="place_email"><?php _e('Placeholder text for Email', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[place_email]" id="place_email" value="<?php if(isset($options['place_email'])) echo $options['place_email']; ?>" class="regular-text"><br>
		        	
		        	<label for="place_url"><?php _e('Placeholder text for Website', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[place_url]" id="place_url" value="<?php if(isset($options['place_url'])) echo $options['place_url']; ?>" class="regular-text"><br>
		        	
		        	<label for="place_phone"><?php _e('Placeholder text for Phone', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[place_phone]" id="place_phone" value="<?php if(isset($options['place_phone'])) echo $options['place_phone']; ?>" class="regular-text"><br>

		        	<label for="place_sub"><?php _e('Placeholder text for Subject', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[place_sub]" id="place_sub" value="<?php if(isset($options['place_sub'])) echo $options['place_sub']; ?>" class="regular-text"><br>

		        	<label for="place_msg"><?php _e('Placeholder text for Message', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[place_msg]" id="place_msg" value="<?php if(isset($options['place_msg'])) echo $options['place_msg']; ?>" class="regular-text"><br>

		        	<label for="place_capt"><?php _e('Placeholder text for Captcha', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[place_capt]" id="place_capt" value="<?php if(isset($options['place_capt'])) echo $options['place_capt']; ?>" class="regular-text"><br>
	        	</td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row">
	        		<label><?php _e('Dialog', 'dialog-contact-form'); ?></label>
	        	</th>
	        	<td class="dcf_label">
		        	<label for="dialog_button"><?php _e('Dialog button text', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[dialog_button]" id="dialog_button" value="<?php if(isset($options['dialog_button'])) echo $options['dialog_button']; ?>" class="regular-text"><br>

		        	<label for="dialog_title"><?php _e('Dialog title text', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[dialog_title]" id="dialog_title" value="<?php if(isset($options['dialog_title'])) echo $options['dialog_title']; ?>" class="regular-text"><br>

		        	<label for="dialog_width"><?php _e('Dialog width', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[dialog_width]" id="dialog_width" value="<?php if(isset($options['dialog_width'])) echo $options['dialog_width']; ?>" class="regular-text"><br>

		        	<label for="dialog_color"><?php _e('Dialog button color', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[dialog_color]" id="dialog_color" value="<?php if(isset($options['dialog_color'])) echo $options['dialog_color']; ?>" class="colorpicker" data-default-color="#ea632d"><br>
	        	</td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row">
	        		<label><?php _e('Messages', 'dialog-contact-form'); ?></label>
	        	</th>
		        <td class="dcf_label_more">
		        	<label for="err_name"><?php _e('Validation errors occurred for name validation', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[err_name]" id="err_name" value="<?php if(isset($options['err_name'])) echo $options['err_name']; ?>" class="regular-text"><br>

		        	<label for="err_email"><?php _e('Validation errors occurred for email validation', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[err_email]" id="err_email" value="<?php if(isset($options['err_email'])) echo $options['err_email']; ?>" class="regular-text"><br>

		        	<label for="err_url"><?php _e('Validation errors occurred for URL validation', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[err_url]" id="err_url" value="<?php if(isset($options['err_url'])) echo $options['err_url']; ?>" class="regular-text"><br>

		        	<label for="err_message"><?php _e('Validation errors occurred for Message validation', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[err_message]" id="err_message" value="<?php if(isset($options['err_message'])) echo $options['err_message']; ?>" class="regular-text"><br>

		        	<label for="err_captcha"><?php _e('Validation errors occurred for captcha validation', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[err_captcha]" id="err_captcha" value="<?php if(isset($options['err_captcha'])) echo $options['err_captcha']; ?>" class="regular-text"><br>

		        	<label for="msg_success"><?php _e('Sender\'s message was sent successfully', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[msg_success]" id="msg_success" value="<?php if(isset($options['msg_success'])) echo $options['msg_success']; ?>" class="regular-text"><br>

		        	<label for="msg_fail"><?php _e('Sender\'s message was failed to send', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[msg_fail]" id="msg_fail" value="<?php if(isset($options['msg_fail'])) echo $options['msg_fail']; ?>" class="regular-text"><br>

		        	<label for="msg_subject"><?php _e('Message subject text.', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[msg_subject]" id="msg_subject" value="<?php if(isset($options['msg_subject'])) echo $options['msg_subject']; ?>" class="regular-text"><br>

		        	<label for="msg_body"><?php _e('Message body text.', 'dialog-contact-form'); ?></label>
		        	<input type="text" name="dialog-contact-form_options[msg_body]" id="msg_body" value="<?php if(isset($options['msg_body'])) echo $options['msg_body']; ?>" class="regular-text"><br>
		        </td>
	        </tr>
	    </table>
	    <?php submit_button(); ?>
	</form>
</div>