<?php
namespace Zawntech\WordPress\Utility;

class Ajax
{
    public static function setHeaders()
    {
        header('Content-Type: application/json');
    }
}