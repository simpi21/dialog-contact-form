<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;
$form = Dialog_Contact_Form_Form::instance( $form_id );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
    <style type="text/css" media="screen">
        html {
            margin-top: 0 !important;
        }

        * html body {
            margin-top: 0 !important;
        }

        @media screen and ( max-width: 782px ) {
            html {
                margin-top: 0 !important;
            }

            * html body {
                margin-top: 0 !important;
            }
        }
    </style>
</head>
<body>
<div class="dcf-form">
    <div class="columns is-multiline">
		<?php echo $form->form_content(); ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        var frameEl = window.frameElement;
        // get the form element
        var $form = jQuery('.dcf-form');
        // get the height of the form
        var height = $form.find('.columns.is-multiline').outerHeight(true);

        if (frameEl) {
            frameEl.height = height;
        }
    });
</script>
</body>
</html>
