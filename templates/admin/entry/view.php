<?php

use DialogContactForm\Abstracts\Field;
use DialogContactForm\Entries\Entry;
use DialogContactForm\Supports\Browser;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = Entry::findById( $id );

// Update status to read
if ( 'unread' === $data->getStatus() ) {
	Entry::update( array( 'status' => 'read' ), array( 'id' => $id ) );
}

$browser    = new Browser( $data->getUserAgent() );
$user_agent = sprintf( '%s / %s %s', $browser->getPlatform(), $browser->getBrowser(), $browser->getVersion() );

$form       = $data->getForm();
$form_title = sprintf( '%s : Entry # %s', $form->getTitle(), $data->getId() );


$fields = array();
/** @var \DialogContactForm\Abstracts\Field $field */
foreach ( $form->getFormFields() as $field ) {
	$fields[ $field->getName() ] = $field;
}

if ( ! empty( $_REQUEST['redirect_to'] ) ) {
	$back_url = esc_url( rawurldecode( $_REQUEST['redirect_to'] ) );
} else {
	$back_url_args = array(
		'post_type' => 'dialog-contact-form',
		'page'      => 'dcf-entries',
	);
	if ( isset( $_REQUEST['form_id'] ) && is_numeric( $_REQUEST['form_id'] ) ) {
		$back_url_args['form_id'] = intval( $_REQUEST['form_id'] );
	}
	$back_url = add_query_arg( $back_url_args, admin_url( 'edit.php' ) );
}

?>
<style type="text/css">
    .dcf-data-table tr {
        border-bottom: 1px solid #f1f1f1;
    }

    .dcf-data-table tr:last-child {
        border-bottom-width: 0;
    }

    .submission-info-list {
        margin: 0 -12px 0;
        padding: 0;
    }

    .submission-info-list li {
        padding: 0.5em 1em;
        margin: 0;
    }

    .submission-info-list li:nth-child(even) {
        background: #f9f9f9;
    }

    .submission-info-list .label {
        font-weight: bold;
    }
</style>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Entry Details', 'dialog-contact-form' ); ?></h1>
    <a href="<?php echo esc_url( $back_url ); ?>"
       class="page-title-action"><?php esc_html_e( 'Back to Entries', 'dialog-contact-form' ); ?></a>
    <hr class="wp-header-end">

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="postbox">
                    <h2 class="hndle">
                        <span><?php echo $form_title; ?></span>
                    </h2>
                    <div class="inside">
                        <table class="form-table dcf-data-table">
							<?php
							foreach ( $data['field_values'] as $_key => $value ) {

								$field = isset( $fields[ $_key ] ) ? $fields[ $_key ] : null;

								if ( ! $field instanceof Field ) {
									continue;
								}

								if ( ! $field->showInEntry() ) {
									continue;
								}
								echo '<tr>';
								echo '<th scope="row">' . esc_html( $field->get( 'field_title' ) ) . '</th>';
								echo '<td>' . $data->formatFieldValue( $value ) . '</td>';
								echo '</tr>';
							}
							?>
                        </table>
                    </div>

                </div><!-- .dcf-tabs-wrapper -->
            </div><!-- #post-body-content -->

            <div id="postbox-container-1" class="postbox-container">
                <div class="postbox">
                    <h2 class="hndle">
                        <span><?php esc_html_e( 'Submission Info', 'dialog-contact-form' ); ?></span>
                    </h2>
                    <div class="inside">
                        <ul class="submission-info-list">
                            <li>
                                <span class="label"><?php esc_html_e( 'Entry ID', 'dialog-contact-form' ); ?></span>
                                <span class="sep">:</span>
                                <span class="value">#<?php echo $data->getId(); ?></span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Source', 'dialog-contact-form' ); ?></span>
                                <span class="sep">:</span>
                                <span class="value"><?php echo $data->getReferer(); ?></span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'User IP', 'dialog-contact-form' ); ?></span>
                                <span class="sep">:</span>
                                <span class="value"><?php echo $data->getUserIp(); ?></span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'User agent', 'dialog-contact-form' ); ?></span>
                                <span class="sep">:</span>
                                <span class="value"><?php echo $user_agent; ?></span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Date', 'dialog-contact-form' ); ?></span>
                                <span class="sep">:</span>
                                <span class="value"><?php echo $data->getCreatedAt()->format( 'r' ); ?></span>
                            </li>
                        </ul>
                        <div class="submitbox" id="submitpost" style="margin: 12px -12px -12px;">
                            <div id="major-publishing-actions">
                                <div id="publishing-action">
                                    <a href="#" class="button">
                                        <span class="dashicons dashicons-trash"></span>
										<?php esc_html_e( 'Delete', 'dialog-contact-form' ); ?>
                                    </a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- #postbox-container-1 -->

        </div><!-- #post-body -->
    </div>

</div>
