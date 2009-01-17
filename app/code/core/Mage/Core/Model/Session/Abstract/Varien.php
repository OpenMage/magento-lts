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

        session_start();

        Varien_Profiler::stop(__METHOD__.'/start');

        return $this;
    }

    public function revalidateCookie()
    {
        if (empty($_SESSION['_cookie_revalidate'])) {
            $time = time() + round(ini_get('session.gc_maxlifetime') / 4);
            $_SESSION['_cookie_revalidate'] = $time;
        }
        else {
            if ($_SESSION['_cookie_revalidate'] < time()) {
                setcookie(
                    session_name(),
                    session_id(),
                    time() + ini_get('session.gc_maxlifetime'),
                    ini_get('session.cookie_path'),
                    ini_get('session.cookie_domain')
                );

                $time = time() + round(ini_get('session.gc_maxlifetime') / 4);
                $_SESSION['_cookie_revalidate'] = $time;
            }
        }
    }

    public function init($namespace, $sessionName=null)
    {
        if (!isset($_SESSION)) {
            $this->start($sessionName);
            $this->revalidateCookie($namespace);
        }
        if (!isset($_SESSION[$namespace])) {
            $_SESSION[$namespace] = array();
        }
        $this->_data = &$_SESSION[$namespace];

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
}