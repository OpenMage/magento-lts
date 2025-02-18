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
 * @copyright  Copyright (c) 2018-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
 *
 * @method bool|null getSkipEmptySessionCheck()
 * @method $this setSkipEmptySessionCheck(bool $flag)
 */
class Mage_Core_Model_Session_Abstract_Varien extends Varien_Object
{
    public const VALIDATOR_KEY                         = '_session_validator_data';
    public const VALIDATOR_HTTP_USER_AGENT_KEY         = 'http_user_agent';
    public const VALIDATOR_HTTP_X_FORVARDED_FOR_KEY    = 'http_x_forwarded_for';
    public const VALIDATOR_HTTP_VIA_KEY                = 'http_via';
    public const VALIDATOR_REMOTE_ADDR_KEY             = 'remote_addr';
    public const VALIDATOR_SESSION_EXPIRE_TIMESTAMP    = 'session_expire_timestamp';
    public const VALIDATOR_SESSION_RENEW_TIMESTAMP     = 'session_renew_timestamp';
    public const VALIDATOR_SESSION_LIFETIME            = 'session_lifetime';
    public const VALIDATOR_PASSWORD_CREATE_TIMESTAMP   = 'password_create_timestamp';
    public const SECURE_COOKIE_CHECK_KEY               = '_secure_cookie_check';
    public const REGISTRY_CONCURRENCY_ERROR            = 'concurrent_connections_exceeded';

    /** @var bool Flag true if session validator data has already been evaluated */
    protected static $isValidated = false;

    /**
     * Map of session enabled hosts
     * @example array('host.name' => true)
     * @var array
     */
    protected $_sessionHosts = [];

