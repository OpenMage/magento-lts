<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Orders collection
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Mysql4_Order_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Initialize orders collection
     *
     */
    protected function _construct()
    {
        $this->_init('sales/order');
    }

    /**
     * Add order items count expression
     *
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    public function addItemCountExpr()
    {
        $orderTable = $this->getEntity()->getEntityTable();
        $orderItemEntityTypeId = Mage::getResourceSingleton('sales/order_item')->getTypeId();
        $this->getSelect()->join(
                array('items'=>$orderTable),
                'items.parent_id=e.entity_id and items.entity_type_id='.$orderItemEntityTypeId,
                array('items_count'=>new Zend_Db_Expr('COUNT(items.entity_id)'))
            )
            ->group('e.entity_id');
        return $this;
    }

    /**
     * Minimize usual count select
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->resetJoinLeft();
        return $countSelect;
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllIds($limit=null, $offset=null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->from(null, 'e.'.$this->getEntity()->getIdFieldName());
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }
}
