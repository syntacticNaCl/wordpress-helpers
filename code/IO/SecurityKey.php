<?php
namespace Zawntech\WordPress\IO;

class SecurityKey
{
    /**
     * @var string The option key.
     */
    public static $key = 'io_security_key';

    /**
     * @var string A base prefix for generating hash keys.
     */
    protected static $base = 'hail-wanzwa';

    /**
     * @return string A random md5 hash.
     */
    protected static function getRandomKey()
    {
        // Integer.
        $int = mt_rand(1, 999999);

        // Get blog name
        $siteName = get_bloginfo('name');

        // Return an md5 hash.
        return md5( static::$base . $siteName . $int );
    }

    /**
     * @return string Get the prefixed option key.
     */
    protected static function getOptionKey()
    {
        return WORDPRESS_HELPERS_OPTIONS_PREFIX . static::$key;
    }

    /**
     * @param null $key Option
     */
    public static function setKey($key = null)
    {
        // Set key.
        if ( null === $key )
        {
            $key = static::getRandomKey();
        }

        // Set the option.
        update_option( static::getOptionKey(), $key );

        return self::getKey();
    }

    /**
     * @return mixed|void
     */
    public static function getKey()
    {
        // Get data.
        $data = get_option( static::getOptionKey() );

        if ( false === $data || "false" === $data || ! $data )
        {
            static::setKey();
            $data = get_option( static::getOptionKey() );
        }

        return $data;
    }
}