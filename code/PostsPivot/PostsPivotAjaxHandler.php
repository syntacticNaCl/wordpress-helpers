<?php
namespace Zawntech\WordPress\PostsPivot;

class PostsPivotAjaxHandler
{
    protected function validateRequest()
    {

    }

    public function get()
    {
        echo 'get';
        exit;
    }

    public function attach()
    {
        echo 'attach';
        exit;
    }

    public function detach()
    {
        echo 'detach';
        exit;
    }

    public function __construct()
    {
        add_action( 'wp_ajax_posts_pivot_get', [$this, 'get'] );
        add_action( 'wp_ajax_posts_pivot_attach', [$this, 'get'] );
        add_action( 'wp_ajax_posts_pivot_detach', [$this, 'get'] );
    }
}