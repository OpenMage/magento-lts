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
 * @category   Varien
 * @package    Varien_Object
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Maged_Model_Session extends Maged_Model
{
    protected $_session;

    public function start()
    {
        if (class_exists('Mage') && Mage::isInstalled()) {
            // initialize Magento Config
            Mage::app();
            $this->_session = Mage::getSingleton('admin/session');
        } else {
            session_start();
        }
        return $this;
    }

    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function authenticate()
    {
        if (!$this->_session) {
            return $this;
        }

        if (!empty($_GET['return'])) {
            $this->set('return_url', $_GET['return']);
        }

        if ($this->getUserId()) {
            return $this;
        }

        if (!$this->controller()->isInstalled()) {
            return $this;
        }

        try {
            if (empty($_POST['username']) || empty($_POST['password'])) {
                $this->controller()->setAction('login');
                return $this;
            }

            $user = $this->_session->login($_POST['username'], $_POST['password']);
            $this->_session->refreshAcl();

            if (!$user->getId() || !$this->_session->isAllowed('all')) {
                $this->addMessage('error', 'Invalid user name or password');
                $this->controller()->setAction('login');
                return $this;
            }

        } catch (Exception $e) {

            $this->addMessage('error', $e->getMessage());

        }

        $this->controller()
            ->redirect($this->controller()->url($this->controller()->getAction()).'&loggedin', true);
    }

    public function logout()
    {
        if (!$this->_session) {
            return $this;
        }
        $this->_session->unsUser();
        return $this;
    }

    public function getUserId()
    {
        return ($session = $this->_session) && ($user = $session->getUser()) ? $user->getId() : false;
    }

    public function addMessage($type, $msg)
    {
        $msgs = $this->getMessages(false);
        $msgs[$type][] = $msg;
        $this->set('messages', $msgs);
        return $this;
    }

    public function getMessages($clear = true)
    {
        $msgs = $this->get('messages');
        $msgs = $msgs ? $msgs : array();
        if ($clear) {
            unset($_SESSION['messages']);
        }
        return $msgs;
    }

    public function getReturnUrl()
    {
        if (!$this->_session) {
            return '';
        }
        return Mage::getSingleton('adminhtml/url')->getUrl('adminhtml');
    }
}
