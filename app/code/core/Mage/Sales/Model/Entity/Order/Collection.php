<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Orders collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
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
