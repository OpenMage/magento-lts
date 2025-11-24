<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * API2 class for order items (customer)
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Api2_Order_Item_Rest_Customer_V1 extends Mage_Sales_Model_Api2_Order_Item_Rest
{
    /**
     * Load order by id
     *
     * @param int $id
     * @return Mage_Sales_Model_Order
     * @throws Mage_Api2_Exception
     */
    protected function _loadOrderById($id)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($id);
        if (!$order->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        // check order owner
        if ($this->getApiUser()->getUserId() != $order->getCustomerId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $order;
    }
}
