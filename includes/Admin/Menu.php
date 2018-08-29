<?php

namespace DialogContactForm\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Menu {

    /**
     * @var self
     */
    private static $instance;

    /**
     * Ensures only one instance of this class is loaded or can be loaded.
     *
     * @return self
     */
    public static function init() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();

            add_action( 'admin_menu', array( self::$instance, 'admin_menu' ) );
        }

        return self::$instance;
    }

    /**
     * Add admin menu
     *
     * @return void
     */
    public function admin_menu() {
        global $submenu;
        $capability = 'manage_options';
        $slug       = 'dialog-contact-form';

        $hook = add_menu_page( __( 'Forms', 'dialog-contact-form' ), __( 'Forms', 'dialog-contact-form' ),
            $capability, $slug, array( $this, 'admin_menu_callback' ), 'dashicons-email-alt', 5.555 );

        $menus = [
            [ 'title' => __( 'All Forms', 'dialog-contact-form' ), 'slug' => '#/' ],
            [ 'title' => __( 'Entries', 'dialog-contact-form' ), 'slug' => '#/entries' ],
            [ 'title' => __( 'Settings', 'dialog-contact-form' ), 'slug' => '#/settings' ],
        ];

        if ( current_user_can( $capability ) ) {
            foreach ( $menus as $menu ) {
                $submenu[ $slug ][] = [ $menu['title'], $capability, 'admin.php?page=' . $slug . $menu['slug'] ];
            }
        }

        add_action( 'load-' . $hook, [ $this, 'init_hooks' ] );
    }

    /**
     * Admin menu callback
     *
     * @return void
     */
    public function admin_menu_callback() {
        echo '<div class="wrap"><div id="dialog-contact-form"></div></div>';
    }

    /**
     * Load assets for the menu
     *
     * @return void
     */
    public function init_hooks() {
        wp_enqueue_style( 'dialog-contact-form-admins' );
        wp_enqueue_script( 'dialog-contact-form-admins' );
        wp_localize_script( 'dialog-contact-form-admins', 'dcfApiSettings', array(
            'root'  => esc_url_raw( rest_url( 'dialog-contact-form/v1' ) ),
            'nonce' => wp_create_nonce( 'wp_rest' )
        ) );
    }
}
