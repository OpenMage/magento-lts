<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 */

/**
 * Authorizenet directpayment observer
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 */
class Mage_Authorizenet_Model_Directpost_Observer
{
    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @return $this
     */
    public function saveOrderAfterSubmit(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getData('order');
        Mage::register('directpost_order', $order, true);

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @return $this
     */
    public function addAdditionalFieldsToResponseFrontend(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::registry('directpost_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && $payment->getMethod() == Mage::getModel('authorizenet/directpost')->getCode()) {
                /** @var Mage_Core_Controller_Varien_Action $controller */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = Mage::helper('core')->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    Zend_Json::TYPE_ARRAY,
                );

                if (empty($result['error'])) {
                    $payment = $order->getPayment();
                    //if success, then set order to session and add new fields
                    $session = Mage::getSingleton('authorizenet/directpost_session');
                    $session->addCheckoutOrderIncrementId($order->getIncrementId());
                    $session->setLastOrderIncrementId($order->getIncrementId());
                    $requestToPaygate = $payment->getMethodInstance()->generateRequestFromOrder($order);
                    $requestToPaygate->setControllerActionName($controller->getRequest()->getControllerName());
                    $requestToPaygate->setIsSecure((string) Mage::app()->getStore()->isCurrentlySecure());

                    $result['directpost'] = ['fields' => $requestToPaygate->getData()];

                    $controller->getResponse()->clearHeader('Location');
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }

        return $this;
    }

    /**
     * Update all edit increments for all orders if module is enabled.
     * Needed for correct work of edit orders in Admin area.
     *
     * @return $this
     */
    public function updateAllEditIncrements(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getData('order');
        Mage::helper('authorizenet')->updateOrderEditIncrements($order);

        return $this;
    }
}
