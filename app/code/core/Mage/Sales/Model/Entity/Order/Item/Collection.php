<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order status collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Item_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('sales/order_item');
    }

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderFilter($orderId)
    {
        $this->addAttributeToFilter('parent_id', $orderId);
        return $this;
    }
}
