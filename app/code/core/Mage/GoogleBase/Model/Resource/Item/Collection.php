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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Google Base items collection
 *
 * @deprecated after 1.5.1.0 
 * @category    Mage
 * @package     Mage_GoogleBase
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Resource_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('googlebase/item');
    }

    /**
     * Init collection select
     *
     * @return Mage_GoogleBase_Model_Resource_Item_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinTables();
        return $this;
    }

    /**
     * Deprecated
     *
     * @param int $storeId
     * @return Mage_GoogleBase_Model_Resource_Item_Collection
     */
    public function addStoreFilterId($storeId)
    {
        return $this->addStoreFilter($storeId);
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return Mage_GoogleBase_Model_Resource_Item_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }

    /**
     * Filter collection by product id
     *
     * @param int $productId
     * @return Mage_GoogleBase_Model_Resource_Item_Collection
     */
    public function addProductFilterId($productId)
    {
        $this->getSelect()->where('main_table.product_id=?', $productId);
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @param string $field
     * @param null|string|array $condition
     * @return Mage_GoogleBase_Model_Resource_Item_Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'name') {
            $codeExpr = $this->getConnection()->getCheckSql('p.value IS NOT NULL', 'p.value', 'p_d.value');
            $conditionSql = $this->_getConditionSql($codeExpr, $condition);
            $this->getSelect()->where($conditionSql, null, Varien_Db_Select::TYPE_CONDITION);
        } else {
            parent::addFieldToFilter($field, $condition);
        }
        return $this;
    }

    /**
     * Join product and type data
     *
     * @return Mage_GoogleBase_Model_Resource_Item_Collection
     */
    protected function _joinTables()
    {
        $adapter = $this->getConnection();
        $entityType = Mage::getSingleton('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY);
        $attribute = Mage::getModel('eav/config')->getAttribute($entityType->getEntityTypeId(), 'name');

        $joinConditionDefault = $adapter->quoteInto('p_d.attribute_id=?', $attribute->getAttributeId()) .
            $adapter->quoteInto(' AND p_d.store_id=?', 0) . ' AND main_table.product_id=p_d.entity_id';

        $joinCondition = $adapter->quoteInto('p.attribute_id=?', $attribute->getAttributeId()) .
            ' AND p.store_id=main_table.store_id AND main_table.product_id=p.entity_id';

        $this->getSelect()
            ->joinLeft(
                array('p_d' => $attribute->getBackend()->getTable()),
                $joinConditionDefault,
                array()
            );

        $codeExpr = $adapter->getCheckSql('p.value IS NOT NULL', 'p.value', 'p_d.value');
        $this->getSelect()
            ->joinLeft(
                array('p' => $attribute->getBackend()->getTable()),
                $joinCondition,
                array('name' => $codeExpr)
            );

        $codeExpr = $adapter->getCheckSql(
            'types.gbase_itemtype IS NOT NULL',
            'types.gbase_itemtype',
            $adapter->quote(Mage_GoogleBase_Model_Service_Item::DEFAULT_ITEM_TYPE)
        );

        $this->getSelect()
            ->joinLeft(
                array('types' => $this->getTable('googlebase/types')),
                'main_table.type_id=types.type_id',
                array('gbase_itemtype' => $codeExpr)
            );

        return $this;
    }
}
