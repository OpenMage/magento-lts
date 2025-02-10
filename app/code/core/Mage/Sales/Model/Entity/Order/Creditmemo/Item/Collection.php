<?php
/**
 * Quote creditmemo items collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Creditmemo_Item_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_creditmemo_item');
    }

    /**
     * @param int $creditmemoId
     * @return $this
     */
    public function setCreditmemoFilter($creditmemoId)
    {
        $this->addAttributeToFilter('parent_id', $creditmemoId);
        return $this;
    }
}
