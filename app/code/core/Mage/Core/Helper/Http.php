<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core Http Helper
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_Http extends Mage_Core_Helper_Abstract
{
    public const XML_NODE_REMOTE_ADDR_HEADERS  = 'global/remote_addr_headers';

    protected $_moduleName = 'Mage_Core';

    /**
     * Remote address cache
     * @var string
     */
    protected $_remoteAddr;

    /**
     * Validate and retrieve user and password from HTTP
     * @param string|null $headers
     * @return array
     */
    public function authValidate($headers = null)
    {
        if (!is_null($headers)) {
            $_SERVER = $headers;
        }

        $user = '';
        $pass = '';

        // moshe's fix for CGI
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            foreach ($_SERVER as $k => $v) {
                if (substr($k, -18) === 'HTTP_AUTHORIZATION' && !empty($v)) {
                    $_SERVER['HTTP_AUTHORIZATION'] = $v;
                    break;
                }
            }
        }

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $user = $_SERVER['PHP_AUTH_USER'];
            $pass = $_SERVER['PHP_AUTH_PW'];
        } elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            // IIS Note:: For HTTP Authentication to work with IIS,
            // the PHP directive cgi.rfc2616_headers must be set to 0 (the default value).
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
            list($user, $pass) = explode(':', base64_decode(substr($auth, strpos($auth, " ") + 1)));
        } elseif (!empty($_SERVER['Authorization'])) {
            $auth = $_SERVER['Authorization'];
            list($user, $pass) = explode(':', base64_decode(substr($auth, strpos($auth, " ") + 1)));
        }

        if (!$user || !$pass) {
            $this->authFailed();
        }

        return [$user, $pass];
    }

    /**
     * Send auth failed Headers and exit
     *
     */
    public function authFailed()
    {
        Mage::app()->getResponse()
            ->setHeader('HTTP/1.1', '401 Unauthorized')
            ->setHeader('WWW-Authenticate', 'Basic realm="RSS Feeds"')
            ->setBody('<h1>401 Unauthorized</h1>')
            ->sendResponse();
        exit;
    }

    /**
     * Retrieve Remote Addresses Additional check Headers
     *
     * @return array
     */
    public function getRemoteAddrHeaders()
    {
        $headers = [];
        $element = Mage::getConfig()->getNode(self::XML_NODE_REMOTE_ADDR_HEADERS);
        if ($element instanceof Mage_Core_Model_Config_Element) {
            foreach ($element->children() as $node) {
                $headers[] = (string)$node;
            }
        }

        return $headers;
    }

    /**
     * Retrieve Client Remote Address
     *
     * @param bool $ipToLong converting IP to long format
     * @return false|string IPv4|long
     */
    public function getRemoteAddr($ipToLong = false)
    {
        if (is_null($this->_remoteAddr)) {
            $headers = $this->getRemoteAddrHeaders();
            foreach ($headers as $var) {
                if ($var != 'REMOTE_ADDR' && $this->_getRequest()->getServer($var, false)) {
                    $this->_remoteAddr = $_SERVER[$var];
                    break;
                }
            }

            if (!$this->_remoteAddr) {
                $this->_remoteAddr = $this->_getRequest()->getServer('REMOTE_ADDR');
            }
        }

        if (!$this->_remoteAddr) {
            return false;
        }

        if (strpos($this->_remoteAddr, ',') !== false) {
            $ipList = explode(',', $this->_remoteAddr);
            $this->_remoteAddr = trim(reset($ipList));
        }

        return $ipToLong ? inet_pton($this->_remoteAddr) : $this->_remoteAddr;
    }

    /**
     * Retrieve Server IP address
     *
     * @param bool $ipToLong converting IP to long format
     * @return false|string IPv4|long
     */
    public function getServerAddr($ipToLong = false)
    {
        $address = $this->_getRequest()->getServer('SERVER_ADDR');
        if (!$address) {
            return false;
        }
        return $ipToLong ? inet_pton($address) : $address;
    }

    /**
     * Retrieve HTTP "clean" value
     *
     * @param string $var
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    protected function _getHttpCleanValue($var, $clean = true)
    {
        $value = $this->_getRequest()->getServer($var, '');
        if ($clean) {
            $value = Mage::helper('core/string')->cleanString($value);
        }

        return $value;
    }

    /**
     * Retrieve HTTP HOST
     *
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpHost($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_HOST', $clean);
    }

    /**
     * Retrieve HTTP USER AGENT
     *
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpUserAgent($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_USER_AGENT', $clean);
    }

    /**
     * Retrieve HTTP ACCEPT LANGUAGE
     *
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpAcceptLanguage($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_ACCEPT_LANGUAGE', $clean);
    }

    /**
     * Retrieve HTTP ACCEPT CHARSET
     *
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpAcceptCharset($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_ACCEPT_CHARSET', $clean);
    }

    /**
     * Retrieve HTTP REFERER
     *
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpReferer($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_REFERER', $clean);
    }

    /**
     * Returns the REQUEST_URI taking into account
     * platform differences between Apache and IIS
     *
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    public function getRequestUri($clean = false)
    {
        $uri = $this->_getRequest()->getRequestUri();
        if ($clean) {
            $uri = Mage::helper('core/string')->cleanString($uri);
        }
        return $uri;
    }

    /**
     * Validate IP address
     *
     * @param string $address
     * @return bool
     */
    public function validateIpAddr($address)
    {
        return preg_match('#^(1?\d{1,2}|2([0-4]\d|5[0-5]))(\.(1?\d{1,2}|2([0-4]\d|5[0-5]))){3}$#', $address);
    }
}
