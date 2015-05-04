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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Session Observer
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Observer_Session
{
    /**
     * Create/Update and Load session when customer log in
     *
     * @param Varien_Event_Observer $observer
     */
    public function synchronizePersistentOnLogin(Varien_Event_Observer $observer)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid (remove persistent cookie for invalid customer)
        if (!$customer || !$customer->getId() || !Mage::helper('persistent/session')->isRememberMeChecked()) {
            Mage::getModel('persistent/session')->removePersistentCookie();
            return;
        }

        $persistentLifeTime = Mage::helper('persistent')->getLifeTime();
        // Delete persistent session, if persistent could not be applied
        if (Mage::helper('persistent')->isEnabled() && ($persistentLifeTime <= 0)) {
            // Remove current customer persistent session
            Mage::getModel('persistent/session')->deleteByCustomerId($customer->getId());
            return;
        }

        /** @var $sessionModel Mage_Persistent_Model_Session */
        $sessionModel = Mage::helper('persistent/session')->getSession();

        // Check if session is wrong or not exists, so create new session
        if (!$sessionModel->getId() || ($sessionModel->getCustomerId() != $customer->getId())) {
            $sessionModel = Mage::getModel('persistent/session')
                ->setLoadExpired()
                ->loadByCustomerId($customer->getId());
            if (!$sessionModel->getId()) {
                $sessionModel = Mage::getModel('persistent/session')
                    ->setCustomerId($customer->getId())
                    ->save();
            }

            Mage::helper('persistent/session')->setSession($sessionModel);
        }

        // Set new cookie
        if ($sessionModel->getId()) {
            Mage::getSingleton('core/cookie')->set(
                Mage_Persistent_Model_Session::COOKIE_NAME,
                $sessionModel->getKey(),
                $persistentLifeTime
            );
        }
    }

    /**
     * Unload persistent session (if set in config)
     *
     * @param Varien_Event_Observer $observer
     */
    public function synchronizePersistentOnLogout(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent')->getClearOnLogout()) {
            return;
        }

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid
        if (!$customer || !$customer->getId()) {
            return;
        }

        Mage::getModel('persistent/session')->removePersistentCookie();

        // Unset persistent session
        Mage::helper('persistent/session')->setSession(null);
    }

    /**
     * Synchronize persistent session info
     *
     * @param Varien_Event_Observer $observer
     */
    public function synchronizePersistentInfo(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent/session')->isPersistent()) {
            return;
        }

        /** @var $sessionModel Mage_Persistent_Model_Session */
        $sessionModel = Mage::helper('persistent/session')->getSession();

        /** @var $request Mage_Core_Controller_Request_Http */
        $request = $observer->getEvent()->getFront()->getRequest();

        // Quote Id could be changed only by logged in customer
        if (Mage::getSingleton('customer/session')->isLoggedIn()
            || ($request && $request->getActionName() == 'logout' && $request->getControllerName() == 'account')
        ) {
            $sessionModel->save();
        }
    }

    /**
     * Set Checked status of "Remember Me"
     *
     * @param Varien_Event_Observer $observer
     */
    public function setRememberMeCheckedStatus(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent')->isRememberMeEnabled()
        ) {
            return;
        }

        /** @var $controllerAction Mage_Core_Controller_Varien_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if ($controllerAction) {
            $rememberMeCheckbox = $controllerAction->getRequest()->getPost('persistent_remember_me');
            Mage::helper('persistent/session')->setRememberMeChecked((bool)$rememberMeCheckbox);
            if (
                $controllerAction->getFullActionName() == 'checkout_onepage_saveBilling'
                    || $controllerAction->getFullActionName() == 'customer_account_createpost'
            ) {
                Mage::getSingleton('checkout/session')->setRememberMeChecked((bool)$rememberMeCheckbox);
            }
        }
    }

    /**
     * Renew persistent cookie
     *
     * @param Varien_Event_Observer $observer
     */
    public function renewCookie(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent/session')->isPersistent()
        ) {
            return;
        }

        /** @var $controllerAction Mage_Core_Controller_Front_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();

        if (Mage::getSingleton('customer/session')->isLoggedIn()
            || $controllerAction->getFullActionName() == 'customer_account_logout'
        ) {
            Mage::getSingleton('core/cookie')->renew(
                Mage_Persistent_Model_Session::COOKIE_NAME,
                Mage::helper('persistent')->getLifeTime()
            );
        }
    }
}
