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
 * Session abstaract class
 *
 * @category   Mage
 * @package    Mage_Core
 *
 * @method string getCookieDomain()
 * @method string getCookiePath()
 * @method string getCookieLifetime()
 */
abstract class Mage_Core_Model_Session_Abstract_Zend extends Varien_Object
{
    /**
     * Session namespace object
     *
     * @var Zend_Session_Namespace
     */
    protected $_namespace;

    /**
     * @return Zend_Session_Namespace
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * @return $this
     */
    public function start()
    {
        Varien_Profiler::start(__METHOD__ . '/setOptions');
        $options = [
            'save_path' => Mage::getBaseDir('session'),
            'use_only_cookies' => 'off',
            'throw_startup_exceptions' => E_ALL ^ E_NOTICE,
        ];
        if ($this->getCookieDomain()) {
            $options['cookie_domain'] = $this->getCookieDomain();
        }
        if ($this->getCookiePath()) {
            $options['cookie_path'] = $this->getCookiePath();
        }
        if ($this->getCookieLifetime()) {
            $options['cookie_lifetime'] = $this->getCookieLifetime();
        }
        Zend_Session::setOptions($options);
        Varien_Profiler::stop(__METHOD__ . '/setOptions');
        /*
                Varien_Profiler::start(__METHOD__.'/setHandler');
                $sessionResource = Mage::getResourceSingleton('core/session');
                if ($sessionResource->hasConnection()) {
                    Zend_Session::setSaveHandler($sessionResource);
                }
                Varien_Profiler::stop(__METHOD__.'/setHandler');
        */
        Varien_Profiler::start(__METHOD__ . '/start');
        Zend_Session::start();
        Varien_Profiler::stop(__METHOD__ . '/start');

        return $this;
    }

    /**
     * Initialization session namespace
     *
     * @param string $namespace
     * @return $this
     */
    public function init($namespace)
    {
        if (!Zend_Session::sessionExists()) {
            $this->start();
        }

        Varien_Profiler::start(__METHOD__ . '/init');
        $this->_namespace = new Zend_Session_Namespace($namespace, Zend_Session_Namespace::SINGLE_INSTANCE);
        Varien_Profiler::stop(__METHOD__ . '/init');
        return $this;
    }

    /**
     * Redeclaration object setter
     *
     * @param string $key
     * @param mixed $value
     * @param bool $isChanged
     * @return $this
     */
    public function setData($key, $value = '', $isChanged = false)
    {
        if (!$this->_namespace->data) {
            $this->_namespace->data = new Varien_Object();
        }
        $this->_namespace->data->setData($key, $value, $isChanged);
        return $this;
    }

    /**
     * Redeclaration object getter
     *
     * @param   string $var
     * @param   bool $clear
     * @return  mixed
     */
    public function getData($var = null, $clear = false)
    {
        if (!$this->_namespace->data) {
            $this->_namespace->data = new Varien_Object();
        }

        $data = $this->_namespace->data->getData($var);

        if ($clear) {
            $this->_namespace->data->unsetData($var);
        }

        return $data;
    }

    /**
     * Clear session data
     *
     * @return $this
     */
    public function unsetAll()
    {
        $this->_namespace->unsetAll();
        return $this;
    }

    /**
     * Retrieve current session identifier
     *
     * @return string
     */
    public function getSessionId()
    {
        return Zend_Session::getId();
    }

    /**
     * @param string|null $id
     * @return $this
     */
    public function setSessionId($id = null)
    {
        if (!is_null($id)) {
            Zend_Session::setId($id);
        }
        return $this;
    }

    /**
     * Regenerate session Id
     *
     * @return $this
     */
    public function regenerateSessionId()
    {
        Zend_Session::regenerateId();
        return $this;
    }
}
