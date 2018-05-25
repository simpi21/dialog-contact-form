<?php

namespace DialogContactForm;

use DialogContactForm\ActionManager;

class Admin {

	/**
	 * Dialog contact form post-type
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * @var array
	 */
	private $entries_count = array();

	/**
	 * @var object
	 */
	private static $instance;

	/**
	 * Ensures only one instance of this class is loaded or can be loaded.
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->post_type = DIALOG_CONTACT_FORM_POST_TYPE;

		add_action( 'init', array( $this, 'post_type' ) );
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		add_action( 'admin_menu', array( $this, 'remove_submitdiv' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta' ), 10, 2 );
	}

	/**
	 * Register Custom Post Type
	 */
	public function post_type() {

		$labels = array(
			'name'               => _x( 'Forms', 'Post Type General Name', 'dialog-contact-form' ),
			'singular_name'      => _x( 'Form', 'Post Type Singular Name', 'dialog-contact-form' ),
			'menu_name'          => __( 'Forms', 'dialog-contact-form' ),
			'name_admin_bar'     => __( 'Form', 'dialog-contact-form' ),
			'all_items'          => __( 'All Forms', 'dialog-contact-form' ),
			'add_new_item'       => __( 'Add New Form', 'dialog-contact-form' ),
			'add_new'            => __( 'Add New', 'dialog-contact-form' ),
			'new_item'           => __( 'New Form', 'dialog-contact-form' ),
			'edit_item'          => __( 'Edit Form', 'dialog-contact-form' ),
			'update_item'        => __( 'Update Form', 'dialog-contact-form' ),
			'view_item'          => __( 'View Form', 'dialog-contact-form' ),
			'view_items'         => __( 'View Forms', 'dialog-contact-form' ),
			'search_items'       => __( 'Search Forms', 'dialog-contact-form' ),
			'not_found'          => __( 'No Forms', 'dialog-contact-form' ),
			'not_found_in_trash' => __( 'No Forms in Trash', 'dialog-contact-form' ),
		);

		$args = array(
			'label'               => __( 'Form', 'dialog-contact-form' ),
			'description'         => __( 'Simple but flexible WordPress contact form.', 'dialog-contact-form' ),
			'labels'              => apply_filters( 'dialog_contact_form_labels', $labels ),
			'supports'            => array( 'title', ),
			'hierarchical'        => false,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-email-alt',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'show_in_rest'        => false,
		);

		register_post_type( $this->post_type, apply_filters( 'dialog_contact_form_args', $args ) );
		add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'columns_head' ) );
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'content' ), 10, 2 );
		// Remove Quick Edit from list table
		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );

		$this->entries_count = $this->count_entries();
	}

	/**
	 * Add columns to Contact Forms admin
	 * Edit the columns in the table of contact forms post types
	 *
	 * @return array()
	 */
	public function columns_head() {
		$columns = array(
			'cb'        => '<input type="checkbox">',
			'title'     => __( 'Title', 'dialog-contact-form' ),
			'shortcode' => __( 'Shortcode', 'dialog-contact-form' ),
			'entries'   => __( 'Entries', 'dialog-contact-form' ),
		);

		return $columns;
	}


	/**
	 * Customize link column
	 * Customize the columns in the table of all post types
	 *
	 * @param string $column Column name
	 * @param int $post_id
	 */
	public function content( $column, $post_id ) {
		switch ( $column ) {
			case "shortcode":
				?>
                <input
                        type="text"
                        onmousedown="this.clicked = 1;"
                        onfocus="if (!this.clicked) this.select(); else this.clicked = 2;"
                        onclick="if (this.clicked === 2) this.select(); this.clicked = 0;"
                        value="[dialog_contact_form id='<?php echo $post_id; ?>']"
                        style="background-color: #f1f1f1;letter-spacing: 1px;min-width: 300px;padding: 5px 8px;"
                >
				<?php
				break;

			case 'entries':
				$entry_url   = add_query_arg( array(
					'post_type' => 'dialog-contact-form',
					'page'      => 'dcf-entries',
					'form_id'   => $post_id,
				), admin_url( 'edit.php' ) );
				$entry_count = isset( $this->entries_count[ $post_id ] ) ? $this->entries_count[ $post_id ] : 0;
				echo '<a href="' . esc_url( $entry_url ) . '">' . $entry_count . '</a>';
				break;
			default:
				break;
		}
	}

	/**
	 * Hide view and quick edit from carousel slider admin
	 *
	 * @param array $actions
	 * @param \WP_Post $post
	 *
	 * @return mixed
	 */
	public function post_row_actions( $actions, $post ) {
		if ( $post->post_type == $this->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}

	/**
	 * @param \WP_Post $post
	 *
	 * @return void
	 */
	public function edit_form_after_title( $post ) {
		if ( DIALOG_CONTACT_FORM_POST_TYPE !== $post->post_type ) {
			return;
		}
		$actions = ActionManager::init();
		?>
        <div class="dcf-tabs-wrapper">
            <div id="dcf-metabox-tabs" class="dcf-tabs">
                <ul class="dcf-tabs-list">
                    <li class="dcf-tab-list--fields">
                        <a href="#dcf-tab-1">
							<?php esc_html_e( 'Form Fields', 'dialog-contact-form' ); ?>
                        </a>
                    </li>
                    <li class="dcf-tab-list--configuration">
                        <a href="#dcf-tab-2">
							<?php esc_html_e( 'Form Settings', 'dialog-contact-form' ); ?>
                        </a>
                    </li>
                    <li class="dcf-tab-list--mail">
                        <a href="#dcf-tab-3">
							<?php esc_html_e( 'Emails & Actions', 'dialog-contact-form' ); ?>
                        </a>
                    </li>
                    <li class="dcf-tab-list--message">
                        <a href="#dcf-tab-4">
							<?php esc_html_e( 'Validation Messages', 'dialog-contact-form' ); ?>
                        </a>
                    </li>
                </ul>
                <div id="dcf-tab-1" class="dcf_options_panel">
					<?php include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/fields.php'; ?>
                </div>
                <div id="dcf-tab-2" class="dcf_options_panel">
					<?php include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/configuration.php'; ?>
                </div>
                <div id="dcf-tab-3" class="dcf_options_panel">
					<?php
					/** @var \DialogContactForm\Abstracts\Abstract_Action $action */
					foreach ( $actions as $action ) {
						echo '<div data-id="closed" class="dcf-toggle dcf-toggle--normal">';
						echo '<span class="dcf-toggle-title">' . $action->get_title() . '</span>';
						echo '<div class="dcf-toggle-inner"><div class="dcf-toggle-content">';
						if ( $action->get_description() ) {
							echo '<p class="description">' . $action->get_description() . '</p>';
						}
						$action->build_fields();
						echo '</div></div>';
						echo '</div>';
					}
					?>
                </div>
                <div id="dcf-tab-4" class="dcf_options_panel">
					<?php include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/messages.php'; ?>
                </div>
            </div>
        </div>
		<?php

	}

	/**
	 * Remove submit div form post type
	 */
	public function remove_submitdiv() {
		remove_meta_box( 'submitdiv', $this->post_type, 'side' );
	}

	/**
	 * Add carousel slider meta box
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'dialog-contact-form-usage',
			__( 'Usage', 'dialog-contact-form' ),
			array( $this, 'meta_box_shortcode_cb' ),
			$this->post_type,
			'side',
			'high'
		);
	}

	/**
	 * Metabox shortcode callback
	 *
	 * @param \WP_Post $post
	 */
	public function meta_box_shortcode_cb( $post ) {
		$shortcode   = sprintf( '[dialog_contact_form id=\'%s\']', $post->ID );
		$preview_url = add_query_arg( array(
			'dcf_forms_preview' => true,
			'dcf_forms_iframe'  => true,
			'form_id'           => $post->ID,
		), site_url() );
		?>
        <p><?php esc_html_e( 'Copy this shortcode and paste it into your post, page, or text widget content:
', 'dialog-contact-form' ); ?></p>
        <input
                type="text"
                onmousedown="this.clicked = 1;"
                onfocus="if (!this.clicked) this.select(); else this.clicked = 2;"
                onclick="if (this.clicked === 2) this.select(); this.clicked = 0;"
                value="<?php echo $shortcode; ?>"
                style="background-color: #f1f1f1;letter-spacing: 1px;width: 100%;padding: 5px 8px;"
        >
        <div class="submitbox" id="submitpost" style="margin: 12px -12px -12px;">
            <input type="hidden" id="post_status" name="post_status" value="publish">
            <div id="major-publishing-actions">
                <div id="preview-action" style="display: inline-block;">
                    <a class="preview button" href="<?php echo esc_url( $preview_url ); ?>" target="_blank">
                        Preview Changes
                        <span class="screen-reader-text"> (opens in a new window)</span>
                    </a>
                </div>
                <div id="publishing-action">
                    <span class="spinner"></span>
                    <input name="original_publish" type="hidden" id="original_publish" value="Update">
                    <input name="save" type="submit" class="button button-primary button-large" id="publish"
                           value="Update">
                </div>
                <div class="clear"></div>
            </div>
        </div>
		<?php
	}

	/**
	 * Save post metadata when a post is saved.
	 *
	 * @param int $post_id The post ID.
	 * @param \WP_Post $post The post object.
	 */
	public function save_meta( $post_id, $post ) {
		// If this isn't a 'contact-form' post, don't update it.
		if ( $post->post_type !== $this->post_type ) {
			return;
		}

		// - Update the post's metadata.
		if ( isset( $_POST['config'] ) ) {
			update_post_meta( $post_id, '_contact_form_config', self::sanitize_value( $_POST['config'] ) );
		}

		if ( isset( $_POST['messages'] ) ) {
			update_post_meta( $post_id, '_contact_form_messages', self::sanitize_value( $_POST['messages'] ) );
		}

		if ( isset( $_POST['field'] ) && is_array( $_POST['field'] ) ) {
			$_data = array();
			foreach ( $_POST['field'] as $field ) {
				$_data[] = array(
					'field_title'        => isset( $field['field_title'] ) ? sanitize_text_field( $field['field_title'] ) : '',
					'field_name'         => isset( $field['field_id'] ) ? sanitize_text_field( $field['field_id'] ) : '',
					'field_id'           => isset( $field['field_id'] ) ? sanitize_text_field( $field['field_id'] ) : '',
					'field_type'         => isset( $field['field_type'] ) ? sanitize_text_field( $field['field_type'] ) : '',
					'options'            => isset( $field['options'] ) ? wp_strip_all_tags( $field['options'] ) : '',
					'number_min'         => isset( $field['number_min'] ) ? $this->positive_int( $field['number_min'] ) : '',
					'number_max'         => isset( $field['number_max'] ) ? $this->positive_int( $field['number_max'] ) : '',
					'number_step'        => isset( $field['number_step'] ) ? $this->positive_int( $field['number_step'] ) : '',
					'field_value'        => isset( $field['field_value'] ) ? sanitize_text_field( $field['field_value'] ) : '',
					'required_field'     => isset( $field['required_field'] ) ? sanitize_text_field( $field['required_field'] ) : '',
					'field_class'        => isset( $field['field_class'] ) ? sanitize_text_field( $field['field_class'] ) : '',
					'field_width'        => isset( $field['field_width'] ) ? sanitize_text_field( $field['field_width'] ) : '',
					'validation'         => isset( $field['validation'] ) ? self::sanitize_value( $field['validation'] ) : array(),
					'placeholder'        => isset( $field['placeholder'] ) ? sanitize_text_field( $field['placeholder'] ) : '',
					'error_message'      => isset( $field['error_message'] ) ? sanitize_text_field( $field['error_message'] ) : '',
					// Acceptance Field
					'acceptance_text'    => isset( $field['acceptance_text'] ) ? wp_kses_post( $field['acceptance_text'] ) : '',
					'checked_by_default' => isset( $field['checked_by_default'] ) ? sanitize_text_field( $field['checked_by_default'] ) : '',
					// Date Field
					'min_date'           => isset( $field['min_date'] ) ? sanitize_text_field( $field['min_date'] ) : '',
					'max_date'           => isset( $field['max_date'] ) ? sanitize_text_field( $field['max_date'] ) : '',
					// Date & Time Field
					'native_html5'       => isset( $field['native_html5'] ) ? sanitize_text_field( $field['native_html5'] ) : '',
					// File Field
					'max_file_size'      => isset( $field['max_file_size'] ) ? absint( $field['max_file_size'] ) : '',
					'allowed_file_types' => isset( $field['allowed_file_types'] ) ? self::sanitize_value( $field['allowed_file_types'] ) : array(),
					'multiple_files'     => isset( $field['multiple_files'] ) ? sanitize_text_field( $field['multiple_files'] ) : '',
				);
			}

			update_post_meta( $post_id, '_contact_form_fields', $_data );
		}

		$actions = ActionManager::init();
		/** @var \DialogContactForm\Abstracts\Abstract_Action $action */
		foreach ( $actions as $action ) {
			$action->save( $post_id, $post );
		}

		/**
		 * Let give option to save settings for other plugins
		 *
		 * @param int $post_id The post ID.
		 * @param \WP_Post $post The post object.
		 */
		do_action( 'dialog_contact_form_save_post', $post_id, $post );
	}

	/**
	 * Sanitize meta value
	 *
	 * @param $input
	 *
	 * @return array|string
	 */
	private static function sanitize_value( $input ) {
		// Initialize the new array that will hold the sanitize values
		$new_input = array();

		if ( is_array( $input ) ) {
			// Loop through the input and sanitize each of the values
			foreach ( $input as $key => $value ) {
				if ( is_array( $value ) ) {
					$new_input[ $key ] = self::sanitize_value( $value );
				} else {
					$new_input[ $key ] = sanitize_text_field( $value );
				}
			}
		} else {
			return sanitize_text_field( $input );
		}

		return $new_input;
	}

	/**
	 * Check if the value is a positive integer
	 *
	 * @param $value
	 *
	 * @return int|null
	 */
	private function positive_int( $value ) {
		$value = preg_replace( '/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $value );
		if ( empty( $value ) ) {
			return null;
		}

		return $value;
	}

	/**
	 * Count form entries
	 *
	 * @return array
	 */
	private function count_entries() {
		$counts = wp_cache_get( 'form_entries_count', 'dialog-contact-form' );

		if ( false === $counts ) {
			global $wpdb;
			$table = $wpdb->prefix . "dcf_entries";

			$query   = "SELECT form_id, COUNT( * ) AS num_entries FROM {$table} GROUP BY form_id";
			$results = $wpdb->get_results( $query, ARRAY_A );

			$counts = array();
			foreach ( $results as $row ) {
				$counts[ $row['form_id'] ] = intval( $row['num_entries'] );
			}

			wp_cache_set( 'form_entries_count', $counts, 'dialog-contact-form' );
		}

		return $counts;
	}
}