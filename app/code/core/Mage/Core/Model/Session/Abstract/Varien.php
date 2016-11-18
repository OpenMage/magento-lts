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


class Mage_Core_Model_Session_Abstract_Varien extends Varien_Object
{
    const VALIDATOR_KEY                         = '_session_validator_data';
    const VALIDATOR_HTTP_USER_AGENT_KEY         = 'http_user_agent';
    const VALIDATOR_HTTP_X_FORVARDED_FOR_KEY    = 'http_x_forwarded_for';
    const VALIDATOR_HTTP_VIA_KEY                = 'http_via';
    const VALIDATOR_REMOTE_ADDR_KEY             = 'remote_addr';
    const VALIDATOR_SESSION_EXPIRE_TIMESTAMP    = 'session_expire_timestamp';
    const SECURE_COOKIE_CHECK_KEY               = '_secure_cookie_check';

    /**
     * Map of session enabled hosts
     * @example array('host.name' => true)
     * @var array
     */
    protected $_sessionHosts = array();

    /**
     * Configure and start session
     *
     * @param string $sessionName
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function start($sessionName=null)
    {
        if (isset($_SESSION) && !$this->getSkipEmptySessionCheck()) {
            return $this;
        }

        // getSessionSaveMethod has to return correct version of handler in any case
        $moduleName = $this->getSessionSaveMethod();
        switch ($moduleName) {
            /**
             * backward compatibility with db argument (option is @deprecated after 1.12.0.2)
             */
            case 'db':
                $moduleName = 'user';
                /* @var $sessionResource Mage_Core_Model_Resource_Session */
                $sessionResource = Mage::getResourceSingleton('core/session');
                $sessionResource->setSaveHandler();
                break;
            case 'user':
                // getSessionSavePath represents static function for custom session handler setup
                call_user_func($this->getSessionSavePath());
                break;
            case 'files':
                //don't change path if it's not writable
                if (!is_writable($this->getSessionSavePath())) {
                    break;
                }
            default:
                session_save_path($this->getSessionSavePath());
                break;
        }
        session_module_name($moduleName);

        $cookie = $this->getCookie();
        if (Mage::app()->getStore()->isAdmin()) {
            $sessionMaxLifetime = Mage_Core_Model_Resource_Session::SEESION_MAX_COOKIE_LIFETIME;
            $adminSessionLifetime = (int)Mage::getStoreConfig('admin/security/session_cookie_lifetime');
            if ($adminSessionLifetime > $sessionMaxLifetime) {
                $adminSessionLifetime = $sessionMaxLifetime;
            }
            if ($adminSessionLifetime > 60) {
                $cookie->setLifetime($adminSessionLifetime);
            }
        }

        // session cookie params
        $cookieParams = array(
            'lifetime' => $cookie->getLifetime(),
            'path'     => $cookie->getPath(),
            'domain'   => $cookie->getConfigDomain(),
            'secure'   => $cookie->isSecure(),
            'httponly' => $cookie->getHttponly()
        );

        if (!$cookieParams['httponly']) {
            unset($cookieParams['httponly']);
            if (!$cookieParams['secure']) {
                unset($cookieParams['secure']);
                if (!$cookieParams['domain']) {
                    unset($cookieParams['domain']);
                }
            }
        }

        if (isset($cookieParams['domain'])) {
            $cookieParams['domain'] = $cookie->getDomain();
        }

        call_user_func_array('session_set_cookie_params', $cookieParams);

        if (!empty($sessionName)) {
            $this->setSessionName($sessionName);
        }

        // potential custom logic for session id (ex. switching between hosts)
        $this->setSessionId();

        Varien_Profiler::start(__METHOD__.'/start');
        $sessionCacheLimiter = Mage::getConfig()->getNode('global/session_cache_limiter');
        if ($sessionCacheLimiter) {
            session_cache_limiter((string)$sessionCacheLimiter);
        }

        session_start();

        if (Mage::app()->getFrontController()->getRequest()->isSecure() && empty($cookieParams['secure'])) {
            // secure cookie check to prevent MITM attack
            $secureCookieName = $sessionName . '_cid';
            if (isset($_SESSION[self::SECURE_COOKIE_CHECK_KEY])
                && $_SESSION[self::SECURE_COOKIE_CHECK_KEY] !== md5($cookie->get($secureCookieName))
            ) {
                session_regenerate_id(false);
                $sessionHosts = $this->getSessionHosts();
                $currentCookieDomain = $cookie->getDomain();
                foreach (array_keys($sessionHosts) as $host) {
                    // Delete cookies with the same name for parent domains
                    if (strpos($currentCookieDomain, $host) > 0) {
                        $cookie->delete($this->getSessionName(), null, $host);
                    }
                }
                $_SESSION = array();
            }
            if (!isset($_SESSION[self::SECURE_COOKIE_CHECK_KEY])) {
                $checkId = Mage::helper('core')->getRandomString(16);
                $cookie->set($secureCookieName, $checkId, null, null, null, true);
                $_SESSION[self::SECURE_COOKIE_CHECK_KEY] = md5($checkId);
            }
        }

        /**
        * Renew cookie expiration time if session id did not change
        */
        if ($cookie->get(session_name()) == $this->getSessionId()) {
            $cookie->renew(session_name());
        }
        Varien_Profiler::stop(__METHOD__.'/start');

        return $this;
    }

    /**
     * Get session hosts
     *
     * @return array
     */
    public function getSessionHosts()
    {
        return $this->_sessionHosts;
    }

    /**
     * Set session hosts
     *
     * @param array $hosts
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function setSessionHosts(array $hosts)
    {
        $this->_sessionHosts = $hosts;
        return $this;
    }

    /**
     * Retrieve cookie object
     *
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        return Mage::getSingleton('core/cookie');
    }

    /**
     * Revalidate cookie
     * @deprecated after 1.4 cookie renew moved to session start method
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function revalidateCookie()
    {
        return $this;
    }

    /**
     * Init session with namespace
     *
     * @param string $namespace
     * @param string $sessionName
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function init($namespace, $sessionName=null)
    {
        if (!isset($_SESSION)) {
            $this->start($sessionName);
        }
        if (!isset($_SESSION[$namespace])) {
            $_SESSION[$namespace] = array();
        }

        $this->_data = &$_SESSION[$namespace];

        $this->validate();
        $this->revalidateCookie();

        return $this;
    }

    /**
     * Additional get data with clear mode
     *
     * @param string $key
     * @param bool $clear
     * @return mixed
     */
    public function getData($key='', $clear = false)
    {
        $data = parent::getData($key);
        if ($clear && isset($this->_data[$key])) {
            unset($this->_data[$key]);
        }
        return $data;
    }

    /**
     * Retrieve session Id
     *
     * @return string
     */
    public function getSessionId()
    {
        return session_id();
    }

    /**
     * Set custom session id
     *
     * @param string $id
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function setSessionId($id=null)
    {
        if (!is_null($id) && preg_match('#^[0-9a-zA-Z,-]+$#', $id)) {
            session_id($id);
        }
        return $this;
    }

    /**
     * Retrieve session name
     *
     * @return string
     */
    public function getSessionName()
    {
        return session_name();
    }

    /**
     * Set session name
     *
     * @param string $name
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function setSessionName($name)
    {
        session_name($name);
        return $this;
    }

    /**
     * Unset all data
     *
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function unsetAll()
    {
        $this->unsetData();
        return $this;
    }

    /**
     * Alias for unsetAll
     *
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function clear()
    {
        return $this->unsetAll();
    }

    /**
     * Retrieve session save method
     * Default files
     *
     * @return string
     */
    public function getSessionSaveMethod()
    {
        return 'files';
    }

    /**
     * Get sesssion save path
     *
     * @return string
     */
    public function getSessionSavePath()
    {
        return Mage::getBaseDir('session');
    }

    /**
     * Use REMOTE_ADDR in validator key
     *
     * @return bool
     */
    public function useValidateRemoteAddr()
    {
        return true;
    }

    /**
     * Use HTTP_VIA in validator key
     *
     * @return bool
     */
    public function useValidateHttpVia()
    {
        return true;
    }

    /**
     * Use HTTP_X_FORWARDED_FOR in validator key
     *
     * @return bool
     */
    public function useValidateHttpXForwardedFor()
    {
        return true;
    }

    /**
     * Use HTTP_USER_AGENT in validator key
     *
     * @return bool
     */
    public function useValidateHttpUserAgent()
    {
        return true;
    }

    /**
     * Use session expire timestamp in validator key
     *
     * @return bool
     */
    public function useValidateSessionExpire()
    {
        return $this->getCookie()->getLifetime() > 0;
    }

    /**
     * Retrieve skip User Agent validation strings (Flash etc)
     *
     * @return array
     */
    public function getValidateHttpUserAgentSkip()
    {
        return array();
    }

    /**
     * Validate session
     *
     * @param string $namespace
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function validate()
    {
        if (!isset($this->_data[self::VALIDATOR_KEY])) {
            $this->_data[self::VALIDATOR_KEY] = $this->getValidatorData();
        }
        else {
            if (!$this->_validate()) {
                $this->getCookie()->delete(session_name());
                // throw core session exception
                throw new Mage_Core_Model_Session_Exception('');
            }
        }

        return $this;
    }

    /**
     * Validate data
     *
     * @return bool
     */
    protected function _validate()
    {
        $sessionData = $this->_data[self::VALIDATOR_KEY];
        $validatorData = $this->getValidatorData();

        if ($this->useValidateRemoteAddr()
                && $sessionData[self::VALIDATOR_REMOTE_ADDR_KEY] != $validatorData[self::VALIDATOR_REMOTE_ADDR_KEY]) {
            return false;
        }
        if ($this->useValidateHttpVia()
                && $sessionData[self::VALIDATOR_HTTP_VIA_KEY] != $validatorData[self::VALIDATOR_HTTP_VIA_KEY]) {
            return false;
        }

        $sessionValidateHttpXForwardedForKey = $sessionData[self::VALIDATOR_HTTP_X_FORVARDED_FOR_KEY];
        $validatorValidateHttpXForwardedForKey = $validatorData[self::VALIDATOR_HTTP_X_FORVARDED_FOR_KEY];
        if ($this->useValidateHttpXForwardedFor()
            && $sessionValidateHttpXForwardedForKey != $validatorValidateHttpXForwardedForKey ) {
            return false;
        }
        if ($this->useValidateHttpUserAgent()
            && $sessionData[self::VALIDATOR_HTTP_USER_AGENT_KEY] != $validatorData[self::VALIDATOR_HTTP_USER_AGENT_KEY]
        ) {
            $userAgentValidated = $this->getValidateHttpUserAgentSkip();
            foreach ($userAgentValidated as $agent) {
                if (preg_match('/' . $agent . '/iu', $validatorData[self::VALIDATOR_HTTP_USER_AGENT_KEY])) {
                    return true;
                }
            }
            return false;
        }

        if ($this->useValidateSessionExpire()
            && isset($sessionData[self::VALIDATOR_SESSION_EXPIRE_TIMESTAMP])
            && $sessionData[self::VALIDATOR_SESSION_EXPIRE_TIMESTAMP] < time() ) {
            return false;
        } else {
            $this->_data[self::VALIDATOR_KEY][self::VALIDATOR_SESSION_EXPIRE_TIMESTAMP]
                = $validatorData[self::VALIDATOR_SESSION_EXPIRE_TIMESTAMP];
        }

        return true;
    }

    /**
     * Retrieve unique user data for validator
     *
     * @return array
     */
    public function getValidatorData()
    {
        $parts = array(
            self::VALIDATOR_REMOTE_ADDR_KEY             => '',
            self::VALIDATOR_HTTP_VIA_KEY                => '',
            self::VALIDATOR_HTTP_X_FORVARDED_FOR_KEY    => '',
            self::VALIDATOR_HTTP_USER_AGENT_KEY         => ''
        );

        // collect ip data
        if (Mage::helper('core/http')->getRemoteAddr()) {
            $parts[self::VALIDATOR_REMOTE_ADDR_KEY] = Mage::helper('core/http')->getRemoteAddr();
        }
        if (isset($_ENV['HTTP_VIA'])) {
            $parts[self::VALIDATOR_HTTP_VIA_KEY] = (string)$_ENV['HTTP_VIA'];
        }
        if (isset($_ENV['HTTP_X_FORWARDED_FOR'])) {
            $parts[self::VALIDATOR_HTTP_X_FORVARDED_FOR_KEY] = (string)$_ENV['HTTP_X_FORWARDED_FOR'];
        }

        // collect user agent data
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $parts[self::VALIDATOR_HTTP_USER_AGENT_KEY] = (string)$_SERVER['HTTP_USER_AGENT'];
        }

        $parts[self::VALIDATOR_SESSION_EXPIRE_TIMESTAMP] = time() + $this->getCookie()->getLifetime();

        return $parts;
    }

    /**
     * Regenerate session Id
     *
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function regenerateSessionId()
    {
        session_regenerate_id(true);
        return $this;
    }
}
