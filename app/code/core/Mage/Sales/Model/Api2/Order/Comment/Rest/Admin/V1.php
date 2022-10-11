<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 class for sales order comments (admin)
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Api2_Order_Comment_Rest_Admin_V1 extends Mage_Sales_Model_Api2_Order_Comment_Rest
{
    /**
     * Add comment to order
     * 
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        $orderId = $this->getRequest()->getParam(self::PARAM_ORDER_ID);
        $order   = $this->_loadOrderById($orderId);

        $status         = $data['status'] ?? false;
        $comment        = $data['comment'] ?? '';
        $visibleOnFront = $data['is_visible_on_front'] ?? 0;
        $notifyCustomer = array_key_exists('is_customer_notified', $data) ? $data['is_customer_notified'] : false;

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
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), self::RESOURCE_INTERNAL_ERROR);
        } catch (Throwable $t) {
            Mage::logException($t);
            $this->_critical($t->getMessage(), self::RESOURCE_UNKNOWN_ERROR);
        }

        return $this->_getLocation($historyItem);
    }

    /**
     * Retrieve order comment by id
     * 
     * @return array
     */
    protected function _retrieve()
    {
        $comment = Mage::getModel('sales/order_status_history')->load(
            $this->getRequest()->getParam(self::PARAM_COMMENT_ID)
        );

        if (!$comment->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $comment->getData();
    }
}
