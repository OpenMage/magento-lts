<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order API
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Api extends Mage_Sales_Model_Api_Resource
{
    /**
     * Initialize attributes map
     */
    public function __construct()
    {
        $this->_attributesMap = [
            'order' => ['order_id' => 'entity_id'],
            'order_address' => ['address_id' => 'entity_id'],
            'order_payment' => ['payment_id' => 'entity_id'],
        ];
    }

    /**
     * Initialize basic order model
     *
     * @param mixed $orderIncrementId
     * @return Mage_Sales_Model_Order
     */
    protected function _initOrder($orderIncrementId)
    {
        $order = Mage::getModel('sales/order');

        /** @var Mage_Sales_Model_Order $order */

        $order->loadByIncrementId($orderIncrementId);

        if (!$order->getId()) {
            $this->_fault('not_exists');
        }

        return $order;
    }

    /**
     * Retrieve list of orders. Filtration could be applied
     *
     * @param null|array|object $filters
     * @return array
     */
    public function items($filters = null)
    {
        $orders = [];

        //TODO: add full name logic
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';

        $orderCollection = Mage::getModel('sales/order')->getCollection();
        $billingFirstnameField = "$billingAliasName.firstname";
        $billingLastnameField = "$billingAliasName.lastname";
        $shippingFirstnameField = "$shippingAliasName.firstname";
        $shippingLastnameField = "$shippingAliasName.lastname";
        $orderCollection->addAttributeToSelect('*')
            ->addAddressFields()
            ->addExpressionFieldToSelect(
                'billing_firstname',
                '{{billing_firstname}}',
                ['billing_firstname' => $billingFirstnameField],
            )
            ->addExpressionFieldToSelect(
                'billing_lastname',
                '{{billing_lastname}}',
                ['billing_lastname' => $billingLastnameField],
            )
            ->addExpressionFieldToSelect(
                'shipping_firstname',
                '{{shipping_firstname}}',
                ['shipping_firstname' => $shippingFirstnameField],
            )
            ->addExpressionFieldToSelect(
                'shipping_lastname',
                '{{shipping_lastname}}',
                ['shipping_lastname' => $shippingLastnameField],
            )
            ->addExpressionFieldToSelect(
                'billing_name',
                "CONCAT({{billing_firstname}}, ' ', {{billing_lastname}})",
                ['billing_firstname' => $billingFirstnameField, 'billing_lastname' => $billingLastnameField],
            )
            ->addExpressionFieldToSelect(
                'shipping_name',
                'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
                ['shipping_firstname' => $shippingFirstnameField, 'shipping_lastname' => $shippingLastnameField],
            );

        /** @var Mage_Api_Helper_Data $apiHelper */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['order']);
        try {
            foreach ($filters as $field => $value) {
                $orderCollection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('filters_invalid', $mageCoreException->getMessage());
        }

        foreach ($orderCollection as $order) {
            $orders[] = $this->_getAttributes($order, 'order');
        }

        return $orders;
    }

    /**
     * Retrieve full order information
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function info($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        if ($order->getGiftMessageId() > 0) {
            $order->setGiftMessage(
                Mage::getSingleton('giftmessage/message')->load($order->getGiftMessageId())->getMessage(),
            );
        }

        $result = $this->_getAttributes($order, 'order');

        $result['shipping_address'] = $this->_getAttributes($order->getShippingAddress(), 'order_address');
        $result['billing_address']  = $this->_getAttributes($order->getBillingAddress(), 'order_address');
        $result['items'] = [];

        foreach ($order->getAllItems() as $item) {
            if ($item->getGiftMessageId() > 0) {
                $item->setGiftMessage(
                    Mage::getSingleton('giftmessage/message')->load($item->getGiftMessageId())->getMessage(),
                );
            }

            $result['items'][] = $this->_getAttributes($item, 'order_item');
        }

        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');

        $result['status_history'] = [];

        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }

        return $result;
    }

    /**
     * Add comment to order
     *
     * @param string $orderIncrementId
     * @param string $status
     * @param string $comment
     * @param bool $notify
     * @return bool
     */
    public function addComment($orderIncrementId, $status, $comment = '', $notify = false)
    {
        $order = $this->_initOrder($orderIncrementId);

        $historyItem = $order->addStatusHistoryComment($comment, $status);
        $historyItem->setIsCustomerNotified($notify)->save();

        try {
            if ($notify && $comment) {
                $oldStore = Mage::getDesign()->getStore();
                $oldArea = Mage::getDesign()->getArea();
                Mage::getDesign()->setStore($order->getStoreId());
                Mage::getDesign()->setArea('frontend');
            }

            $order->save();
            $order->sendOrderUpdateEmail($notify, $comment);
            if (isset($oldStore, $oldArea) && $notify && $comment) {
                Mage::getDesign()->setStore($oldStore);
                Mage::getDesign()->setArea($oldArea);
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('status_not_changed', $mageCoreException->getMessage());
        }

        return true;
    }

    /**
     * Hold order
     *
     * @param string $orderIncrementId
     * @return bool
     */
    public function hold($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        try {
            $order->hold();
            $order->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('status_not_changed', $mageCoreException->getMessage());
        }

        return true;
    }

    /**
     * Unhold order
     *
     * @param string $orderIncrementId
     * @return bool
     */
    public function unhold($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        try {
            $order->unhold();
            $order->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('status_not_changed', $mageCoreException->getMessage());
        }

        return true;
    }

    /**
     * Cancel order
     *
     * @param string $orderIncrementId
     * @return bool
     */
    public function cancel($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        if (Mage_Sales_Model_Order::STATE_CANCELED == $order->getState()) {
            $this->_fault('status_not_changed');
        }

        try {
            $order->cancel();
            $order->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('status_not_changed', $mageCoreException->getMessage());
        }

        if (Mage_Sales_Model_Order::STATE_CANCELED != $order->getState()) {
            $this->_fault('status_not_changed');
        }

        return true;
    }
}
