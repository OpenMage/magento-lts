<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * API2 class for sales order comments (admin)
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Api2_Order_Comment_Rest_Admin_V1 extends Mage_Sales_Model_Api2_Order_Comment_Rest
{
    /**
     * Add comment to order
     *
     * @return string
     * @throws Exception
     * @throws Mage_Api2_Exception
     * @throws Throwable
     */
    protected function _create(array $filteredData)
    {
        $orderId = $this->getRequest()->getParam(self::PARAM_ORDER_ID);
        $order   = $this->_loadOrderById($orderId);

        $status         = $filteredData['status'] ?? false;
        $comment        = $filteredData['comment'] ?? '';
        $visibleOnFront = $filteredData['is_visible_on_front'] ?? 0;
        $notifyCustomer = array_key_exists('is_customer_notified', $filteredData) ? $filteredData['is_customer_notified'] : false;

        $historyItem = $order->addStatusHistoryComment($comment, $status);
        $historyItem->setIsCustomerNotified($notifyCustomer)
            ->setIsVisibleOnFront((int) $visibleOnFront)
            ->save();

        try {
            $oldStore = Mage::getDesign()->getStore();
            $oldArea = Mage::getDesign()->getArea();

            if ($notifyCustomer && $comment) {
                Mage::getDesign()->setStore($order->getStoreId());
                Mage::getDesign()->setArea('frontend');
            }

            $order->save();
            $order->sendOrderUpdateEmail((bool) $notifyCustomer, $comment);

            if ($notifyCustomer && $comment) {
                Mage::getDesign()->setStore($oldStore);
                Mage::getDesign()->setArea($oldArea);
            }
        } catch (Mage_Core_Exception) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        } catch (Throwable $throwable) {
            Mage::logException($throwable);
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }

        return $this->_getLocation($historyItem);
    }

    /**
     * Retrieve order comment by id
     *
     * @return array
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    protected function _retrieve()
    {
        $comment = Mage::getModel('sales/order_status_history')->load(
            $this->getRequest()->getParam(self::PARAM_COMMENT_ID),
        );

        if (!$comment->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $comment->getData();
    }
}
