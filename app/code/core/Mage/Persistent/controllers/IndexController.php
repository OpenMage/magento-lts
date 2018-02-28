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
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Persistent front controller
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Whether clear checkout session when logout
     *
     * @var bool
     */
    protected $_clearCheckoutSession = true;

    /**
     * Set whether clear checkout session when logout
     *
     * @param bool $clear
     * @return Mage_Persistent_IndexController
     */
    public function setClearCheckoutSession($clear = true)
    {
        $this->_clearCheckoutSession = $clear;
        return $this;
    }

    /**
     * Retrieve 'persistent session' helper instance
     *
     * @return Mage_Persistent_Helper_Session
     */
    protected function _getHelper()
    {
        return Mage::helper('persistent/session');
    }

    /**
     * Unset persistent cookie action
     */
    public function unsetCookieAction()
    {
        if ($this->_getHelper()->isPersistent()) {
            $this->_cleanup();
        }
        $this->_redirect('customer/account/login');
        return;
    }

    /**
     * Revert all persistent data
     *
     * @return Mage_Persistent_IndexController
     */
    protected function _cleanup()
    {
        Mage::dispatchEvent('persistent_session_expired');
        $customerSession = Mage::getSingleton('customer/session');
        $customerSession
            ->setCustomerId(null)
            ->setCustomerGroupId(null);
        if ($this->_clearCheckoutSession) {
            Mage::getSingleton('checkout/session')->unsetAll();
        }
        $this->_getHelper()->getSession()->removePersistentCookie();
        return $this;
    }

    /**
     * Save onepage checkout method to be register
     */
    public function saveMethodAction()
    {
        if ($this->_getHelper()->isPersistent()) {
            $this->_getHelper()->getSession()->removePersistentCookie();
            /** @var $customerSession Mage_Customer_Model_Session */
            $customerSession = Mage::getSingleton('customer/session');
            if (!$customerSession->isLoggedIn()) {
                $customerSession->setCustomerId(null)
                    ->setCustomerGroupId(null);
            }

            Mage::getSingleton('persistent/observer')->setQuoteGuest();
        }

        $checkoutUrl = $this->_getRefererUrl();
        $this->_redirectUrl($checkoutUrl . (strpos($checkoutUrl, '?') ? '&' : '?') . 'register');
    }

    /**
     * Add appropriate session message and redirect to shopping cart
     * used for paypal express checkout
     */
    public function expressCheckoutAction()
    {
        Mage::getSingleton('core/session')->addNotice(
            Mage::helper('persistent')->__('Shopping cart has been updated with appropriate prices')
        );
        $this->_redirect('checkout/cart');
    }
}
