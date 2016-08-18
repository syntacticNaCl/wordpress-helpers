<?php
namespace Zawntech\WordPress\Utility;

class Ajax
{
    public static function setHeaders()
    {
        header('Content-Type: application/json');
    }

    public static function setResponseCode($code = 200)
    {
        // Set the status code.
        http_response_code( $code );
    }

    public static function jsonResponse($data, $statusCode=200)
    {
        static::setResponseCode( $statusCode );
        echo json_encode( $data );
        exit;
    }

    public static function jsonError($data, $statusCode=400)
    {
        static::jsonResponse( $data, $statusCode );
    }
}