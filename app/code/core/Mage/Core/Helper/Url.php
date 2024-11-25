<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core URL helper
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_Url extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Core';

    /**
     * Retrieve current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        $request = Mage::app()->getRequest();
        $port = $request->getServer('SERVER_PORT');
        if ($port) {
            $defaultPorts = [
                Mage_Core_Controller_Request_Http::DEFAULT_HTTP_PORT,
                Mage_Core_Controller_Request_Http::DEFAULT_HTTPS_PORT
            ];
            $port = (in_array($port, $defaultPorts)) ? '' : ':' . $port;
        }
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $port . $request->getServer('REQUEST_URI');
        return $this->escapeUrl($url);
    }

    /**
     * Retrieve current url in base64 encoding
     *
     * @return string
     */
    public function getCurrentBase64Url()
    {
        return $this->urlEncode($this->getCurrentUrl());
    }

    /**
     * Return encoded url
     *
     * @param null|string $url
     * @return string
     */
    public function getEncodedUrl($url = null)
    {
        if (!$url) {
            $url = $this->getCurrentUrl();
        }
        return $this->urlEncode($url);
    }

    /**
     * Retrieve homepage url
     *
     * @return string
     */
    public function getHomeUrl()
    {
        return Mage::getBaseUrl();
    }

    /**
     * Formatting string
     *
     * @param string $string
     * @return string
     */
    protected function _prepareString($string)
    {
        $string = preg_replace('#[^0-9a-z]+#i', '-', $string);
        $string = strtolower($string);
        $string = trim($string, '-');

        return $string;
    }

    /**
     * Add request parameter into url
     *
     * @param string $url
     * @param array $param ( 'key' => value )
     * @return string
     */
    public function addRequestParam($url, $param)
    {
        $startDelimiter = (strpos($url, '?') === false) ? '?' : '&';

        $arrQueryParams = [];
        foreach ($param as $key => $value) {
            if (is_numeric($key) || is_object($value)) {
                continue;
            }

            if (is_array($value)) {
                $arrQueryParams[] = $key . '[]=' . implode('&' . $key . '[]=', $value);
            } elseif (is_null($value)) {
                $arrQueryParams[] = $key;
            } else {
                $arrQueryParams[] = $key . '=' . $value;
            }
        }
        $url .= $startDelimiter . implode('&', $arrQueryParams);

        return $url;
    }

    /**
     * Remove request parameter from url
     *
     * @param string $url
     * @param string $paramKey
     * @param bool $caseSensitive
     * @return string
     */
    public function removeRequestParam($url, $paramKey, $caseSensitive = false)
    {
        if (!str_contains($url, '?')) {
            return $url;
        }

        list($baseUrl, $query) = explode('?', $url, 2);
        parse_str($query, $params);

        if (!$caseSensitive) {
            $paramsLower = array_change_key_case($params);
            $paramKeyLower = strtolower($paramKey);

            if (array_key_exists($paramKeyLower, $paramsLower)) {
                $params[$paramKey] = $paramsLower[$paramKeyLower];
            }
        }

        if (array_key_exists($paramKey, $params)) {
            unset($params[$paramKey]);
        }

        return $baseUrl . ($params === [] ? '' : '?' . http_build_query($params));
    }

    /**
     * Return singleton model instance
     *
     * @param string $name
     * @param array $arguments
     * @return Mage_Core_Model_Abstract
     */
    protected function _getSingletonModel($name, $arguments = [])
    {
        return Mage::getSingleton($name, $arguments);
    }

    /**
     * Retrieve encoding domain name in punycode
     *
     * @param string $url encode url to Punycode
     * @return string
     */
    public function encodePunycode($url)
    {
        $parsedUrl = parse_url($url);
        if (!$this->_isPunycode($parsedUrl['host'])) {
            $host = idn_to_ascii($parsedUrl['host']);
            return str_replace($parsedUrl['host'], $host, $url);
        }

        return $url;
    }

    /**
     * Retrieve decoding domain name from punycode
     *
     * @param string $url decode url from Punycode
     * @return string
     * @throws Exception
     */
    public function decodePunycode($url)
    {
        $parsedUrl = parse_url($url);
        if ($this->_isPunycode($parsedUrl['host'])) {
            $host = idn_to_utf8($parsedUrl['host']);
            return str_replace($parsedUrl['host'], $host, $url);
        }

        return $url;
    }

    /**
     * Check domain name for IDN using ACE prefix http://tools.ietf.org/html/rfc3490#section-5
     *
     * @param string $host domain name
     * @return bool
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private function _isPunycode($host)
    {
        if (str_starts_with($host, 'xn--') || str_contains($host, '.xn--')
            || str_starts_with($host, 'XN--') || str_contains($host, '.XN--')
        ) {
            return true;
        }
        return false;
    }
}
