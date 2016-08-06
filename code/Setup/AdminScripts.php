<?php
namespace Zawntech\WordPress\Setup;

class AdminScripts
{
    /**
     * Enqueue scripts and styles.
     */
    public function registerScripts()
    {
        // jQuery UI components.
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-droppable');
        wp_enqueue_script('jquery-ui-sortable');

        // Validate JS
        wp_enqueue_script('validate-js', WORDPRESS_HELPERS_URL . 'assets/js/lib/validate.min.js');
        
        // Moment
        wp_enqueue_script('moment', WORDPRESS_HELPERS_URL . 'assets/js/lib/moment.min.js');
        wp_enqueue_script('moment-timezone', WORDPRESS_HELPERS_URL . 'assets/js/lib/moment-timezone.min.js');

        // Knockout JS
        wp_enqueue_script('knockout', WORDPRESS_HELPERS_URL . 'assets/js/lib/knockout.debug.js');

        // Knockout JS Mapping Plugin
        wp_enqueue_script('knockout-mapping', WORDPRESS_HELPERS_URL . 'assets/js/lib/knockout.mapping.js');

        // Register custom knockout components.
        wp_enqueue_script('zawntech-knockout-components', WORDPRESS_HELPERS_URL. 'assets/js/lib/knockout.components.js');

        // Twitter Bootstrap (JS)
        wp_enqueue_script('bootstrap', WORDPRESS_HELPERS_URL. 'assets/js/lib/bootstrap.min.js', ['jquery'], null, true);

        // Bootstrap datetime picker
        wp_enqueue_script('bootstrap-datetime-picker', WORDPRESS_HELPERS_URL. 'assets/js/lib/bootstrap-datetimepicker.min.js', ['jquery', 'bootstrap'], null, true);

        // Zawntech WP Helper CSS
        wp_enqueue_style('zawntech-compiled', WORDPRESS_HELPERS_URL. 'assets/css/zawntech-wordpress-helpers.css');

        // Expose global javascript.
        wp_localize_script('knockout', 'wordpress_helpers',
            [
                // Public URL to wordpress helpers directory.
                'assets' => WORDPRESS_HELPERS_URL . 'assets/'
            ]
        );
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