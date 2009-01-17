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
 * @category   Mage
 * @package    Mage_GoogleCheckout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_GoogleCheckout_Model_Api extends Varien_Object
{
    protected function _getApi($area)
    {
        $api = Mage::getModel('googlecheckout/api_xml_'.$area);
        $api->setApi($this);
        return $api;
    }

// CHECKOUT
    public function checkout(Mage_Sales_Model_Quote $quote)
    {
        $api = $this->_getApi('checkout')
            ->setQuote($quote)
            ->checkout();
        return $api;
    }

// FINANCIAL COMMANDS
    public function authorize($gOrderId)
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->authorize();
        return $api;
    }

    public function charge($gOrderId, $amount)
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->charge($amount);
        return $api;
    }

    public function refund($gOrderId, $amount, $reason, $comment='')
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->refund($amount, $reason, $comment);
        return $api;
    }

    public function cancel($gOrderId, $reason, $comment='')
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->cancel($reason, $comment);
        return $api;
    }

// FULFILLMENT COMMANDS (ORDER BASED)

    public function process($gOrderId)
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->process();
        return $api;
    }

    public function deliver($gOrderId, $carrier, $trackingNo, $sendMail=true)
    {
        $gCarriers = array('dhl'=>'DHL', 'fedex'=>'FedEx', 'ups'=>'UPS', 'usps'=>'USPS');
        $carrier = strtolower($carrier);
        $carrier = isset($gCarriers[$carrier]) ? $gCarriers[$carrier] : 'Other';

        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->deliver($carrier, $trackingNo, $sendMail);
        return $api;
    }

    public function addTrackingData($gOrderId, $carrier, $trackingNo)
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->addTrackingData($carrier, $trackingNo);
        return $api;
    }

// FULFILLMENT COMMANDS (ITEM BASED)

    public function shipItems(Mage_Sales_Model_Order $order, array $items)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function backorderItems()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function returnItems()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function cancelItems()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function resetItemsShippingInformation()
    {

    }

    public function addMerchantOrderNumber()
    {

    }

    public function sendBuyerMessage()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

// OTHER ORDER COMMANDS

    public function archiveOrder()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function unarchiveOrder()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

// WEB SERVICE SERVER PROCEDURES

    public function processCallback()
    {
        $api = $this->_getApi('callback')->process();
        return $api;
    }

    public function processBeacon()
    {
        $debug = Mage::getModel('googlecheckout/api_debug')->setDir('in')
            ->setUrl('googlecheckout/api/beacon')
            ->setRequestBody($_SERVER['QUERY_STRING'])
            ->save();
    }
}