<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Dialog_Contact_Form_PostType' ) ) {

	class Dialog_Contact_Form_PostType {

		/**
		 * Dialog contact form post-type
		 *
		 * @var string
		 */
		private $post_type = DIALOG_CONTACT_FORM_POST_TYPE;

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

		/**
		 * DialogContactFormPostType constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'post_type' ) );
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
			$args   = array(
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
				'shortcode' => __( 'Shortcode', 'dialog-contact-form' )
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

				default:
					break;
			}
		}
	}
}

Dialog_Contact_Form_PostType::init();
