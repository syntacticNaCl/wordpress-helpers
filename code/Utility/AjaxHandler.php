<?php
namespace Zawntech\WordPress\Utility;

/**
 * Provides an extensible class for easily registering action callbacks to WP Ajax.
 * Class AjaxHandler
 * @package Zawntech\WordPress\Utility
 */
class AjaxHandler
{
    /**
     * @var array Public ajax action keys.
     */
    protected $public = [];

    /**
     * @var array Private ajax action keys.
     */
    protected $private = [];

    /**
     * @var array Public/private ajax action keys.
     */
    protected $any = [];

    /**
     * @var string Ajax action prefix.
     */
    protected $prefix = '';

    /**
     * Send a JSON response.
     * @param $data
     * @param int $responseCode
     */
    protected function json( $data, $responseCode = 200 )
    {
        Ajax::jsonResponse( $data, $responseCode );
    }

    /**
     * Send an error response.
     * @param $data
     * @param int $responseCode
     */
    protected function error( $data, $responseCode = 400 )
    {
        Ajax::jsonError( $data, $responseCode );
    }

    public function __construct()
    {
        // Public routes.
        foreach( $this->public as $actionKey )
        {
            add_action( "wp_ajax_nopriv_{$this->prefix}{$actionKey}", function() use ($actionKey) 
            {
                $this->{$actionKey}();
                exit;
            });
        }

        // Protected routes.
        foreach( $this->private as $actionKey )
        {
            add_action( "wp_ajax_{$this->prefix}{$actionKey}", function() use ($actionKey)
            {
                $this->{$actionKey}();
                exit;
            });
        }
        
        // Public and private routes.
        foreach( $this->any as $actionKey )
        {
            // Don't re-bind this public action.
            if ( ! in_array( $actionKey, $this->public ) )
            {
                add_action( "wp_ajax_nopriv_{$this->prefix}{$actionKey}", function() use ($actionKey)
                {
                    $this->{$actionKey}();
                    exit;
                });
            }
            // Don't re-bind this private action.
            if ( ! in_array( $actionKey, $this->private ) )
            {
                add_action( "wp_ajax_{$this->prefix}{$actionKey}", function() use ($actionKey)
                {
                    $this->{$actionKey}();
                    exit;
                });
            }
        }
    }
}