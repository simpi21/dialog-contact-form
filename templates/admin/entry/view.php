<?php

use DialogContactForm\Abstracts\Field;
use DialogContactForm\Entries\Entry;
use DialogContactForm\FieldManager;
use DialogContactForm\Supports\Browser;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$entry = new Entry();
$data  = $entry->get( $id );

// Update status to read
if ( isset( $data['meta_data']['status'] ) && 'unread' === $data['meta_data']['status'] ) {
	$entry->update( array( 'status' => 'read' ), array( 'id' => $id ) );
}

$meta_data = array();
if ( isset( $data['meta_data'] ) && is_array( $data['meta_data'] ) ) {
	$meta_data = $data['meta_data'];
	unset( $data['meta_data'] );
}

$browser    = new Browser( $meta_data['user_agent'] );
$user_agent = sprintf( '%s / %s %s', $browser->getPlatform(), $browser->getBrowser(), $browser->getVersion() );

$form_id    = isset( $meta_data['form_id'] ) ? $meta_data['form_id'] : 0;
$created_at = isset( $meta_data['created_at'] ) ? $meta_data['created_at'] : 0;
$created_at = new \DateTime( $created_at );
$form_title = get_the_title( $form_id );
$form_title = sprintf( '%s : Entry # %s', $form_title, $meta_data['id'] );

$_fields      = array();
$fields       = get_post_meta( $form_id, '_contact_form_fields', true );
$fieldManager = FieldManager::init();
foreach ( $fields as $field ) {
	$_fields[ $field['field_name'] ] = $field;
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
							<?php foreach ( $data as $_key => $value ) {
								$field = isset( $_fields[ $_key ] ) ? $_fields[ $_key ] : array();

								$className = $fieldManager->get( $field['field_type'] );
								if ( ! class_exists( $className ) ) {
									continue;
								}

								$class = new $className;
								if ( ! $class instanceof Field ) {
									continue;
								}

								if ( ! $class->showInEntry() ) {
									continue;
								}
								?>
                                <tr>
                                    <th scope="row">
										<?php
										if ( isset( $field['field_title'] ) ) {
											echo esc_html( $field['field_title'] );
										} else {
											esc_html_e( $_key );
										}
										?>
                                    </th>
                                    <td>
										<?php
										if ( is_string( $value ) ) {
											echo wpautop( $value );
										} elseif ( is_numeric( $value ) ) {
											if ( is_float( $value ) ) {
												echo floatval( $value );
											} else {
												echo intval( $value );
											}
										} elseif ( is_array( $value ) ) {
											foreach ( $value as $v_key => $v_value ) {
												if ( is_string( $v_value ) ) {
													echo $v_value;
												} elseif ( is_array( $v_value ) ) {
													if ( isset( $v_value['attachment_id'] ) && is_numeric( $v_value['attachment_id'] ) ) {
														$url = wp_get_attachment_url( $v_value['attachment_id'] );
														echo '<a href="' . $url . '" target="_blank">';
														echo wp_get_attachment_image( $v_value['attachment_id'] );
														echo '</a>';
													} else {
														echo implode( '<br>', $v_value );
													}
												}
											}
										}
										?>
                                    </td>
                                </tr>
							<?php } ?>
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
                                <span class="value">#<?php echo $meta_data['id']; ?></span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Source', 'dialog-contact-form' ); ?></span>
                                <span class="sep">:</span>
                                <span class="value"><?php echo site_url( $meta_data['referer'] ); ?></span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'User IP', 'dialog-contact-form' ); ?></span>
                                <span class="sep">:</span>
                                <span class="value"><?php echo $meta_data['user_ip']; ?></span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'User agent', 'dialog-contact-form' ); ?></span>
                                <span class="sep">:</span>
                                <span class="value"><?php echo $user_agent; ?></span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Date', 'dialog-contact-form' ); ?></span>
                                <span class="sep">:</span>
                                <span class="value"><?php echo $created_at->format( 'r' ); ?></span>
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
