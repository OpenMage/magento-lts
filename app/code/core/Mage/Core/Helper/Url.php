<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core URL helper
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_Url extends Mage_Core_Helper_Abstract
{

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
            $defaultPorts = array(
                Mage_Core_Controller_Request_Http::DEFAULT_HTTP_PORT,
                Mage_Core_Controller_Request_Http::DEFAULT_HTTPS_PORT
            );
            $port = (in_array($port, $defaultPorts)) ? '' : ':' . $port;
        }
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $port . $request->getServer('REQUEST_URI');
        return $this->escapeUrl($url);
//        return $this->_getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true));
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
     * @param  $url string
     * @param  $param array( 'key' => value )
     * @return string
     */
    public function addRequestParam($url, $param)
    {
        $startDelimiter = (false === strpos($url,'?'))? '?' : '&';

        $arrQueryParams = array();
        foreach ($param as $key => $value) {
            if (is_numeric($key) || is_object($value)) {
                continue;
            }

            if (is_array($value)) {
                // $key[]=$value1&$key[]=$value2 ...
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
     * @param boolean $caseSensitive
     * @return string
     */
    public function removeRequestParam($url, $paramKey, $caseSensitive = false)
    {
        $regExpression = '/\\?[^#]*?(' . preg_quote($paramKey, '/') . '\\=[^#&]*&?)/' . ($caseSensitive ? '' : 'i');
        while (preg_match($regExpression, $url, $mathes) != 0) {
            $paramString = $mathes[1];
            if (preg_match('/&$/', $paramString) == 0) {
                $url = preg_replace('/(&|\\?)?' . preg_quote($paramString, '/') . '/', '', $url);
            } else {
                $url = str_replace($paramString, '', $url);
            }
        }
        return $url;
    }

    /**
     * Return singleton model instance
     *
     * @param string $name
     * @param array $arguments
     * @return Mage_Core_Model_Abstract
     */
    protected function _getSingletonModel($name, $arguments = array())
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
            if (function_exists('idn_to_ascii')) {
                $host = idn_to_ascii($parsedUrl['host']);
            } else {
                $idn = new Net_IDNA2();
                $host = $idn->encode($parsedUrl['host']);
            }
            return str_replace($parsedUrl['host'], $host, $url);
        } else {
            return $url;
        }
    }

    /**
     * Retrieve decoding domain name from punycode
     *
     * @param string $url decode url from Punycode
     * @return string
     */
    public function decodePunycode($url)
    {
        $parsedUrl = parse_url($url);
        if ($this->_isPunycode($parsedUrl['host'])) {
            if (function_exists('idn_to_utf8')) {
                $host = idn_to_utf8($parsedUrl['host']);
            } else {
                $idn = new Net_IDNA2();
                $host = $idn->decode($parsedUrl['host']);
            }
            return str_replace($parsedUrl['host'], $host, $url);
        } else {
            return $url;
        }
    }

    /**
     * Check domain name for IDN using ACE prefix http://tools.ietf.org/html/rfc3490#section-5
     *
     * @param string $host domain name
     * @return boolean
     */
    private function _isPunycode($host)
    {
        if (strpos($host, 'xn--') === 0 || strpos($host, '.xn--') !== false
            || strpos($host, 'XN--') === 0 || strpos($host, '.XN--') !== false
        ) {
            return true;
        }
        return false;
    }
}
