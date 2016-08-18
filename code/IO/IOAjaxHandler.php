<?php
namespace Zawntech\WordPress\IO;

use Zawntech\WordPress\IO\Ajax\IOAjaxLocalTrait;
use Zawntech\WordPress\IO\Ajax\IOAjaxRemoteTrait;
use Zawntech\WordPress\IO\Ajax\IOAjaxValidatorTrait;
use Zawntech\WordPress\Utility\Ajax;

class IOAjaxHandler
{
    use IOAjaxRemoteTrait,
        IOAjaxLocalTrait,
        IOAjaxValidatorTrait;
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function __construct()
    {
        // Define ajax calls.
        $publicAjaxCalls = [
            'dump_posts',
            'dump_instance_data',
            'check_security_key'
        ];

        // Define private ajax calls.
        $privateAjaxCalls =[
            'update_settings',
            'reset_security_key',
            'can_connect_to_remote',
            'get_remote_data'
        ];

        // Loop through defined ajax calls.
        foreach( $publicAjaxCalls as $function )
        {
            // Bind a closure.
            add_action( "wp_ajax_nopriv_io_{$function}", function() use ($function)
            {
                Ajax::setHeaders();
                $this->verifyPublicAccess();
                $this->$function();
                exit;
            });
        }

        foreach( $privateAjaxCalls as $function )
        {
            // Bind a closure.
            add_action( "wp_ajax_io_{$function}", function() use ($function)
            {
                Ajax::setHeaders();
                $this->verifyPrivateAccess();
                $this->$function();
                exit;
            });
        }
    }
}