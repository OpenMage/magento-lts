<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Persistent
 */

/**
 * Persistent front controller
 *
 * @package    Mage_Persistent
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
     * @return $this
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
    }

    /**
     * Revert all persistent data
     *
     * @return $this
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
            /** @var Mage_Customer_Model_Session $customerSession */
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
            Mage::helper('persistent')->__('Shopping cart has been updated with appropriate prices'),
        );
        $this->_redirect('checkout/cart');
    }
}
