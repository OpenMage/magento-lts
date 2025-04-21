<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * API2 class for order address (customer)
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Api2_Order_Address_Rest_Customer_V1 extends Mage_Sales_Model_Api2_Order_Address_Rest
{
    /**
     * Retrieve collection instances
     *
     * @return Mage_Sales_Model_Resource_Order_Address_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        $collection = parent::_getCollectionForRetrieve();
        $collection->addAttributeToFilter('customer_id', $this->getApiUser()->getUserId());

        return $collection;
    }
}
