<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Orders collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order');
    }

    /**
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function addItemCountExpr()
    {
        $orderTable = $this->getEntity()->getEntityTable();
        $orderItemEntityTypeId = Mage::getResourceSingleton('sales/order_item')->getTypeId();
        $this->getSelect()->join(
            ['items' => $orderTable],
            'items.parent_id=e.entity_id and items.entity_type_id=' . $orderItemEntityTypeId,
            ['items_count' => new Zend_Db_Expr('COUNT(items.entity_id)')],
        )
            ->group('e.entity_id');
        return $this;
    }
}
