<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * @SuppressWarnings(PHPMD.ExitExpression)
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
     * @SuppressWarnings(PHPMD.ExitExpression)
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
     * @SuppressWarnings(PHPMD.ExitExpression)
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
