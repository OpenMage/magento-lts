<?php
/**
 * Order status history collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Status_History_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_status_history');
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
