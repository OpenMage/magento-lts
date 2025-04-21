<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_HTTP
 */

/**
 * Interface for different HTTP clients
 *
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
