<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * API2 class for orders (customer)
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Api2_Order_Rest_Customer_V1 extends Mage_Sales_Model_Api2_Order_Rest
{
    /**
     * Retrieve collection instance for orders
     *
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        return parent::_getCollectionForRetrieve()->addAttributeToFilter(
            'customer_id',
            ['eq' => $this->getApiUser()->getUserId()],
        );
    }

    /**
     * Retrieve collection instance for single order
     *
     * @param int $orderId Order identifier
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getCollectionForSingleRetrieve($orderId)
    {
        return parent::_getCollectionForSingleRetrieve($orderId)->addAttributeToFilter(
            'customer_id',
            ['eq' => $this->getApiUser()->getUserId()],
        );
    }

    /**
     * Prepare and return order comments collection
     *
     * @param array $orderIds Orders' identifiers
     * @return Mage_Sales_Model_Resource_Order_Status_History_Collection|Object
     */
    protected function _getCommentsCollection(array $orderIds)
    {
        return parent::_getCommentsCollection($orderIds)->addFieldToFilter('is_visible_on_front', 1);
    }
}
