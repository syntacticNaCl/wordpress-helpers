<?php
namespace Zawntech\WordPress\IO;

/**
 * Class RemoteInstance
 * @package Zawntech\WordPress\IO
 */
class RemoteInstance
{
    /**
     * @var \WP_Http
     */
    protected $http;

    /**
     * @var string URL to the remote WordPress instance.
     */
    protected $url;

    /**
     * @var string An MD5 string of the remote WordPress instance's IO Manager security key.
     */
    protected $securityKey;

    /**
     * @var bool Debug mode enabled?
     */
    protected $debug = true;
    
    /**
     * @var bool|string
     */
    protected $connectionError = false;

    /**
     * Get the remote's admin-ajax.php url.
     * @param array $params
     * @return string Return ajax URL.
     */
    protected function getAjaxUrl($action = '', $params = [])
    {
        // Determine last slash position in the url string.
        $lastSlashPos = strrpos( $this->url, '/' );

        // Get the URL base.
        $urlBase = $lastSlashPos > 8 ? substr( $this->url, $lastSlashPos ) : $this->url . '/';

        // Prepare remote admin ajax URL.
        $adminAjaxUrl = $urlBase . 'wp-admin/admin-ajax.php';

        // Prepare post data.
        $tokenQuery = [
            'action' => $action,
            'securityKey' => $this->securityKey
        ];

        // Prepare URL string.
        $url = $adminAjaxUrl . '?' . http_build_query( array_merge( $tokenQuery, $params ) );

        return $url;
    }

    /**
     * @return bool|string
     */
    public function getConnectionError()
    {
        return $this->connectionError;
    }

    /**
     * Determines if the supplied URL and SecurityKey are valid for a remote connection.
     * @return bool
     */
    public function canConnect()
    {
        /*----------------------------------------------
        | Step 1: Can we access the server?
        \*----------------------------------------------*/

        // Perform the request.
        $responseData = $this->http->get( $this->url );

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
        $responseData = $this->http->get( $adminAjaxUrl );

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

        // Get the ajax url.
        $url = $this->getAjaxUrl('io_check_security_key');

        // Perform request.
        $responseData = $this->http->post( $url );

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

    /**
     * @param $action
     * @param array $params
     * @return array|\WP_Error
     */
    protected function get($action, $params = [])
    {
        // Prepare the remote URL for the given action.
        $remoteUrl = $this->getAjaxUrl($action);

        // If URL parameters are supplied, append them to the request URL.
        if ( [] !== $params )
        {
            $paramsString = http_build_query( $params );
            $remoteUrl = '&' . $paramsString;
        }

        // Return the request.
        return $this->http->get( $remoteUrl );
    }

    public function getInstanceData()
    {
        // Perform request.
        $responseData = $this->get('io_dump_instance_data');

        // Good response?
        if ( 200 == $responseData['response']['code'] )
        {
            return json_decode( $responseData['body'] );
        } else {
            return false;
        }
    }

    /**
     * RemoteInstance constructor.
     * @param $url
     * @param $securityKey
     */
    public function __construct($url, $securityKey)
    {
        if( substr($url, -1) === '/')
        {
            $url = substr($url, 0, -1);
        }

        $this->url = $url;
        $this->securityKey = $securityKey;
        $this->http = new \WP_Http;
    }
}