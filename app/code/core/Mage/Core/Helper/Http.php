<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core Http Helper
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_Http extends Mage_Core_Helper_Abstract
{
    public const XML_NODE_REMOTE_ADDR_HEADERS  = 'global/remote_addr_headers';

    protected $_moduleName = 'Mage_Core';

    /**
     * Remote address cache
     * @var string|null
     */
    protected $_remoteAddr;

    /**
     * Validate and retrieve user and password from HTTP
     * @param array|null $headers
     * @return array
     * @SuppressWarnings("PHPMD.Superglobals")
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
                if (str_ends_with($k, 'HTTP_AUTHORIZATION') && !empty($v)) {
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
            [$user, $pass] = explode(':', base64_decode(substr($auth, strpos($auth, ' ') + 1)));
        } elseif (!empty($_SERVER['Authorization'])) {
            $auth = $_SERVER['Authorization'];
            [$user, $pass] = explode(':', base64_decode(substr($auth, strpos($auth, ' ') + 1)));
        }

        if (!$user || !$pass) {
            $this->authFailed();
        }

        return [$user, $pass];
    }

    /**
     * Send auth failed Headers and exit
     *
     * @return void
     * @SuppressWarnings("PHPMD.ExitExpression")
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
                $headers[] = (string) $node;
            }
        }

        return $headers;
    }

    /**
     * Retrieve Client Remote Address
     *
     * @param bool $ipToLong converting IP to long format
     * @return false|string IPv4|long
     * @SuppressWarnings("PHPMD.Superglobals")
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

        if (str_contains($this->_remoteAddr, ',')) {
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
