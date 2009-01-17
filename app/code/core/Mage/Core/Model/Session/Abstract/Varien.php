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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Session_Abstract_Varien extends Varien_Object
{
    const VALIDATOR_KEY                         = '_session_validator_data';
    const VALIDATOR_HTTP_USER_AGENT_KEY         = 'http_user_agent';
    const VALIDATOR_HTTP_X_FORVARDED_FOR_KEY    = 'http_x_forwarded_for';
    const VALIDATOR_HTTP_VIA_KEY                = 'http_via';
    const VALIDATOR_REMOTE_ADDR_KEY             = 'remote_addr';

    public function start($sessionName=null)
    {
        if (isset($_SESSION)) {
            return $this;
        }

        Varien_Profiler::start(__METHOD__.'/setOptions');
        if (is_writable(Mage::getBaseDir('session'))) {
            session_save_path(Mage::getBaseDir('session'));
        }
        Varien_Profiler::stop(__METHOD__.'/setOptions');

        if ($this->getSessionSaveMethod() == 'files') {
            session_module_name('files');
        }
        else {
            ini_set('session.save_handler', 'user');
            $sessionResource = Mage::getResourceSingleton('core/session');
            /* @var $sessionResource Mage_Core_Model_Mysql4_Session */
            $sessionResource->setSaveHandler();
        }

        if (intval($this->getCookieLifetime()) > 0) {
            ini_set('session.gc_maxlifetime', $this->getCookieLifetime());
            ini_set('session.cookie_lifetime', $this->getCookieLifetime());
        } else {
            /*
            * if cookie life time is empty or 0, we put 0
            * session will be time out when we close the browser
            */
            ini_set('session.gc_maxlifetime', 3600);
            ini_set('session.cookie_lifetime', 3600);
        }
        if (!is_null($this->getCookiePath())) {
            ini_set('session.cookie_path', $this->getCookiePath());
        }
        if (!is_null($this->getCookieDomain()) && strpos($this->getCookieDomain(), '.')!==false) {
            ini_set('session.cookie_domain', $this->getCookieDomain());
        }

        if (!empty($sessionName)) {
            session_name($sessionName);
        }

        // potential custom logic for session id (ex. switching between hosts)
        $this->setSessionId();

        Varien_Profiler::start(__METHOD__.'/start');

        if ($sessionCacheLimiter = Mage::getConfig()->getNode('global/session_cache_limiter')) {
            session_cache_limiter((string)$sessionCacheLimiter);
        }

        session_start();

        Varien_Profiler::stop(__METHOD__.'/start');

        return $this;
    }

    public function revalidateCookie()
    {
        if (empty($this->_data['_cookie_revalidate'])) {
            $time = time() + round(ini_get('session.gc_maxlifetime') / 4);
            $this->_data['_cookie_revalidate'] = $time;
        }
        else {
            if ($this->_data['_cookie_revalidate'] < time()) {
                setcookie(
                    session_name(),
                    session_id(),
                    time() + ini_get('session.gc_maxlifetime'),
                    ini_get('session.cookie_path'),
                    ini_get('session.cookie_domain')
                );

                $time = time() + round(ini_get('session.gc_maxlifetime') / 4);
                $this->_data['_cookie_revalidate'] = $time;
            }
        }
    }

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

    public function getData($key='', $clear=false)
    {
        $data = parent::getData($key);
        if ($clear && isset($this->_data[$key])) {
            unset($this->_data[$key]);
        }
        return $data;
    }

    public function getSessionId()
    {
        return session_id();
    }

    public function setSessionId($id=null)
    {
        if (!is_null($id) && preg_match('#^[0-9a-zA-Z,-]+$#', $id)) {
            session_id($id);
        }
        return $this;
    }

    public function unsetAll()
    {
        $this->unsetData();
        return $this;
    }

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
                // remove session cookie
                setcookie(
                    session_name(),
                    null,
                    null,
                    ini_get('session.cookie_path'),
                    ini_get('session.cookie_domain')
                );
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

        if ($this->useValidateRemoteAddr() && $sessionData[self::VALIDATOR_REMOTE_ADDR_KEY] != $validatorData[self::VALIDATOR_REMOTE_ADDR_KEY]) {
            return false;
        }
        if ($this->useValidateHttpVia() && $sessionData[self::VALIDATOR_HTTP_VIA_KEY] != $validatorData[self::VALIDATOR_HTTP_VIA_KEY]) {
            return false;
        }
        if ($this->useValidateHttpXForwardedFor() && $sessionData[self::VALIDATOR_HTTP_X_FORVARDED_FOR_KEY] != $validatorData[self::VALIDATOR_HTTP_X_FORVARDED_FOR_KEY]) {
            return false;
        }
        if ($this->useValidateHttpUserAgent()
            && $sessionData[self::VALIDATOR_HTTP_USER_AGENT_KEY] != $validatorData[self::VALIDATOR_HTTP_USER_AGENT_KEY]
            && !in_array($validatorData[self::VALIDATOR_HTTP_USER_AGENT_KEY], $this->getValidateHttpUserAgentSkip())) {
            return false;
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
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $parts[self::VALIDATOR_REMOTE_ADDR_KEY] = (string)$_SERVER['REMOTE_ADDR'];
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

        return $parts;
    }
}