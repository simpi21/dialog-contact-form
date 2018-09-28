<?php

namespace DialogContactForm;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Assets {

    /**
     * @var self
     */
    private static $instance;

    /**
     * @return self
     */
    public static function init() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();

            add_action( 'wp_loaded', array( self::$instance, 'register' ) );
        }

        return self::$instance;
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function register() {
        $this->register_scripts( $this->get_scripts() );
        $this->register_styles( $this->get_styles() );
    }

    /**
     * Register scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    private function register_scripts( $scripts ) {
        foreach ( $scripts as $handle => $script ) {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;
            $version   = isset( $script['version'] ) ? $script['version'] : DIALOG_CONTACT_FORM_VERSION;
            wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
        }
    }

    /**
     * Register styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function register_styles( $styles ) {
        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;
            wp_register_style( $handle, $style['src'], $deps, DIALOG_CONTACT_FORM_VERSION );
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    public function get_scripts() {
        $prefix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $scripts = array(
            'dialog-contact-form-vendor' => array(
                'src'       => DIALOG_CONTACT_FORM_ASSETS . '/js/vendor' . $prefix . '.js',
                'deps'      => array(),
                'in_footer' => true
            ),
            'dialog-contact-form-admins' => array(
                'src'       => DIALOG_CONTACT_FORM_ASSETS . '/js/admin-vue' . $prefix . '.js',
                'deps'      => array( 'jquery', 'dialog-contact-form-vendor' ),
                'in_footer' => true
            )
        );

        return $scripts;
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_styles() {
        $styles = array(
            'dialog-contact-form-public' => array(
                'src' => DIALOG_CONTACT_FORM_ASSETS . '/css/public.css'
            ),
            'dialog-contact-form-admins' => array(
                'src' => DIALOG_CONTACT_FORM_ASSETS . '/css/admins.css'
            ),
        );

        return $styles;
    }
}
