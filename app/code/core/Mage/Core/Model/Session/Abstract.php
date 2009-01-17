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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Session_Abstract extends Mage_Core_Model_Session_Abstract_Varien
{
    const XML_PATH_COOKIE_DOMAIN    = 'web/cookie/cookie_domain';
    const XML_PATH_COOKIE_PATH      = 'web/cookie/cookie_path';
    const XML_PATH_COOKIE_LIFETIME  = 'web/cookie/cookie_lifetime';
    const XML_NODE_SESSION_SAVE     = 'global/session_save';

    const SESSION_ID_QUERY_PARAM = 'SID';

    protected static $_urlHostCache = array();

    protected static $_encryptedSessionId;

    public function init($namespace, $sessionName=null)
    {
        parent::init($namespace, $sessionName);
        if (isset($_SERVER['HTTP_HOST'])) {
            $hostArr = explode(':', $_SERVER['HTTP_HOST']);
            $this->addHost($hostArr[0]);
        }
        return $this;
    }

    public function getCookieDomain()
    {
        return Mage::getSingleton('core/cookie')->getCookieDomain();
        $domain = Mage::getStoreConfig(self::XML_PATH_COOKIE_DOMAIN);
        if (empty($domain) && isset($_SERVER['HTTP_HOST'])) {
            $domainArr = explode(':', $_SERVER['HTTP_HOST']);
            $domain = $domainArr[0];
        }
        return $domain;
    }

    public function getCookiePath()
    {
        return Mage::getSingleton('core/cookie')->getCookiePath();
        $path = Mage::getStoreConfig(self::XML_PATH_COOKIE_PATH);
        if (empty($path)) {
            $path = '/';
        }
        return $path;
    }

    public function getCookieLifetime()
    {
        $lifetime = Mage::getStoreConfig(self::XML_PATH_COOKIE_LIFETIME);
        return $lifetime;
    }


    /**
     * Retrieve messages from session
     *
     * @param   bool $clear
     * @return  Mage_Core_Model_Message_Collection
     */
    public function getMessages($clear=false)
    {
        if (!$this->getData('messages')) {
            $this->setMessages(Mage::getModel('core/message_collection'));
        }

        if ($clear) {
            $messages = clone $this->getData('messages');
            $this->getData('messages')->clear();
            return $messages;
        }
        return $this->getData('messages');
    }

    /**
     * Not Mage exeption handling
     *
     * @param   Exception $exception
     * @param   string $alternativeText
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addException(Exception $exception, $alternativeText)
    {
        $this->addMessage(Mage::getSingleton('core/message')->error($alternativeText));
        return $this;
    }

    /**
     * Adding new message to message collection
     *
     * @param   Mage_Core_Model_Message_Abstract $message
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        $this->getMessages()->add($message);
        return $this;
    }

    /**
     * Adding new error message
     *
     * @param   string $message
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addError($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->error($message));
        return $this;
    }

    /**
     * Adding new warning message
     *
     * @param   string $message
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addWarning($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->warning($message));
        return $this;
    }

    /**
     * Adding new nitice message
     *
     * @param   string $message
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addNotice($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->notice($message));
        return $this;
    }

    /**
     * Adding new success message
     *
     * @param   string $message
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addSuccess($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->success($message));
        return $this;
    }

    /**
     * Adding messages array to message collection
     *
     * @param   array $messages
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addMessages($messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                $this->addMessage($message);
            }
        }
        return $this;
    }

    public function setSessionId($id=null)
    {
        if (is_null($id)) {
            if (isset($_GET[self::SESSION_ID_QUERY_PARAM])) {
                if ($tryId = Mage::helper('core')->decrypt($_GET[self::SESSION_ID_QUERY_PARAM])) {
                    $id = $tryId;
                }
            }
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->addHost($_SERVER['HTTP_HOST']);
        }
        parent::setSessionId($id);
    }

    public function getEncryptedSessionId()
    {
        if (!self::$_encryptedSessionId) {
            $helper = Mage::helper('core');
            if (!$helper) {
                return $this;
            }
            self::$_encryptedSessionId = $helper->encrypt($this->getSessionId());
        }
        return self::$_encryptedSessionId;
    }

    public function getSessionIdQueryParam()
    {
        return self::SESSION_ID_QUERY_PARAM;
    }

    /**
     * If the host was switched but session cookie won't recognize it - add session id to query
     *
     * @param string $urlHost can be host or url
     * @return string {session_id_key}={session_id_encrypted}
     */
    public function getSessionIdForHost($urlHost)
    {
        if (empty($_SERVER['HTTP_HOST'])) {
            return '';
        }

        $urlHostArr = explode('/', $urlHost, 4);
        if (!empty($urlHostArr[2])) {
            $urlHost = $urlHostArr[2];
        }

        if (!isset(self::$_urlHostCache[$urlHost])) {
            $urlHostArr = explode(':', $urlHost);
            $urlHost = $urlHostArr[0];

            $curHostArr = explode(':', $_SERVER['HTTP_HOST']);
            if ($curHostArr[0]!==$urlHost && !$this->isValidForHost($urlHost)) {
                $sessionId = $this->getEncryptedSessionId();
            } else {
                $sessionId = '';
            }
            self::$_urlHostCache[$urlHost] = $sessionId;
        }
        return self::$_urlHostCache[$urlHost];
    }

    public function isValidForHost($host)
    {
        $hostArr = explode(':', $host);
        $hosts = $this->getSessionHosts();
        return (!empty($hosts[$hostArr[0]]));
    }

    public function addHost($host)
    {
        $hostArr = explode(':', $host);
        $hosts = $this->getSessionHosts();
        $hosts[$hostArr[0]] = true;
        $this->setSessionHosts($hosts);
        return $this;
    }

    public function getSessionHosts()
    {
        return $this->getData('session_hosts');
    }

    /**
     * Retrieve session save method
     *
     * @return string
     */
    public function getSessionSaveMethod()
    {
        if (Mage::app()->isInstalled() && $sessionSave = Mage::getConfig()->getNode(self::XML_NODE_SESSION_SAVE)) {
            return $sessionSave;
        }
        return parent::getSessionSaveMethod();
    }
}