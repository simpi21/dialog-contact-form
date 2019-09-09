<?php

namespace DialogContactForm\Admin;

use DialogContactForm\Collections\Actions;
use DialogContactForm\Collections\Fields;
use DialogContactForm\Collections\Templates;
use DialogContactForm\Supports\ContactForm;
use DialogContactForm\Supports\Metabox;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		add_action( 'admin_footer', array( $this, 'form_template' ), 0 );
		add_action( 'save_post', array( $this, 'save_meta' ), 10, 2 );
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
	}

	public function add_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=dialog-contact-form',
			__( 'Forms - beta', 'dialog-contact-form' ),
			__( 'Forms - beta', 'dialog-contact-form' ),
			'manage_options',
			'dcf-forms',
			array( $this, 'menu_page_callback' )
		);
	}

	public function menu_page_callback() {
		$data = [
			'templates' => [],
			'actions'   => [],
			'fields'    => [],
		];

		$templateManager = Templates::init();
		foreach ( $templateManager->getTemplatesByPriority() as $template ) {
			$data['templates'][] = $template->toArray();
		}

		$actionManager = Actions::init();
		foreach ( $actionManager->getActionsByPriority() as $action ) {
			$data['actions'][] = $action->toArray();
		}

		$actionManager = Fields::init();
		foreach ( $actionManager->getFieldsByPriority() as $field ) {
			$data['fields'][] = [
				'id'    => $field->getAdminId(),
				'title' => $field->getAdminLabel(),
				'icon'  => $field->getAdminIcon(),
			];
		}

		$data['settings'] = $this->get_form_settings();
		$data['messages'] = $this->get_form_messages();

		echo '<script>window.dialogContactForm = ' . wp_json_encode( $data ) . '</script>';
		echo '<div id="dialog-contact-form-admin-forms"></div>';
		include_once DIALOG_CONTACT_FORM_PATH . '/assets/icon/svg-icons.svg';
	}

	/**
	 * Get form Settings
	 *
	 * @return array
	 */
	public function get_form_settings() {
		$settings = [
			[
				'type'        => 'select',
				'id'          => 'labelPosition',
				'label'       => __( 'Position of the field title', 'dialog-contact-form' ),
				'description' => __( 'choose the position of the field title', 'dialog-contact-form' ),
				'default'     => 'both',
				'options'     => [
					'label'       => esc_html__( 'Label', 'dialog-contact-form' ),
					'placeholder' => esc_html__( 'Placeholder', 'dialog-contact-form' ),
					'both'        => esc_html__( 'Both label and placeholder', 'dialog-contact-form' ),
				],
			],
			[
				'type'        => 'text',
				'id'          => 'btnLabel',
				'label'       => __( 'Submit Button Label', 'dialog-contact-form' ),
				'description' => __( 'Define the label of submit button.', 'dialog-contact-form' ),
				'default'     => esc_html__( 'Send', 'dialog-contact-form' ),
			],
			[
				'type'        => 'radio-button',
				'id'          => 'btnAlign',
				'label'       => __( 'Submit Button Alignment', 'dialog-contact-form' ),
				'description' => __( 'Set the alignment of submit button.', 'dialog-contact-form' ),
				'default'     => 'left',
				'options'     => [
					'left'  => esc_html__( 'Left', 'dialog-contact-form' ),
					'right' => esc_html__( 'Right', 'dialog-contact-form' ),
				],
			],
			[
				'type'        => 'radio-button',
				'id'          => 'reset_form',
				'label'       => __( 'Reset form', 'dialog-contact-form' ),
				'description' => __( 'Choose Yes to reset form after successfully submission.', 'dialog-contact-form' ),
				'default'     => 'yes',
				'options'     => [
					'no'  => esc_html__( 'No', 'dialog-contact-form' ),
					'yes' => esc_html__( 'Yes', 'dialog-contact-form' ),
				],
			],
			[
				'type'    => 'radio-button',
				'id'      => 'recaptcha',
				'label'   => __( 'Enable Google reCAPTCHA', 'dialog-contact-form' ),
				'default' => 'no',
				'options' => [
					'no'  => esc_html__( 'No', 'dialog-contact-form' ),
					'yes' => esc_html__( 'Yes', 'dialog-contact-form' ),
				],
			]
		];

		return apply_filters( 'dialog_contact_form/form/settings', $settings );
	}

	/**
	 * Get form messages
	 *
	 * @return array
	 */
	public function get_form_messages() {
		$default  = Utils::validation_messages();
		$messages = [
			array(
				'type'    => 'textarea',
				'id'      => 'mail_sent_ng',
				'label'   => __( 'Message failed to sent', 'dialog-contact-form' ),
				'default' => $default['mail_sent_ng'],
			),
			array(
				'type'    => 'textarea',
				'id'      => 'validation_error',
				'label'   => __( 'Validation errors occurred', 'dialog-contact-form' ),
				'default' => $default['validation_error'],
			)
		];

		return apply_filters( 'dialog_contact_form/form/messages', $messages );
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
			'labels'              => apply_filters( 'dialog_contact_form/post_type/labels', $labels ),
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

		register_post_type( $this->post_type, apply_filters( 'dialog_contact_form/post_type/args', $args ) );
		add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'columns_head' ) );
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'content' ), 10, 2 );
		// Remove Quick Edit from list table
		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
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
                <label for="shortcode_<?php echo $post_id; ?>" class="screen-reader-text">
					<?php esc_html_e( 'Select shortcode', 'dialog-contact-form' ); ?>
                </label>
                <input id="shortcode_<?php echo $post_id; ?>" type="text" class="dcf-copy-shortcode"
                       value="[dialog_contact_form id='<?php echo $post_id; ?>']"
                       onmousedown="this.clicked = 1;"
                       onfocus="if (!this.clicked) this.select(); else this.clicked = 2;"
                       onclick="if (this.clicked === 2) this.select(); this.clicked = 0;"
                >
				<?php
				break;

			case 'entries':
				$entry_url     = add_query_arg( array(
					'post_type' => 'dialog-contact-form',
					'page'      => 'dcf-entries',
				), admin_url( 'edit.php' ) );
				$entry_url     = $entry_url . "#/forms/" . $post_id . "/entries/all";
				$count_entries = $this->count_entries();
				$entry_count   = isset( $count_entries[ $post_id ] ) ? $count_entries[ $post_id ] : 0;
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
		if ( $post->post_type === $this->post_type ) {
			if ( 'trash' !== $post->post_status ) {
				$new_actions = array();
				if ( current_user_can( 'edit_post', $post->ID ) ) {
					$preview_url = esc_url( add_query_arg( array(
						'dcf_forms_preview' => 1,
						'dcf_forms_iframe'  => 1,
						'form_id'           => $post->ID,
					), site_url() ) );

					$new_actions['edit'] = $actions['edit'];
					$new_actions['view'] = '<a href="' . $preview_url . '" target="_blank">' . __( 'Preview',
							'dialog-contact-form' ) . '</a>';
				}
				if ( current_user_can( 'delete_post', $post->ID ) ) {
					$new_actions['trash'] = $actions['trash'];
				}

				$actions = $new_actions;
			}
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
		$actionManager = Actions::init();
		$actions       = $actionManager->getActionsByPriority();
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
							<?php esc_html_e( 'Actions After Submit', 'dialog-contact-form' ); ?>
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
					Metabox::select( array(
						'id'          => '_contact_form_actions',
						'label'       => __( 'Add Actions', 'dialog-contact-form' ),
						'description' => __( 'Add actions that will be performed after a visitor submits the form (e.g. send an email notification). Choosing an action will add its setting below.',
							'dialog-contact-form' ),
						'multiple'    => true,
						'default'     => array(),
						'options'     => $this->get_actions_list( $actions ),
					) );

					$_actions = (array) get_post_meta( $post->ID, '_contact_form_actions', true );
					/** @var \DialogContactForm\Abstracts\Action $action */
					foreach ( $actions as $action ) {
						$display = in_array( $action->getId(), $_actions ) ? 'block' : 'none';
						echo '<div id="action-' . $action->getId() . '" data-id="closed" class="dcf-toggle dcf-toggle-action dcf-toggle--normal" style="display:' . $display . ';">';
						echo '<span class="dcf-toggle-title">' . $action->getTitle() . '</span>';
						echo '<div class="dcf-toggle-inner"><div class="dcf-toggle-content">';
						if ( $action->getDescription() ) {
							echo $action->getDescription();
						}
						$action->buildFields();
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
	 * Load field template on admin
	 */
	public function form_template() {
		global $post_type;
		if ( $post_type != DIALOG_CONTACT_FORM_POST_TYPE ) {
			return;
		}

		include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/form-template.php';
		include_once DIALOG_CONTACT_FORM_PATH . '/assets/icon/svg-icons.svg';
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
		add_meta_box(
			'dialog-contact-form-fields',
			__( 'Fields', 'dialog-contact-form' ),
			array( $this, 'form_fields' ),
			$this->post_type,
			'side',
			'high'
		);
	}

	/**
	 * Form fields list
	 */
	public function form_fields() {

		$default_class = 'dcf-fields-list is-half';
		$fieldManager  = Fields::init();
		$types         = $fieldManager->getFieldsByPriority();

		echo '<div class="dcf-fields-list-wrapper">';
		/** @var \DialogContactForm\Abstracts\Field $class */
		foreach ( $types as $index => $class ) {
			$input_class = ( $index % 2 === 0 ) ? $default_class . ' is-first' : $default_class . ' is-last';
			echo '<div class="' . $input_class . '" data-type="' . $class->getAdminId() . '">';
			echo $class->getAdminIcon() . ' <span>' . $class->getAdminLabel() . '</span>';
			echo '</div>';
		}
		echo '</div>';
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
        <label for="shortcode_<?php echo $post->ID; ?>" class="screen-reader-text">
			<?php esc_html_e( 'Select shortcode', 'dialog-contact-form' ); ?>
        </label>
        <input type="text" class="dcf-copy-shortcode widefat" id="shortcode_<?php echo $post->ID; ?>"
               onmousedown="this.clicked = 1;" value="<?php echo $shortcode; ?>"
               onfocus="if (!this.clicked) this.select(); else this.clicked = 2;"
               onclick="if (this.clicked === 2) this.select(); this.clicked = 0;"
        >
        <div class="submitbox" id="submitpost" style="margin: 12px -12px -12px;">
            <input type="hidden" id="post_status" name="post_status" value="publish">
            <div id="major-publishing-actions">
                <div id="preview-action" style="display: inline-block;">
                    <a class="preview button" href="<?php echo esc_url( $preview_url ); ?>" target="_blank">
						<?php esc_html_e( 'Preview Changes' ); ?>
                        <span class="screen-reader-text"> <?php esc_html_e( '(opens in a new window)' ); ?></span>
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

		$data = array(
			'config'   => isset( $_POST['config'] ) ? $_POST['config'] : array(),
			'messages' => isset( $_POST['messages'] ) ? $_POST['messages'] : array(),
			'field'    => isset( $_POST['field'] ) ? $_POST['field'] : array(),
		);

		$_actions = isset( $_POST['_contact_form_actions'] ) ? $_POST['_contact_form_actions'] : array();
		foreach ( $_actions as $action ) {
			$data['actions'][ $action ] = isset( $_POST[ $action ] ) ? $_POST[ $action ] : array();
		}

		ContactForm::update( $post_id, $data );

		/**
		 * Let give option to save settings for other plugins
		 *
		 * @param int $post_id The post ID.
		 * @param \WP_Post $post The post object.
		 */
		do_action( 'dialog_contact_form/save_post', $post_id, $post );
	}

	/**
	 * Count form entries
	 *
	 * @return array
	 */
	private function count_entries() {

		if ( ! $this->entries_count ) {
			global $wpdb;
			$table = $wpdb->prefix . "dcf_entries";

			$query   = "SELECT form_id, COUNT( * ) AS num_entries";
			$query   .= " FROM {$table} WHERE status != 'trash' GROUP BY form_id";
			$results = $wpdb->get_results( $query, ARRAY_A );

			$counts = array();
			foreach ( $results as $row ) {
				$counts[ $row['form_id'] ] = intval( $row['num_entries'] );
			}

			$this->entries_count = $counts;
		}

		return $this->entries_count;
	}

	/**
	 * List of after submit actions
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	private function get_actions_list( $actions ) {
		$list = array();
		/** @var \DialogContactForm\Abstracts\Action $action */
		foreach ( $actions as $action ) {
			$list[ $action->getId() ] = $action->getTitle();
		}

		return $list;
	}
}
