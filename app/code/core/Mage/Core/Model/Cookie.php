<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Mage_Adminhtml_Model_System_Config_Source_Cookie_Samesite as CookieSameSite;

/**
 * Core cookie model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Cookie
{
    public const XML_PATH_COOKIE_DOMAIN    = 'web/cookie/cookie_domain';

    public const XML_PATH_COOKIE_PATH      = 'web/cookie/cookie_path';

    public const XML_PATH_COOKIE_LIFETIME  = 'web/cookie/cookie_lifetime';

    public const XML_PATH_COOKIE_HTTPONLY  = 'web/cookie/cookie_httponly';

    public const XML_PATH_COOKIE_SAMESITE  = 'web/cookie/cookie_samesite';

    protected $_lifetime;

    /**
     * Store object
     *
     * @var Mage_Core_Model_Store|null
     */
    protected $_store;

    /**
     * Set Store object
     *
     * @param bool|int|Mage_Core_Model_Store|null|string $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = Mage::app()->getStore($store);
        return $this;
    }

    /**
     * Retrieve Store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore();
        }

        return $this->_store;
    }

    /**
     * Retrieve Request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Retrieve Response object
     *
     * @return Mage_Core_Controller_Response_Http
     */
    protected function _getResponse()
    {
        return Mage::app()->getResponse();
    }

    /**
     * Retrieve Domain for cookie
     *
     * @return string
     */
    public function getDomain()
    {
        $domain = $this->getConfigDomain();
        if (empty($domain)) {
            $domain = $this->_getRequest()->getHttpHost();
        }

        return $domain;
    }

    /**
     * Retrieve Config Domain for cookie
     *
     * @return string
     */
    public function getConfigDomain()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_COOKIE_DOMAIN, $this->getStore());
    }

    /**
     * Retrieve Path for cookie
     *
     * @return string
     */
    public function getPath()
    {
        $path = Mage::getStoreConfig(self::XML_PATH_COOKIE_PATH, $this->getStore());
        if (empty($path)) {
            $path = $this->_getRequest()->getBasePath();
        }

        return $path;
    }

    /**
     * Retrieve cookie lifetime
     *
     * @return int|string
     */
    public function getLifetime()
    {
        if (!is_null($this->_lifetime)) {
            $lifetime = $this->_lifetime;
        } else {
            $lifetime = Mage::getStoreConfig(self::XML_PATH_COOKIE_LIFETIME, $this->getStore());
        }

        if (!is_numeric($lifetime)) {
            $lifetime = 3600;
        }

        return $lifetime;
    }

    /**
     * Set cookie lifetime
     *
     * @param int $lifetime
     * @return $this
     */
    public function setLifetime($lifetime)
    {
        $this->_lifetime = (int) $lifetime;
        return $this;
    }

    /**
     * Retrieve use HTTP only flag
     *
     * @return bool|null
     */
    public function getHttponly()
    {
        $httponly = Mage::getStoreConfig(self::XML_PATH_COOKIE_HTTPONLY, $this->getStore());
        if (is_null($httponly)) {
            return null;
        }

        return (bool) $httponly;
    }

    /**
     * Retrieve use SameSite
     */
    public function getSameSite(): string
    {
        $sameSite = Mage::getStoreConfig(self::XML_PATH_COOKIE_SAMESITE, $this->getStore());
        if (is_null($sameSite)) {
            return CookieSameSite::NONE;
        }

        return (string) $sameSite;
    }

    /**
     * Is https secure request
     * Use secure on adminhtml only
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isSecure()
    {
        if ($this->getStore()->isAdmin()) {
            return $this->_getRequest()->isSecure();
        }

        // Use secure cookie if unsecure base url is actually secure
        if (str_starts_with($this->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, false), 'https:')) {
            return true;
        }

        return false;
    }

    /**
     * Set cookie
     *
     * @param string $name The cookie name
     * @param string $value The cookie value
     * @param int|bool $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param bool $httponly
     * @param string $sameSite
     * @return $this
     * @throws Zend_Controller_Response_Exception
     * @throws Mage_Core_Exception
     */
    public function set($name, $value, $period = null, $path = null, $domain = null, $secure = null, $httponly = null, $sameSite = null)
    {
        /**
         * Check headers sent
         */
        if (!$this->_getResponse()->canSendHeaders(false)) {
            return $this;
        }

        if ($period === true) {
            $period = 3600 * 24 * 365;
        } elseif (is_null($period)) {
            $period = $this->getLifetime();
        }

        if ($period == 0) {
            $expire = 0;
        } else {
            $expire = time() + $period;
        }

        if (is_null($path)) {
            $path = $this->getPath();
        }

        if (is_null($domain)) {
            $domain = $this->getDomain();
        }

        if (is_null($secure)) {
            $secure = $this->isSecure();
        }

        if (is_null($httponly)) {
            $httponly = $this->getHttponly();
        }

        if (is_null($sameSite)) {
            $sameSite = $this->getSameSite();
        }

        if ($sameSite === CookieSameSite::NONE) {
            // Enforce specification SameSite None requires secure
            $secure = true;
        }

        setcookie(
            $name,
            (string) $value,
            [
                'expires'  => $expire,
                'path'     => $path,
                'domain'   => $domain,
                'secure'   => $secure,
                'httponly' => $httponly,
                'samesite' => $sameSite,
            ],
        );

        return $this;
    }

    /**
     * Postpone cookie expiration time if cookie value defined
     *
     * @param string $name The cookie name
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param bool $httponly
     * @param string $sameSite
     * @return $this
     * @throws Zend_Controller_Response_Exception
     * @throws Mage_Core_Exception
     */
    public function renew($name, $period = null, $path = null, $domain = null, $secure = null, $httponly = null, $sameSite = null)
    {
        if (($period === null) && !$this->getLifetime()) {
            return $this;
        }

        $value = $this->_getRequest()->getCookie($name, false);
        if ($value !== false) {
            $this->set($name, $value, $period, $path, $domain, $secure, $httponly, $sameSite);
        }

        return $this;
    }

    /**
     * Retrieve cookie or false if not exists
     *
     * @param string $name The cookie name
     * @return mixed|false
     */
    public function get($name = null)
    {
        return $this->_getRequest()->getCookie($name, false);
    }

    /**
     * Delete cookie
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param int|bool $httponly
     * @param string $sameSite
     * @return $this
     * @throws Zend_Controller_Response_Exception
     * @throws Mage_Core_Exception
     */
    public function delete($name, $path = null, $domain = null, $secure = null, $httponly = null, $sameSite = null)
    {
        /**
         * Check headers sent
         */
        if (!$this->_getResponse()->canSendHeaders(false)) {
            return $this;
        }

        return $this->set($name, '', null, $path, $domain, $secure, $httponly, $sameSite);
    }
}
