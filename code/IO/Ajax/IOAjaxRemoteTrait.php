<?php
namespace Zawntech\WordPress\IO\Ajax;

use Zawntech\WordPress\IO\PostDumper;

trait IOAjaxRemoteTrait
{
    public function dump_instance_data()
    {
        $postDumper = new PostDumper();
        $postDumper->dump();

        echo json_encode([

            'siteData' => [
                'name' => get_bloginfo('name'),
                'description' => get_bloginfo('description'),
                'wpurl' => get_bloginfo('wpurl'),
                'url' => get_bloginfo('url'),
                'admin_email' => get_bloginfo('admin_email'),
            ],

            'postTypes' => $postDumper->getPostTypes(),
        ]);
    }
}