<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales order shippment API V2
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Shipment_Api_V2 extends Mage_Sales_Model_Order_Shipment_Api
{
    /**
     * @param array $data
     * @return array
     */
    protected function _prepareItemQtyData($data)
    {
        $_data = [];
        foreach ($data as $item) {
            if (isset($item->order_item_id) && isset($item->qty)) {
                $_data[$item->order_item_id] = $item->qty;
            }
        }

        return $_data;
    }

    /**
     * Create new shipment for order
     *
     * @param string $orderIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param bool $email
     * @param bool $includeComment
     * @return null|string
     */
    public function create(
        $orderIncrementId,
        $itemsQty = [],
        $comment = null,
        $email = false,
        $includeComment = false
    ) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $itemsQty = $this->_prepareItemQtyData($itemsQty);
        /**
         * Check order existing
         */
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }

        /**
         * Check shipment create availability
         */
        if (!$order->canShip()) {
            $this->_fault('data_invalid', Mage::helper('sales')->__('Cannot do shipment for order.'));
        }

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $order->prepareShipment($itemsQty);
        if ($shipment) {
            $shipment->register();
            $shipment->addComment($comment, $email && $includeComment);
            if ($email) {
                $shipment->setEmailSent(true);
            }

            $shipment->getOrder()->setIsInProcess(true);
            try {
                Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();
                $shipment->sendEmail($email, ($includeComment ? $comment : ''));
            } catch (Mage_Core_Exception $e) {
                $this->_fault('data_invalid', $e->getMessage());
            }

            return $shipment->getIncrementId();
        }

        return null;
    }

    /**
     * Retrieve allowed shipping carriers for specified order
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function getCarriers($orderIncrementId)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        /**
         * Check order existing
         */
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }

        $carriers = [];
        foreach ($this->_getCarriers($order) as $key => $value) {
            $carriers[] = ['key' => $key, 'value' => $value];
        }

        return $carriers;
    }
}
