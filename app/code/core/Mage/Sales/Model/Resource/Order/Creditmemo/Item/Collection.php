<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Flat sales order creditmemo items collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection extends Mage_Sales_Model_Resource_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_creditmemo_item_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_creditmemo_item_collection';

    protected function _construct()
    {
        $this->_init('sales/order_creditmemo_item');
    }

    /**
     * Set creditmemo filter
     *
     * @param int $creditmemoId
     * @return $this
     */
    public function setCreditmemoFilter($creditmemoId)
    {
        $this->addFieldToFilter('parent_id', $creditmemoId);
        return $this;
    }
}
