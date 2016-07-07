<?php
namespace Zawntech\WordPress;

class Role
{
    protected $key;

    protected $name;

    protected $capabilities = [
        'delete_posts' => true,
        'delete_published_posts' => true,
        'edit_posts' => true,
        'edit_published_posts' => true,
        'publish_posts' => true,
        'read' => true,
        'upload_files' => true,
    ];

    public function __construct()
    {
        remove_role($this->key);
        add_role($this->key, $this->name, $this->capabilities);
    }
}