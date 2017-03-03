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
 * @package     Mage_Connect
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
* Class session
*
* @category   Mage
* @package    Mage_Connect
* @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Maged_Model_Session extends Maged_Model
{
    /**
     * Session
     *
     * @var Mage_Admin_Model_Session
     */
    protected $_session;

    /**
     * Init session
     *
     * @return Maged_Model_Session
     */
    public function start()
    {
        if ($this->controller()->isInstalled()) {
            // initialize Magento Config
            Mage::app();
            $this->_session = Mage::getSingleton('admin/session');
        } else {
            session_start();
        }
        return $this;
    }

    /**
     * Get value by key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Set value for key
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * Authentication to downloader
     * @param Maged_BruteForce_Validator $bruteForceValidator
     * @return $this
     */
    public function authenticate(Maged_BruteForce_Validator $bruteForceValidator )
    {
        if (!$this->_session) {
            return $this;
        }

        if (!empty($_GET['return'])) {
            $this->set('return_url', $_GET['return']);
        }

        if ($this->_checkUserAccess()) {
            return $this;
        }

        if (!$this->controller()->isInstalled()) {
            return $this;
        }

        try {
            if (isset($_POST['username']) && !$this->validateFormKey()) {
                $this->controller()
                    ->redirect(
                        $this->controller()->url(),
                        true
                    );
            }
            if ( (isset($_POST['username']) && empty($_POST['username']))
                || (isset($_POST['password']) && empty($_POST['password']))) {
                $this->addMessage('error', 'Invalid user name or password');
            }
            if (empty($_POST['username']) || empty($_POST['password'])) {
                $this->controller()->setAction('login');
                return $this;
            }
            $user = $this->_session->login($_POST['username'], $_POST['password']);
            $this->_session->refreshAcl();
            if ($this->_checkUserAccess($user)) {
                $bruteForceValidator->doGoodLogin();
                return $this;
            } else {
                $bruteForceValidator->doBadLogin();
            }
        } catch (Exception $e) {
            $this->addMessage('error', $e->getMessage());
        }

        $this->controller()
            ->redirect(
                $this->controller()->url('loggedin'),
                true
        );
    }

    /**
     * Check is user logged in and permissions
     *
     * @param Mage_Admin_Model_User|null $user
     * @return bool
     */
    protected function _checkUserAccess($user = null)
    {
        if ($user && !$user->getId()) {
            $this->addMessage('error', 'Invalid user name or password');
            $this->controller()->setAction('login');
        } elseif ($this->getUserId() || ($user && $user->getId())) {
            if ($this->_session->isAllowed('all')) {
                return true;
            } else {
                $this->logout();
                $this->addMessage('error', 'Access Denied', true);
                $this->controller()->setAction('login');
            }
        }
        return false;
    }

    /**
     * Log Out
     *
     * @return Maged_Model_Session
     */
    public function logout()
    {
        if (!$this->_session) {
            return $this;
        }
        $this->_session->unsUser();
        return $this;
    }

    /**
     * Retrieve user
     *
     * @return mixed
     */
    public function getUserId()
    {
        if (($session = $this->_session) && ($user = $session->getUser())) {
            return $user->getId();
        }
        return false;
    }

    /**
     * Add Message
     *
     * @param string $type
     * @param string $msg
     * @param string $clear
     * @return Maged_Model_Session
     */
    public function addMessage($type, $msg, $clear = false)
    {
        $msgs = $this->getMessages($clear);
        $msgs[$type][] = $msg;
        $this->set('messages', $msgs);
        return $this;
    }

    /**
     * Retrieve messages from cache
     *
     * @param boolean $clear
     * @return mixed
     */
    public function getMessages($clear = true)
    {
        $msgs = $this->get('messages');
        $msgs = $msgs ? $msgs : array();
        if ($clear) {
            unset($_SESSION['messages']);
        }
        return $msgs;
    }

    /**
     * Retrieve url to adminhtml
     *
     * @return string
     */
    public function getReturnUrl()
    {
        if (!$this->_session || !$this->_session->isLoggedIn()) {
            return '';
        }
        return Mage::getSingleton('adminhtml/url')->getUrl('adminhtml');
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->get('_form_key')) {
            $this->set('_form_key', Mage::helper('core')->getRandomString(16));
        }
        return $this->get('_form_key');
    }

    /**
     * Validate Form Key
     *
     * @return bool
     */
    public function validateFormKey()
    {
        if (!($formKey = $_REQUEST['form_key']) || $formKey != $this->getFormKey()) {
            return false;
        }
        return true;
    }
}
