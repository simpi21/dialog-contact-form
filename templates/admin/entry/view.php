<?php

use DialogContactForm\Entries\Entry;

$entry = new Entry();
$data  = $entry->get( $id );
var_dump( $data );
$entry->update( array( 'status' => 'read' ), array( 'id' => $id ) );
$meta_data = array();

if ( isset( $data['meta_data'] ) && is_array( $data['meta_data'] ) ) {
	$meta_data = $data['meta_data'];
	unset( $data['meta_data'] );
}

$form_id    = isset( $meta_data['form_id'] ) ? $meta_data['form_id'] : 0;
$created_at = isset( $meta_data['created_at'] ) ? $meta_data['created_at'] : 0;
$created_at = new \DateTime( $created_at );
$form_title = get_the_title( $form_id );
$form_title = sprintf( '%s : Entry # %s', $form_title, $meta_data['id'] );

$fields   = get_post_meta( $form_id, '_contact_form_fields', true );
$back_url = add_query_arg( array(
	'post_type' => 'dialog-contact-form',
	'page'      => 'dcf-entries',
), admin_url( 'edit.php' ) );

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
							<?php foreach ( $fields as $field ) { ?>
                                <tr>
                                    <th scope="row"><?php echo $field['field_title']; ?></th>
                                    <td>
										<?php
										$value = isset( $data[ $field['field_name'] ] ) ? $data[ $field['field_name'] ] : null;
										if ( is_string( $value ) ) {
											echo wpautop( $value );
										} elseif ( is_numeric( $value ) ) {
											if ( is_float( $value ) ) {
												echo floatval( $value );
											} else {
												echo intval( $value );
											}
										} elseif ( is_array( $value ) ) {
											echo implode( '<br>', $value );
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
                                <span class="value"><?php echo $meta_data['user_agent']; ?></span>
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
