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

            add_action( 'wp_loaded', [ self::$instance, 'register' ] );
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
        $scripts = [
            'dialog-contact-form-vendor' => [
                'src'       => DIALOG_CONTACT_FORM_ASSETS . '/js/vendor' . $prefix . '.js',
                'deps'      => [],
                'in_footer' => true
            ],
            'dialog-contact-form-admins'  => [
                'src'       => DIALOG_CONTACT_FORM_ASSETS . '/js/admins' . $prefix . '.js',
                'deps'      => [ 'jquery', 'dialog-contact-form-vendor' ],
                'in_footer' => true
            ]
        ];

        return $scripts;
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_styles() {
        $styles = [
            'dialog-contact-form-public' => [
                'src' => DIALOG_CONTACT_FORM_ASSETS . '/css/public.css'
            ],
            'dialog-contact-form-admins'  => [
                'src' => DIALOG_CONTACT_FORM_ASSETS . '/css/admins.css'
            ],
        ];

        return $styles;
    }
}
