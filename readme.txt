=== Dialog Contact Form ===
Contributors: sayful
Tags: contact, contact form, email, feedback, form, modal, dialog
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 3.0.0-alpha1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt

Just another WordPress contact form plugin. Simple but flexible.

== Description ==

Just another WordPress contact form plugin. Simple but flexible.

= Features of Dialog Contact Forms include, but are not limited to: =
* Beginner friendly WordPress contact form plugin.
* Very simple and lightweight.
* Form submission via AJAX, allowing a seamless user experience without page refreshes
* No JavaScript dependency, Written in vanilla JavaScript.
* Design for modern browser but also works for older browsers.
* Support creating multiple forms.
* Support upload file as attachment.
* Support using multiple forms in same page.
* Option page for SMTP settings for better mail delivery.
* More than ten input field types.
* Option to add unlimited fields for each form.
* Google reCAPTCHA to protect your form from spam.
* Option to arrange and re-arrange fields as your need.
* Option to customize mail template as you want.
* Custom field width: Full, Three Quarters, Two Thirds, Half, One Third, One Quarter.
* Give your users a success message or redirect them elsewhere after they complete a form.

= For Developers =

The Dialog Contact Form framework is on [GitHub](https://github.com/sayful1/dialog-contact-form)! If you're a developer and want to help make Dialog Contact Form better, check it out.

== Frequently Asked Questions ==

= Where can I report a bug? =

Report bugs, suggest ideas, and participate in development at <a href="https://github.com/sayful1/dialog-contact-form">https://github.com/sayful1/dialog-contact-form</a>.


== Installation ==

Installing the plugins is just like installing other WordPress plugins. If you don't know how to install plugins, please review the option below:

* From your WordPress dashboard go to **Plugins > Add New**.
* Search for **Dialog Contact Form** in **Search Plugins** box.
* Find the WordPress Plugin named **Dialog Contact Form** by **Sayful Islam**.
* Click **Install Now** to install the **Dialog Contact Form** Plugin.
* The plugin will begin to download and install.
* Now just click **Activate** to activate the plugin.

If you still need help. visit [WordPress codex](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins)


== Screenshots ==

1. Screenshot of Dialog Contact Form on Dialog.
2. Screenshot of Dialog Contact Form on Page.
3. Screenshot of Dialog Contact Form of Setting Page.

== Upgrade Notice ==

= 3.0.0-alpha1 =

Version 3 is a major update. After upgrading to version 3, check form and settings.

== Changelog ==

= version 3.0.0-alpha1 =
* Feature   - MailChimp API integration.
* Feature   - Add entries table to save visitor submitted data.
* Feature   - Add client side validation using The HTML5 constraint validation API.
* Feature   - Add Acceptance field type.
* Feature   - Add "Preview Changes" button to preview form design.
* Added     - Moved validation message from individual form to settings page.
* Added     - Add polyfill for ClassList and validityState for IE 9.
* Added     - Add dcf- prefix in all css class.
* Added     - Add action manager for managing multiple form submission actions.
* Added     - Add support for multiple value for checkbox.
* Added     - Add IP Address field type.
* Added     - Add max_file_size, allowed_file_types, multiple_files option for file field.
* Added     - Add accept attribute on file field.
* Dev       - Add reCAPTCHA field.
* Dev       - Add select2 jQuery library for select fields.
* Dev       - Add PSR4 class loader for loading fields.
* Dev       - Add UploadedFile helper class for handling file upload.
* Dev       - Add Attachment class for upload, validate and store attachment.
* Dev       - Add PHP version check functionality.

= version 2.2.1 - 2018-04-24 =
* Fixed     - Google reCAPTCHA error message is not showing.

= version 2.2.0 - 2018-04-23 =
* Added     - Add alpha color picker.
* Added     - Add File attachment option.
* Added     - Add option to disable plugin default styles.
* Added     - Add gutenberg block to add form with live preview on new upcoming gutenberg editor.
* Fixed     - Google reCAPTCHA script is loading multiple times when use multiple form on same page.
* Fixed     - Fix extra space on select, radio and checkbox field.
* Dev       - Update send mail data validation rules.
* Dev       - Change validator methods to static mode.
* Dev       - Add Dialog_Contact_Form_Metabox class for adding metabox fields.
* Dev       - Add Dialog_Contact_Form_Settings_API class for adding setting page fields.
* Dev       - Add Dialog_Contact_Form_Form class for adding public facing form.

= version 2.1.0 - 2017-12-05 =
* Tweak      - Remove custom session for flash message.
* Tweak      - Use global variable for flash message if javaScript not enabled.

= version 2.0.1 - 2017-10-10 =
* Tweak      - Remove "Additional Mail Settings" for settings
* Tweak      - Added option to set mail from "Name" and "Email" from user submitted value.
* Fixed      - Fixed issue for adding slashes on message content.
* Tweak      - Hide form type field as it is not functional yet.

= version 2.0.0 - 2017-08-10 =
* Added     - Re-write from core.
* Added     - No JavaScript dependency, Written in vanilla JavaScript.
* Added     - Design for modern browser but also works for older browsers.
* Added     - Support creating multiple forms.
* Added     - Support using multiple forms in same page.
* Added     - Added option page for SMTP settings for better mail delivery.
* Added     - Added more than ten input field types.
* Added     - Added more than fifteen input field validation rules.
* Added     - Added option to customize each field validation message.
* Added     - Added Option to add unlimited fields for each form.
* Added     - Added Google reCAPTCHA to protect your form from spam.
* Added     - Option to arrange and re-arrange fields as your need.
* Added     - Option to customize mail template as you want.
* Added     - Custom field width: Full, Three Quarters, Two Thirds, Half, One Third, One Quarter.

= version 1.2.1 - 2017-02-23 =
* Fixed 	- Fixed 404 error for captcha file.
* Fixed 	- Fixed Cross-Site Scripting (XSS) issue.

= version 1.2.0 - 2017-01-18 =
* Added 	- AJAX form submission.
* Added  	- AJAX Captcha refresh.
* Removed 	- Removed dependency over "jquery-ui-dialog".
* and others security improvement

= version 1.1.0 =
* Implement more functionality to change everything as your need.

= version 1.0.1 =
* Added Option page.
* Option to change receiver email address.
* Option to show or hide Dialog Contact Form.

= version 1.0.0 =
* Initial release.
