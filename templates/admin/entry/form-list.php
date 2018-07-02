<?php

/** @var \wpdb $wpdb */
global $wpdb;

$forms = get_posts( array(
	'posts_per_page' => - 1,
	'orderby'        => 'ID',
	'order'          => 'DESC',
	'post_type'      => 'dialog-contact-form',
	'post_status'    => 'publish',
) );


$table   = $wpdb->prefix . "dcf_entries";
$query   = "SELECT status, form_id, COUNT( * ) AS num_entries FROM {$table} GROUP BY status, form_id";
$results = $wpdb->get_results( $query, ARRAY_A );

$counts = array();
foreach ( $results as $row ) {
	$counts[ $row['form_id'] ][ $row['status'] ] = intval( $row['num_entries'] );
}
?>
<style type="text/css">
    #screen-meta-links {
        display: none;
    }

    .dcf-form-list-item {
        background: white;
        margin-bottom: 20px;
        padding: 20px;
        text-align: center;
    }

    .dcf-form-list-item:before,
    .dcf-form-list-item:after {
        display: table;
        content: '';
    }

    .dcf-form-list-item:after {
        clear: both;
    }

    .dcf-form-list-item:first-child {
        margin-top: 20px;
    }

    .dcf-form-item-title {
        margin-top: 0;
        text-align: center;
    }

    .subsubsub {
        width: 100%;
        margin-top: 0;
    }
</style>
<div class="wrap">
    <div class="dcf-form-list">
		<?php
		foreach ( $forms as $form ) {
			$form_actions = (array) get_post_meta( $form->ID, '_contact_form_actions', true );
			if ( ! in_array( 'store_submission', $form_actions ) ) {
				continue;
			}

			$default_count   = array( 'unread' => 0, 'read' => 0, 'trash' => 0, );
			$_count          = isset( $counts[ $form->ID ] ) ? $counts[ $form->ID ] : array();
			$count           = wp_parse_args( $_count, $default_count );
			$all_url_args    = array(
				'post_type' => 'dialog-contact-form',
				'page'      => 'dcf-entries',
				'form_id'   => $form->ID,
			);
			$unread_url_args = $all_url_args + array( 'post_status' => 'unread' );
			$read_url_args   = $all_url_args + array( 'post_status' => 'read' );
			$trash_url_args  = $all_url_args + array( 'post_status' => 'trash' );

			$all_url    = add_query_arg( $all_url_args, admin_url( 'edit.php' ) );
			$unread_url = add_query_arg( $unread_url_args, admin_url( 'edit.php' ) );
			$read_url   = add_query_arg( $read_url_args, admin_url( 'edit.php' ) );
			$trash_url  = add_query_arg( $trash_url_args, admin_url( 'edit.php' ) );
			?>
            <div class="dcf-form-list-item">
                <h3 class="dcf-form-item-title">
					<?php echo get_the_title( $form ); ?>
                </h3>
                <ul class="subsubsub">
                    <li class="all">
                        <a href="<?php echo esc_url( $all_url ); ?>">
                            All (<?php echo $count['unread'] + $count['read']; ?>)
                        </a> |
                    </li>
                    <li class="unread">
                        <a href="<?php echo esc_url( $unread_url ); ?>">
                            Unread (<?php echo $count['unread']; ?>)
                        </a> |
                    </li>
                    <li class="read">
                        <a href="<?php echo esc_url( $read_url ); ?>">
                            Read (<?php echo $count['read']; ?>)
                        </a> |
                    </li>
                    <li class="trash">
                        <a href="<?php echo esc_url( $trash_url ); ?>">
                            Trash (<?php echo $count['trash']; ?>)
                        </a>
                    </li>
                </ul>
            </div>
			<?php
		}
		?>
    </div>
</div>