    /**
     * Configure and start session
     *
     * @param string $sessionName
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     * @SuppressWarnings("PHPMD.Superglobals")
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    public function start($sessionName = null)
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
                /** @var Mage_Core_Model_Resource_Session $sessionResource */
                $sessionResource = Mage::getResourceSingleton('core/session');
                $sessionResource->setSaveHandler();
                break;
            case 'redis':
                /** @var Cm_RedisSession_Model_Session $sessionResource */
                $sessionResource = Mage::getSingleton('cm_redissession/session');
                $sessionResource->setSaveHandler();
                if (method_exists($sessionResource, 'setDieOnError')) {
                    $sessionResource->setDieOnError(false);
                }
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
                // no break
            default:
                session_save_path($this->getSessionSavePath());
                session_module_name($moduleName);
                break;
        }

        $cookie = $this->getCookie();
        if (Mage::app()->getStore()->isAdmin()) {
            $sessionMaxLifetime = Mage_Core_Model_Resource_Session::SEESION_MAX_COOKIE_LIFETIME;
            $adminSessionLifetime = Mage::getStoreConfigAsInt('admin/security/session_cookie_lifetime');
            if ($adminSessionLifetime > $sessionMaxLifetime) {
                $adminSessionLifetime = $sessionMaxLifetime;
            }
            if ($adminSessionLifetime > 60) {
                $cookie->setLifetime($adminSessionLifetime);
            }
        }

        // session cookie params
        $cookieParams = [
            'lifetime' => (int) $cookie->getLifetime(),
            'path'     => $cookie->getPath(),
            'domain'   => $cookie->getConfigDomain(),
            'secure'   => $cookie->isSecure(),
            'httponly' => $cookie->getHttponly(),
        ];

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

        call_user_func_array('session_set_cookie_params', array_values($cookieParams));

        if (!empty($sessionName)) {
            $this->setSessionName($sessionName);

            // Migrate old cookie from 'frontend'
            if ($sessionName === \Mage_Core_Controller_Front_Action::SESSION_NAMESPACE
                && $cookie->get('frontend')
                && ! $cookie->get(\Mage_Core_Controller_Front_Action::SESSION_NAMESPACE)
            ) {
                $frontendValue = $cookie->get('frontend');
                $_COOKIE[\Mage_Core_Controller_Front_Action::SESSION_NAMESPACE] = $frontendValue;
                $cookie->set(Mage_Core_Controller_Front_Action::SESSION_NAMESPACE, $frontendValue);
                $cookie->delete('frontend');
            }
        }
        // potential custom logic for session id (ex. switching between hosts)
        $this->setSessionId();

        Varien_Profiler::start(__METHOD__ . '/start');
        $sessionCacheLimiter = Mage::getConfig()->getNode('global/session_cache_limiter');
        if ($sessionCacheLimiter) {
            session_cache_limiter((string) $sessionCacheLimiter);
        }

        // Start session, abort and render error page if it fails
        try {
            if (session_start() === false) {
                throw new Exception('Unable to start session.');
            }
        } catch (Throwable $e) {
            session_abort();
            if (Mage::registry(self::REGISTRY_CONCURRENCY_ERROR)) {
                require_once Mage::getBaseDir() . DS . 'errors' . DS . '503.php';
                die();
            } else {
                Mage::printException($e);
            }
        }

        Mage::dispatchEvent('session_before_renew_cookie', ['cookie' => $cookie]);

        // Secure cookie check to prevent MITM attack
        if (Mage::app()->getFrontController()->getRequest()->isSecure() && empty($cookieParams['secure'])) {
            $secureCookieName = $this->getSessionName() . '_cid';
            $cookieValue = $cookie->get($secureCookieName);

            // Migrate old cookie from 'frontend'
            if (!$cookieValue
                && $sessionName === \Mage_Core_Controller_Front_Action::SESSION_NAMESPACE
                && $cookie->get('frontend_cid')
            ) {
                $cookieValue = $cookie->get('frontend_cid');
                $_COOKIE[$secureCookieName] = $cookieValue;
                $cookie->set($secureCookieName, $cookieValue);
                $cookie->delete('frontend_cid');
            }

            // Set secure cookie check value in session if not yet set
            if (!isset($_SESSION[self::SECURE_COOKIE_CHECK_KEY])) {
                $cookieValue = Mage::helper('core')->getRandomString(16);
                $cookie->set($secureCookieName, $cookieValue, null, null, null, true, true);
                $_SESSION[self::SECURE_COOKIE_CHECK_KEY] = md5($cookieValue);
            } elseif (is_string($cookieValue) && $_SESSION[self::SECURE_COOKIE_CHECK_KEY] === md5($cookieValue)) {
                // Renew secret check value cookie if it is valid
                $cookie->renew($secureCookieName, null, null, null, true, true);
            } else {
                // Secure cookie check value is invalid, regenerate session
                session_regenerate_id(false);
                $sessionHosts = $this->getSessionHosts();
                $currentCookieDomain = $cookie->getDomain();
                foreach (array_keys($sessionHosts) as $host) {
                    // Delete cookies with the same name for parent domains
                    if (strpos($currentCookieDomain, $host) > 0) {
                        // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                        $cookie->delete($this->getSessionName(), null, $host);
                    }
                }
                $_SESSION = [];
            }
        }

        /**
         * Renew cookie expiration time if session id did not change
         */
        if ($cookie->get(session_name()) == $this->getSessionId()) {
            $cookie->renew(session_name());
        }
        Varien_Profiler::stop(__METHOD__ . '/start');

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
     * @return $this
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
     * @return $this
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
     * @return $this
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function init($namespace, $sessionName = null)
    {
        if (!isset($_SESSION)) {
            $this->start($sessionName);
        }
        if (!isset($_SESSION[$namespace])) {
            $_SESSION[$namespace] = [];
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
    public function getData($key = '', $clear = false)
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
     * @return $this
     */
    public function setSessionId($id = null)
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
     * @return $this
     */
    public function setSessionName($name)
    {
        session_name($name);
        return $this;
    }

    /**
     * Unset all data
     *
     * @return $this
     */
    public function unsetAll()
    {
        $this->unsetData();
        return $this;
    }

    /**
     * Alias for unsetAll
     *
     * @return $this
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
     * Get session save path
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
     * Password creation timestamp must not be newer than last session renewal
     *
     * @return bool
     */
    public function useValidateSessionPasswordTimestamp()
    {
        return true;
    }

    /**
     * Retrieve skip User Agent validation strings (Flash etc)
     *
     * @return array
     */
    public function getValidateHttpUserAgentSkip()
    {
        return [];
    }

    /**
     * Validate session
     *
     * @throws Mage_Core_Model_Session_Exception
     * @return $this
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function validate()
    {
        // Backwards compatibility with legacy sessions (validator data stored per-namespace)
        if (isset($this->_data[self::VALIDATOR_KEY])) {
            $_SESSION[self::VALIDATOR_KEY] = $this->_data[self::VALIDATOR_KEY];
            unset($this->_data[self::VALIDATOR_KEY]);
        }
        if (!isset($_SESSION[self::VALIDATOR_KEY])) {
            $_SESSION[self::VALIDATOR_KEY] = $this->getValidatorData();
        } else {
            if (!self::$isValidated && ! $this->_validate()) {
                $this->getCookie()->delete(session_name());
                // throw core session exception
                throw new Mage_Core_Model_Session_Exception('');
            }

            // Refresh expire timestamp
            if ($this->useValidateSessionExpire() || $this->useValidateSessionPasswordTimestamp()) {
                $this->setValidatorSessionRenewTimestamp(time());
                $_SESSION[self::VALIDATOR_KEY][self::VALIDATOR_SESSION_LIFETIME] = $this->getCookie()->getLifetime();
            }
        }

        return $this;
    }

    /**
     * Update the session's last legitimate renewal time (call when customer password is updated to avoid
     * being logged out)
     *
     * @param int $timestamp
     * @return void
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function setValidatorSessionRenewTimestamp($timestamp)
    {
        $_SESSION[self::VALIDATOR_KEY][self::VALIDATOR_SESSION_RENEW_TIMESTAMP] = $timestamp;
    }

    /**
     * Validate data
     *
     * @return bool
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    protected function _validate()
    {
        $sessionData = $_SESSION[self::VALIDATOR_KEY];
        $validatorData = $this->getValidatorData();
        self::$isValidated = true; // Only validate once since the validator data is the same for every namespace

        if ($this->useValidateRemoteAddr()
                && $sessionData[self::VALIDATOR_REMOTE_ADDR_KEY] != $validatorData[self::VALIDATOR_REMOTE_ADDR_KEY]
        ) {
            return false;
        }
        if ($this->useValidateHttpVia()
                && $sessionData[self::VALIDATOR_HTTP_VIA_KEY] != $validatorData[self::VALIDATOR_HTTP_VIA_KEY]
        ) {
            return false;
        }

        if ($this->useValidateHttpXForwardedFor()
                && $sessionData[self::VALIDATOR_HTTP_X_FORVARDED_FOR_KEY] != $validatorData[self::VALIDATOR_HTTP_X_FORVARDED_FOR_KEY]
        ) {
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
            && isset($sessionData[self::VALIDATOR_SESSION_RENEW_TIMESTAMP])
            && isset($sessionData[self::VALIDATOR_SESSION_LIFETIME])
            && ((int) $sessionData[self::VALIDATOR_SESSION_RENEW_TIMESTAMP] + (int) $sessionData[self::VALIDATOR_SESSION_LIFETIME])
            < time()
        ) {
            return false;
        }
        if ($this->useValidateSessionPasswordTimestamp()
            && isset($validatorData[self::VALIDATOR_PASSWORD_CREATE_TIMESTAMP])
            && isset($sessionData[self::VALIDATOR_SESSION_RENEW_TIMESTAMP])
            && $validatorData[self::VALIDATOR_PASSWORD_CREATE_TIMESTAMP]
            > $sessionData[self::VALIDATOR_SESSION_RENEW_TIMESTAMP]
        ) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve unique user data for validator
     *
     * @return array
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function getValidatorData()
    {
        $parts = [
            self::VALIDATOR_REMOTE_ADDR_KEY             => '',
            self::VALIDATOR_HTTP_VIA_KEY                => '',
            self::VALIDATOR_HTTP_X_FORVARDED_FOR_KEY    => '',
            self::VALIDATOR_HTTP_USER_AGENT_KEY         => '',
        ];

        // collect ip data
        if (Mage::helper('core/http')->getRemoteAddr()) {
            $parts[self::VALIDATOR_REMOTE_ADDR_KEY] = Mage::helper('core/http')->getRemoteAddr();
        }
        if (isset($_ENV['HTTP_VIA'])) {
            $parts[self::VALIDATOR_HTTP_VIA_KEY] = (string) $_ENV['HTTP_VIA'];
        }
        if (isset($_ENV['HTTP_X_FORWARDED_FOR'])) {
            $parts[self::VALIDATOR_HTTP_X_FORVARDED_FOR_KEY] = (string) $_ENV['HTTP_X_FORWARDED_FOR'];
        }

        // collect user agent data
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $parts[self::VALIDATOR_HTTP_USER_AGENT_KEY] = (string) $_SERVER['HTTP_USER_AGENT'];
        }

        // get time when password was last changed
        if (isset($this->_data['visitor_data']['customer_id'])) {
            $parts[self::VALIDATOR_PASSWORD_CREATE_TIMESTAMP] =
                Mage::helper('customer')->getPasswordTimestamp($this->_data['visitor_data']['customer_id']);
        }

        return $parts;
    }

    /**
     * @return array
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function getSessionValidatorData()
    {
        return $_SESSION[self::VALIDATOR_KEY];
    }

    /**
     * Regenerate session Id
     *
     * @return $this
     */
    public function regenerateSessionId()
    {
        session_regenerate_id(true);
        return $this;
    }
}
