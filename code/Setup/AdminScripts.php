<?php
namespace Zawntech\WordPress\Setup;

class AdminScripts
{
    /**
     * Enqueue scripts and styles.
     */
    public function registerScripts()
    {
        // Moment
        wp_enqueue_script('moment', WORDPRESS_HELPERS_URL . 'assets/js/lib/moment.min.js');
        wp_enqueue_script('moment-timezone', WORDPRESS_HELPERS_URL . 'assets/js/lib/moment-timezone.min.js');

        // Knockout JS
        wp_enqueue_script('knockout', WORDPRESS_HELPERS_URL . 'assets/js/lib/knockout.debug.js');

        // Knockout JS Mapping Plugin
        wp_enqueue_script('knockout-mapping', WORDPRESS_HELPERS_URL . 'assets/js/lib/knockout.mapping.js');

        // Register custom knockout components.
        wp_enqueue_script('zawntech-knockout-components', WORDPRESS_HELPERS_URL. 'assets/js/lib/zawntech-knockout-components.js');

        // Twitter Bootstrap (JS)
        wp_enqueue_script('bootstrap', WORDPRESS_HELPERS_URL. 'assets/js/lib/bootstrap.min.js', ['jquery'], null, true);

        // Bootstrap datetime picker
        wp_enqueue_script('bootstrap-datetime-picker', WORDPRESS_HELPERS_URL. 'assets/js/lib/bootstrap-datetimepicker.min.js', ['jquery', 'bootstrap'], null, true);

        // Zawntech WP Helper CSS
        wp_enqueue_style('zawntech-compiled', WORDPRESS_HELPERS_URL. 'assets/css/zawntech-wordpress-helpers.css');
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