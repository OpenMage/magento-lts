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
 * Interface for different HTTP clients
 *
 * @category   Mage
 * @package    Mage_HTTP
 */
interface Mage_HTTP_IClient
{
    /**
     * Set request timeout
     * @param int $value
     */
    public function setTimeout($value);

    /**
     * Set request headers from hash
     * @param array $headers
     */
    public function setHeaders($headers);

    /**
     * Add header to request
     * @param string $name
     * @param string $value
     */
    public function addHeader($name, $value);

    /**
     * Remove header from request
     * @param string $name
     */
    public function removeHeader($name);

    /**
     * Set login credentials
     * for basic auth.
     * @param string $login
     * @param string $pass
     */
    public function setCredentials($login, $pass);

    /**
     * Add cookie to request
     * @param string $name
     * @param string $value
     */
    public function addCookie($name, $value);

    /**
     * Remove cookie from request
     * @param string $name
     */
    public function removeCookie($name);

    /**
     * Set request cookies from hash
     * @param array $cookies
     */
    public function setCookies($cookies);

    /**
     * Remove cookies from request
     */
    public function removeCookies();

    /**
     * Make GET request
     * @param string $uri full uri
     */
    public function get($uri);

    /**
     * Make POST request
     * @param string $uri full uri
     * @param array $params POST fields array
     */
    public function post($uri, $params);

    /**
     * Get response headers
     * @return array
     */
    public function getHeaders();

    /**
     * Get response body
     * @return string
     */
    public function getBody();

    /**
     * Get response status code
     * @return int
     */
    public function getStatus();

    /**
     * Get response cookies (k=>v)
     * @return array
     */
    public function getCookies();

    /**
     * Set additional option
     * @param string $key
     * @param string $value
     */
    public function setOption($key, $value);

    /**
     * Set additional options
     * @param array $arr
     */
    public function setOptions($arr);
}
