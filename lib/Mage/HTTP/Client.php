<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_HTTP
 */

/**
 * Factory for HTTP client classes
 *
 * @package    Mage_HTTP
 */

class Mage_HTTP_Client
{
    /**
     * Disallow to instantiate - pvt constructor
     */
    private function __construct() {}

    /**
     * Factory for HTTP client
     * @param string|false $frontend  'curl'/'socket' or false for auto-detect
     * @return Mage_HTTP_IClient
     */
    public static function getInstance($frontend = false)
    {
        if (false === $frontend) {
            $frontend = self::detectFrontend();
        }

        if (false === $frontend) {
            throw new Exception('Cannot find frontend automatically, set it manually');
        }

        $class = self::class . '_' . str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $frontend)));
        return new $class();
    }

    /**
     * Detects frontend type.
     * Priority is given to CURL
     *
     * @return string|false
     */
    protected static function detectFrontend()
    {
        if (function_exists('curl_init')) {
            return 'curl';
        }

        if (function_exists('fsockopen')) {
            return 'socket';
        }

        return false;
    }
}
