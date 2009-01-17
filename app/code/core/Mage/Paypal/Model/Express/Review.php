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
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Paypal_Model_Express_Review
{
    /**
     * Enter description here...
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Enter description here...
     *
     * @param int $addressId
     * @return Mage_Customer_Model_Address
     */
    public function getAddress($addressId)
    {
        $address = Mage::getModel('customer/address')->load((int)$addressId);
        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $address->setRegion($address->getRegionId());
        }
        return $address;
    }

    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('paypalUk')->__('Invalid data')
            );
            return $res;
        }

        $this->getQuote()->getShippingAddress()
                           ->setShippingMethod($shippingMethod)
                           ->setCollectShippingRates(true);
        $this->getQuote()->collectTotals()->save();
        return array();
    }

    /**
     * Enter description here...
     *
     * @return array
     * following method is obsolete
     * to set cctransid, we can only set to payment method
     */
    public function saveOrder()
    {
        $res = array('error'=>true);

        try {
            $billing = $this->getQuote()->getBillingAddress();
            $shipping = $this->getQuote()->getShippingAddress();


            $convertQuote = Mage::getModel('sales/convert_quote');
            /* @var $convertQuote Mage_Sales_Model_Convert_Quote */
            $order = Mage::getModel('sales/order');
            /* @var $order Mage_Sales_Model_Order */

            if ($this->getQuote()->isVirtual()) {
                $order = $convertQuote->addressToOrder($billing);
            } else {
                $order = $convertQuote->addressToOrder($shipping);
            }

            $order->setBillingAddress($convertQuote->addressToOrderAddress($billing));
            $order->setShippingAddress($convertQuote->addressToOrderAddress($shipping));
            $order->setPayment($convertQuote->paymentToOrderPayment($this->getQuote()->getPayment()));

            foreach ($this->getQuote()->getAllItems() as $item) {
               $order->addItem($convertQuote->itemToOrderItem($item));
            }

            /**
             * We can use configuration data for declare new order status
             */
            Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$this->getQuote()));
            #$order->save();
            $order->place();
            $order->save();

            $this->getQuote()->setIsActive(false);
            $this->getQuote()->save();

            $orderId = $order->getIncrementId();
            $this->getCheckout()->setLastQuoteId($this->getQuote()->getId());
            $this->getCheckout()->setLastOrderId($order->getId());
            $this->getCheckout()->setLastRealOrderId($order->getIncrementId());

            $order->sendNewOrderEmail();

            $res['success'] = true;
            $res['error']   = false;
            //$res['error']   = true;
        }
        catch (Exception $e){
            $res['success'] = false;
            $res['error'] = true;
            $res['error_messages'] = $order->getErrors();
        }

        return $res;
    }
}