<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_HTTP
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Factory for HTTP client classes
 *
 * @category   Mage
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
