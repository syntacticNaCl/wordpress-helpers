<?php
namespace Zawntech\WordPress\IO;

class IOManager
{
    public function registerSettingsSubPage()
    {
        add_options_page('IO Manager', 'IO Manager', 'manage_options', 'io-manager.php', function()
        {
            // Load IO Manager Javascripts
            wp_enqueue_script('io-ajax', WORDPRESS_HELPERS_URL . '/assets/js/view-models/io-manager/io-ajax.js');
            wp_enqueue_script('io-manager-import-options', WORDPRESS_HELPERS_URL . '/assets/js/view-models/io-manager/io-manager-import-options.js');
            wp_enqueue_script('io-manager-progress-bar', WORDPRESS_HELPERS_URL . '/assets/js/view-models/io-manager/io-progress-bar.js');
            wp_enqueue_script('io-importer', WORDPRESS_HELPERS_URL . '/assets/js/view-models/io-manager/io-importer.js');
            wp_enqueue_script('io-manager-importer', WORDPRESS_HELPERS_URL . '/assets/js/view-models/io-manager/io-manager-importer.js');
            wp_enqueue_script('io-manager-view-model', WORDPRESS_HELPERS_URL . '/assets/js/view-models/io-manager/io-manager-view-model.js');

            // Print view.
            echo view( 'admin.io-manager.main', [
                'options' => [
                    'securityKey' => SecurityKey::getKey(),
                    'settingsNonce' => wp_create_nonce('io-update-settings')
                ]
            ]);
        });
    }

    public function __construct()
    {
        // Register settings page.
        add_action( 'admin_menu', [$this, 'registerSettingsSubPage'] );
        
        // Register AJAX
        new IOAjaxHandler;
    }
}