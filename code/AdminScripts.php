<?php
namespace Zawntech\WordPress;

class AdminScripts
{
    public function registerScripts()
    {
        // Knockout JS
        wp_enqueue_script('knockout', WORDPRESS_HELPERS_URL . 'assets/js/lib/knockout.debug.js');

        // Knockout JS Mapping Plugin
        wp_enqueue_script('knockout-mapping', WORDPRESS_HELPERS_URL . 'assets/js/lib/knockout.mapping.js');

        // Register custom knockout components.
        wp_enqueue_script('zawntech-knockout-components', WORDPRESS_HELPERS_URL. 'assets/js/lib/zawntech-knockout-components.js');
    }

    public function __construct()
    {
        // Do nothing if not admin screen.
        if ( ! is_admin() ) {
            return;
        }

        // Enqueue scripts.
        add_action( 'admin_enqueue_scripts', [$this, 'registerScripts'] );
    }
}