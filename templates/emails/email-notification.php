<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fontFamily = 'font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;';
$style = array(
	/* Layout ------------------------------ */
	'body'                => 'margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;',
	'email-wrapper'       => 'width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;',

	/* Masthead ----------------------- */
	'email-masthead'      => 'padding: 25px 0; text-align: center;',
	'email-masthead_name' => 'font-size: 16px; font-weight: bold; color: #2F3133; text-decoration: none; text-shadow: 0 1px 0 white;',

	'email-body'       => 'width: 100%; margin: 0; padding: 35px 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FFF;',
	'email-body_inner' => 'width: auto; min-width: 300px; max-width: 600px; margin: 0 auto; padding: 0;',
	'email-body_cell'  => 'padding: 8px 10px;',

	'email-footer'      => 'width: auto; max-width: 600px; margin: 0 auto; padding: 0; text-align: center;',
	'email-footer_cell' => 'color: #AEAEAE; padding: 35px; text-align: center;',

	/* Field ------------------------------ */
	'field-label'       => $fontFamily . 'background: #F2F4F6; font-weight: bold; padding: 8px 10px;',
	'field-value'       => $fontFamily . 'padding: 8px 10px 35px;',

	/* Body ------------------------------ */
	'body_action'       => 'width: 100%; margin: 30px auto; padding: 0; text-align: center;',
	'body_sub'          => 'margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;',

	/* Type ------------------------------ */
	'anchor'            => 'color: #3869D4;',
	'header-1'          => 'margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;',
	'paragraph'         => 'margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;',
	'paragraph-sub'     => 'margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;',
	'paragraph-center'  => 'text-align: center;',

	/* Buttons ------------------------------ */
	'button'            => 'display: block; display: inline-block; width: 200px; min-height: 20px; padding: 10px;
                 background-color: #3869D4; border-radius: 3px; color: #ffffff; font-size: 15px; line-height: 25px;
                 text-align: center; text-decoration: none; -webkit-text-size-adjust: none;',

	'button--green' => 'background-color: #22BC66;',
	'button--red'   => 'background-color: #dc4d2f;',
	'button--blue'  => 'background-color: #3869D4;',
);

?><!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>

<body style="<?php echo $style['body']; ?>">

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="<?php echo $style['email-wrapper']; ?>" align="center">
            <table width="100%" cellpadding="0" cellspacing="0">
                <!-- Logo -->
                <tr>
                    <td style="<?php echo $style['email-masthead']; ?>">
                        <a style="<?php echo $fontFamily . $style['email-masthead_name']; ?>"
                           href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">
							<?php bloginfo( 'name' ); ?>
                        </a>
                    </td>
                </tr><!-- Logo End -->

                <!-- Body -->
                <tr>
                    <td style="<?php echo $style['email-body']; ?>" width="100%">
                        <table style="<?php echo $style['email-body_inner']; ?>" align="center" width="600"
                               cellpadding="0" cellspacing="0">
							<?php foreach ( $form_fields as $all_field ) {
								$value = str_replace( array( "\r\n", "\r", "\n" ), "<br>", $all_field['value'] );
								?>
                                <tr>
                                    <td style="<?php echo $style['field-label']; ?>">
										<?php echo $all_field['label']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="<?php echo $style['field-value']; ?>">
										<?php echo $value; ?>
                                    </td>
                                </tr>
							<?php } ?>
                        </table>
                    </td>
                </tr>
                <!-- Body -->

                <!-- Footer -->
                <tr>
                    <td>
                        <table style="<?php echo $style['email-footer']; ?>" align="center" width="600" cellpadding="0"
                               cellspacing="0">
                            <tr>
                                <td style="<?php echo $fontFamily . $style['email-footer_cell']; ?>">
                                    <p style="<?php echo $style['paragraph-sub']; ?>">
                                        &copy; <?php echo date( 'Y' ); ?>
                                        <a style="<?php echo $style['anchor']; ?>"
                                           href="<?php echo esc_url( home_url() ); ?>"
                                           target="_blank"><?php bloginfo( 'name' ); ?></a>.
                                        All rights reserved.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr><!-- Footer -->
            </table>
        </td>
    </tr>
</table>

</body>
</html>
