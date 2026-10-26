<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog EAV collection resource abstract model
 * Implement using different stores for retrieve attribute values
 *
 * @package    Mage_Catalog
 * @template T of Mage_Catalog_Model_Abstract
 * @extends Mage_Eav_Model_Entity_Collection_Abstract<T>
 */
class Mage_Catalog_Model_Resource_Collection_Abstract extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Current scope (store Id)
     *
     * @var null|int
     */
    protected $_storeId;

    /**
     * Set store scope
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->setStoreId(Mage::app()->getStore($store)->getId());
        return $this;
    }

    /**
     * Set store scope
     *
     * @param  int|Mage_Core_Model_Store|string $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof Mage_Core_Model_Store) {
            $storeId = $storeId->getId();
        }

        $this->_storeId = (int) $storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        return $this->_storeId;
    }

    /**
     * Retrieve default store id
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _getLoadAttributesSelect($table, $attributeIds = [])
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }

        $storeId = $this->getStoreId();

        if ($storeId) {
            $adapter = $this->getConnection();
            $entity  = $this->getEntity();

            // see also Mage_Catalog_Model_Resource_Abstract::getAttributeRawValue()
            $select = $adapter->select()
                ->from(['e' => $entity->getEntityTable()], [])
                ->where('e.entity_id IN (?)', array_keys($this->_itemsById));
            // attr join
            $select->joinInner(
                ['attr' => $table],
                implode(' AND ', [
                    'attr.entity_id = e.entity_id',
                    $adapter->quoteInto('attr.attribute_id IN (?)', $attributeIds),
                    'attr.store_id IN (' . $this->getDefaultStoreId() . ', ' . $storeId . ')',
                    'attr.entity_type_id = ' . $entity->getTypeId(),
                ]),
                [],
            );
            // t_d join
            $select->joinLeft(
                ['t_d' => $table],
                implode(' AND ', [
                    't_d.entity_id = e.entity_id',
                    't_d.attribute_id = attr.attribute_id',
                    't_d.store_id = ' . $this->getDefaultStoreId(),
                ]),
                [],
            );
            // t_s join
            $attributeIdExpr = $adapter->getCheckSql(
                't_s.attribute_id IS NULL',
                't_d.attribute_id',
                't_s.attribute_id',
            );
            $select->joinLeft(
                ['t_s' => $table],
                implode(' AND ', [
                    't_s.entity_id = e.entity_id',
                    't_s.attribute_id = attr.attribute_id',
                    't_s.store_id = ' . $storeId,
                ]),
                ['e.entity_id', 'attribute_id' => $attributeIdExpr],
            );
            $select->group('e.entity_id')->group('attr.attribute_id');

            return $select;
        }

        return parent::_getLoadAttributesSelect($table)
            ->where('store_id = ?', $this->getDefaultStoreId());
    }

    /**
     * @param  Varien_Db_Select $select
     * @param  string           $table
     * @param  string           $type
     * @return Varien_Db_Select
     */
    #[Override]
    protected function _addLoadAttributesSelectValues($select, $table, $type)
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            /** @var Mage_Eav_Model_Resource_Helper_Mysql4 $helper */
            $helper = Mage::getResourceHelper('eav');
            $adapter        = $this->getConnection();
            $valueExpr      = $adapter->getCheckSql(
                't_s.value_id IS NULL',
                $helper->prepareEavAttributeValue('t_d.value', $type),
                $helper->prepareEavAttributeValue('t_s.value', $type),
            );

            $select->columns([
                'default_value' => $helper->prepareEavAttributeValue('t_d.value', $type),
                'store_value'   => $helper->prepareEavAttributeValue('t_s.value', $type),
                'value'         => $valueExpr,
            ]);
        } else {
            $select = parent::_addLoadAttributesSelectValues($select, $table, $type);
        }

        return $select;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param  string                                    $method
     * @param  object                                    $attribute
     * @param  string                                    $tableAlias
     * @param  array                                     $condition
     * @param  string                                    $fieldCode
     * @param  string                                    $fieldAlias
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    #[Override]
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        $storeId = $this->_joinAttributes[$fieldCode]['store_id'] ?? $this->getStoreId();

        $adapter = $this->getConnection();

        if ($storeId != $this->getDefaultStoreId() && !$attribute->isScopeGlobal()) {
            /**
             * Add joining default value for not default store
             * if value for store is null - we use default value
             */
            $defCondition = '(' . implode(') AND (', $condition) . ')';
            $defAlias     = $tableAlias . '_default';
            $defAlias     = $this->getConnection()->getTableName($defAlias);
            $defFieldAlias = str_replace($tableAlias, $defAlias, $fieldAlias);
            $tableAlias   = $this->getConnection()->getTableName($tableAlias);

            $defCondition = str_replace($tableAlias, $defAlias, $defCondition);
            $defCondition .= $adapter->quoteInto(
                ' AND ' . $adapter->quoteColumnAs("{$defAlias}.store_id", null) . ' = ?',
                $this->getDefaultStoreId(),
            );

            $this->getSelect()->$method(
                [$defAlias => $attribute->getBackend()->getTable()],
                $defCondition,
                [],
            );

            $method = 'joinLeft';
            $fieldAlias = $this->getConnection()->getCheckSql(
                "{$tableAlias}.value_id > 0",
                $fieldAlias,
                $defFieldAlias,
            );
            $this->_joinAttributes[$fieldCode]['condition_alias'] = $fieldAlias;
            $this->_joinAttributes[$fieldCode]['attribute']       = $attribute;
        } else {
            $storeId = $this->getDefaultStoreId();
        }

        $condition[] = $adapter->quoteInto(
            $adapter->quoteColumnAs("{$tableAlias}.store_id", null) . ' = ?',
            $storeId,
        );
        return parent::_joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias);
    }
}
