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
 * @category    Mage
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth observer
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Model_Observer
{
    /**
     * Get callback url
     *
     * @param string $userType
     * @return string
     */
    protected function _getAfterAuthUrl($userType)
    {
        $simple = Mage::app()->getRequest()->getParam('simple');

        if (Mage_OAuth_Model_Token::USER_TYPE_CUSTOMER == $userType) {
            if ($simple) {
                $route = Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE;
            } else {
                $route = Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE_CUSTOMER;
            }
        } elseif (Mage_OAuth_Model_Token::USER_TYPE_ADMIN == $userType) {
            if ($simple) {
                $route = Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE_ADMIN_SIMPLE;
            } else {
                $route = Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE_ADMIN;
            }
        } else {
            throw new Exception('Invalid user type.');
        }

        return Mage::getUrl($route, array('_query' => array('oauth_token' => $this->_getOauthToken())));
    }

    /**
     * Retrieve oauth_token param from request
     *
     * @return string|null
     */
    protected function _getOauthToken()
    {
        return Mage::app()->getRequest()->getParam('oauth_token', null);
    }

    /**
     * Redirect customer to callback page after login success
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function afterCustomerLogin(Varien_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            /** @var $session Mage_Customer_Model_Session */
            $session = Mage::getSingleton('customer/session');
            $session->setAfterAuthUrl($this->_getAfterAuthUrl(Mage_OAuth_Model_Token::USER_TYPE_CUSTOMER));
        }
    }

    /**
     * Redirect admin to authorize controller after login success
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function afterAdminLogin(Varien_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $userType = Mage_OAuth_Model_Token::USER_TYPE_ADMIN;

            $url = $this->_getAfterAuthUrl($userType);
            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
        }
    }

    public function afterAdminLoginFailed(Varien_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            /** @var $session Mage_Admin_Model_Session */
            $session = Mage::getSingleton('admin/session');
            $session->addError($observer->getException()->getMessage());

            $params = array('oauth_token' => $this->_getOauthToken());

            if (Mage::app()->getRequest()->getParam('simple')) {
                $route = Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE_ADMIN_SIMPLE;
            } else {
                $route = Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE_ADMIN;
            }
            $url = Mage::getUrl($route, array('_query' => $params));

            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
        }
    }
}
