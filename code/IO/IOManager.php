<?php
namespace Zawntech\WordPress\IO;

class IOManager
{
    public function registerSettingsSubPage()
    {
        add_options_page('IO Manager', 'IO Manager', 'manage_options', 'io-manager.php', function()
        {
            // Load view model.
            wp_enqueue_script('io-manager-view-model', WORDPRESS_HELPERS_URL . '/assets/js/view-models/admin/pages/io-manager-view-model.js');

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