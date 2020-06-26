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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Adminhtml mobile user controller
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Adminhtml_Connect_UserController extends Mage_XmlConnect_Controller_AdminAction
{
    /**
     * Get admin user session
     *
     * @return Mage_Admin_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * Login form action
     */
    public function loginFormAction()
    {
        try {
            $result = $this->loadLayout(false)->getLayout()
                ->addBlock('xmlconnect/adminhtml_connect_loginform', 'login_form')->toHtml();
            $this->getResponse()->setBody($result);
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $this->_message(Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_USER_SPACE_DEFAULT, $e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_SERVER_SP_DEFAULT,
                $this->__('An error occurred while loading login form.')
            );
        }
    }

    /**
     * User login action
     */
    public function loginAction()
    {
        try {
            $session = $this->_getSession();
            $request = $this->getRequest();

            if ($session->isLoggedIn()) {
                $this->_message(
                    Mage_XmlConnect_Model_Simplexml_Message::MESSAGE_STATUS_SUCCESS,
                    $this->__('Authentication complete.')
                );
                return;
            }

            $postLogin = $request->getPost('login_info');
            $request->setPost('login_info', null);
            if ($postLogin) {
                $username = isset($postLogin['username']) ? $postLogin['username'] : '';
                $password = isset($postLogin['password']) ? $postLogin['password'] : '';
                $user = $session->login($username, $password);
                $request->setPost('login', null);
                if ($user && $session->isLoggedIn()) {
                    $this->_message(
                        Mage_XmlConnect_Model_Simplexml_Message::MESSAGE_STATUS_SUCCESS,
                        $this->__('Authentication complete.')
                    );
                    return;
                } else {
                    $this->_message(
                        Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_USER_SPACE_DEFAULT,
                        $this->__('Invalid login or password.')
                    );
                    return;
                }
            }
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_CLIENT_SP_DEFAULT,
                $this->__('Post data is empty.')
            );
        } catch (Mage_Core_Exception $e) {
            $this->_message(Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_USER_SPACE_DEFAULT, $e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_SERVER_SP_DEFAULT,
                $this->__('An error occurred while loading login form.')
            );
        }
    }

    /**
     * User logout action
     */
    public function logoutAction()
    {
        try {
            $this->_getSession()->unsetAll();
            $this->_getSession()->getCookie()->delete($this->_getSession()->getSessionName());
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message::MESSAGE_STATUS_SUCCESS,
                Mage::helper('adminhtml')->__('You have logged out.')
            );
        } catch (Mage_Core_Exception $e) {
            $this->_message(Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_USER_SPACE_DEFAULT, $e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_SERVER_SP_DEFAULT,
                $this->__('An error occurred while loading login form.')
            );
        }
    }

    /**
     * Check the permission to run actions
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        if (!Mage::getSingleton('xmlconnect/configuration')->isActiveAdminApp()) {
            $this->_forward('denied');
        }
        return true;
    }

    /**
     * Is check cookie required flag
     *
     * @return bool
     */
    protected function _isCheckCookieRequired()
    {
        return false;
    }
}
