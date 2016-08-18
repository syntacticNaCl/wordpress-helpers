<?php
namespace Zawntech\WordPress\IO\Ajax;

use Zawntech\WordPress\IO\IODataDumper;
use Zawntech\WordPress\IO\SecurityKey;

trait IOAjaxRemoteTrait
{
    /**
     * Determines if a supplied security key is valid.
     */
    public function check_security_key()
    {
        echo json_encode( SecurityKey::getKey() === $_GET['securityKey'] );
    }

    public function dump_instance_data()
    {
        // Make dumper object.
        $dumper = new IODataDumper();

        // Dump database to json files.
        $dumper->exportToJson();

        // Print output.
        echo json_encode([

            'siteData' => [
                'name' => get_bloginfo('name'),
                'description' => get_bloginfo('description'),
                'wpurl' => get_bloginfo('wpurl'),
                'url' => get_bloginfo('url'),
                'admin_email' => get_bloginfo('admin_email'),
            ],

            'postTypes' => $dumper->getPostTypes(),

            'postTypesCount' => $dumper->postTypeCount,

            'usersCount' => $dumper->usersCount,

            'users' => $dumper->users,

            'json' => $dumper->getJsonList()
        ]);
    }
}