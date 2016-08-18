<?php
namespace Zawntech\WordPress\IO;

class RemoteInstance
{
    protected $url;
    protected $securityKey;
    protected $debug = true;
    
    /**
     * @var bool|string
     */
    protected $connectionError = false;

    public function canConnect()
    {
        // Instantiate a WP_Http object.
        $http = new \WP_Http();

        /*----------------------------------------------
        | Step 1: Can we access the server?
        \*----------------------------------------------*/

        // Perform the request.
        $responseData = $http->get( $this->url );

        // Reference the response code.
        $responseCode = $responseData['response']['code'];

        // Debug.
        if ( $this->debug )
        {
            IOLogger::log([
                'test' => 'Can we access the server?',
                'url' => $this->url,
                'response' => $responseData
            ]);
        }

        // This check is local to my network, because AT&T hijacks my requests. Neat.
        if ( false !== strpos( $responseData['body'], 'http://dnserrorassist.att.net' ) )
        {
            $this->connectionError = 'ATT strikes again.';
            return false;
        }

        // Check that we received a 200 response code.
        if ( 200 !== $responseCode )
        {
            $this->connectionError = 'Invalid response code when connecting: ' . $responseCode;
            return false;
        }

        /*----------------------------------------------
        | Step 2: Can we access the admin-ajax.php
        \*----------------------------------------------*/

        // Determine last slash position in the url string.
        $lastSlashPos = strrpos( $this->url, '/' );

        // Get the URL base.
        $urlBase = $lastSlashPos > 8 ? substr( $this->url, $lastSlashPos ) : $this->url . '/';

        // Prepare remote admin ajax URL.
        $adminAjaxUrl = $urlBase . 'wp-admin/admin-ajax.php';

        // Perform the request.
        $responseData = $http->get( $adminAjaxUrl );

        // Reference the response code.
        $responseCode = $responseData['response']['code'];

        // Debug.
        if ( $this->debug )
        {
            IOLogger::log([
                'test' => 'Can we access the remote server\'s admin-ajax.php?',
                'url' => $adminAjaxUrl,
                'response' => $responseData
            ]);
        }

        // Check that we received a 200 response code.
        if ( 200 !== $responseCode )
        {
            $this->connectionError = 'Invalid response code when connecting to the remote admin-ajax.php:' . $responseCode;
            return false;
        }

        // We should get a response body of "0"
        if ( '0' !== $responseData['body'] )
        {
            $this->connectionError = 'Invalid response from admin-ajax.php, expecting "0"';
            return false;
        }

        /*----------------------------------------------
        | Step 3: Can we authorize the security key?
        \*----------------------------------------------*/

        // Prepare post data.
        $tokenQuery = [
            'action' => 'io_check_security_key',
            'securityKey' => $this->securityKey
        ];

        $url = $adminAjaxUrl . '?' . http_build_query( $tokenQuery );

        // Perform request.
        $responseData = $http->post( $url );

        // Debug.
        if ( $this->debug )
        {
            IOLogger::log([
                'test' => 'Can we authenticate the security key on the remote?',
                'url' => $url,
                'response' => $responseData
            ]);
        }

        // Check that we received a 200 response code.
        if ( 200 !== $responseCode )
        {
            $this->connectionError = 'Invalid response code when checking security token:' . $responseCode;
            return false;
        }

        // If the response body is not "true", then an invalid token was supplied.
        if ( true !== json_decode( $responseData['body'] ) )
        {
            $this->connectionError = 'Invalid security token!';
            return false;
        }

        // All good!
        return true;
    }
    
    public function __construct($url, $securityKey)
    {
        $this->url = $url;
        $this->securityKey = $securityKey;
    }
}