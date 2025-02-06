<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * API2 class for order address (customer)
 *
 * @category   Mage
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
