<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Oauth
 */

/**
 * OAuth observer
 *
 * @category   Mage
 * @package    Mage_Oauth
 */
class Mage_Oauth_Model_Observer
{
    /**
     * Retrieve oauth_token param from request
     *
     * @return string|null
     */
    protected function _getOauthToken()
    {
        return Mage::helper('oauth')->getOauthToken();
    }

    /**
     * Redirect customer to callback page after login
     *
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    public function afterCustomerLogin(Varien_Event_Observer $observer)
    {
        if ($this->_getOauthToken() !== null) {
            $userType = Mage_Oauth_Model_Token::USER_TYPE_CUSTOMER;
            $url = Mage::helper('oauth')->getAuthorizeUrl($userType);
            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }

    /**
     * Redirect admin to authorize controller after login success
     *
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    public function afterAdminLogin(Varien_Event_Observer $observer)
    {
        if ($this->_getOauthToken() !== null) {
            $userType = Mage_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = Mage::helper('oauth')->getAuthorizeUrl($userType);
            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }

    /**
     * Redirect admin to authorize controller after login fail
     *
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    public function afterAdminLoginFailed(Varien_Event_Observer $observer)
    {
        if ($this->_getOauthToken() !== null) {
            /** @var Mage_Admin_Model_Session $session */
            $session = Mage::getSingleton('admin/session');
            $session->addError($observer->getException()->getMessage());

            $userType = Mage_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = Mage::helper('oauth')->getAuthorizeUrl($userType);
            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }
}
