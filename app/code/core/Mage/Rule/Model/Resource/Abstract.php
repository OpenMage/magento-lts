<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract Rule entity resource model
 *
 * @category   Mage
 * @package    Mage_Rule
 */
abstract class Mage_Rule_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * Example:
     * array(
     *    'entity_type1' => array(
     *        'associations_table' => 'table_name',
     *        'rule_id_field'      => 'rule_id',
     *        'entity_id_field'    => 'entity_id'
     *    ),
     *    'entity_type2' => array(
     *        'associations_table' => 'table_name',
     *        'rule_id_field'      => 'rule_id',
     *        'entity_id_field'    => 'entity_id'
     *    )
     *    ....
     * )
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [];

    /**
     * Prepare rule's active "from" and "to" dates
     *
     *
     * @return Mage_Rule_Model_Resource_Abstract
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $fromDate = $object->getFromDate();
        if ($fromDate instanceof Zend_Date) {
            $object->setFromDate($fromDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        } elseif (!is_string($fromDate) || empty($fromDate)) {
            $object->setFromDate(null);
        }

        $toDate = $object->getToDate();
        if ($toDate instanceof Zend_Date) {
            $object->setToDate($toDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        } elseif (!is_string($toDate) || empty($toDate)) {
            $object->setToDate(null);
        }

        parent::_beforeSave($object);
        return $this;
    }

    /**
     * Prepare select for condition
     *
     * @param int $storeId
     * @param Mage_Rule_Model_Condition_Abstract $condition
     * @return Varien_Db_Select
     */
    public function getProductFlatSelect($storeId, $condition)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(
            ['p' => $this->getTable('catalog/product')],
            [new Zend_Db_Expr('DISTINCT p.entity_id')]
        )
            ->joinInner(
                ['cpf' => $this->getTable('catalog/product_flat') . '_' . $storeId],
                'cpf.entity_id = p.entity_id',
                []
            )->joinLeft(
                ['ccp' => $this->getTable('catalog/category_product')],
                'ccp.product_id = p.entity_id',
                []
            );

        $where = $condition->prepareConditionSql();
        if (!empty($where)) {
            $select->where($where);
        }

        return $select;
    }

    /**
     * Bind specified rules to entities
     *
     * @param array|int|string $ruleIds
     * @param array|int|string $entityIds
     * @param string $entityType
     * @param bool $deleteOldResults
     *
     * @throws Exception
     * @return Mage_Rule_Model_Resource_Abstract
     */
    public function bindRuleToEntity($ruleIds, $entityIds, $entityType, $deleteOldResults = true)
    {
        if (empty($ruleIds) || empty($entityIds)) {
            return $this;
        }
        $adapter    = $this->_getWriteAdapter();
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        if (!is_array($ruleIds)) {
            $ruleIds = [(int) $ruleIds];
        }
        if (!is_array($entityIds)) {
            $entityIds = [(int) $entityIds];
        }

        $data  = [];
        $count = 0;

        $adapter->beginTransaction();

        try {
            foreach ($ruleIds as $ruleId) {
                foreach ($entityIds as $entityId) {
                    $data[] = [
                        $entityInfo['entity_id_field'] => $entityId,
                        $entityInfo['rule_id_field'] => $ruleId
                    ];
                    $count++;
                    if (($count % 1000) == 0) {
                        $adapter->insertOnDuplicate(
                            $this->getTable($entityInfo['associations_table']),
                            $data,
                            [$entityInfo['rule_id_field']]
                        );
                        $data = [];
                    }
                }
            }
            if (!empty($data)) {
                $adapter->insertOnDuplicate(
                    $this->getTable($entityInfo['associations_table']),
                    $data,
                    [$entityInfo['rule_id_field']]
                );
            }

            if ($deleteOldResults) {
                $adapter->delete(
                    $this->getTable($entityInfo['associations_table']),
                    $adapter->quoteInto($entityInfo['rule_id_field'] . ' IN (?) AND ', $ruleIds) .
                    $adapter->quoteInto($entityInfo['entity_id_field'] . ' NOT IN (?)', $entityIds)
                );
            }

            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Unbind specified rules from entities
     *
     * @param array|int|string $ruleIds
     * @param array|int|string $entityIds
     * @param string $entityType
     *
     * @return Mage_Rule_Model_Resource_Abstract
     */
    public function unbindRuleFromEntity($ruleIds, $entityIds, $entityType)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        if (!is_array($entityIds)) {
            $entityIds = [(int)$entityIds];
        }
        if (!is_array($ruleIds)) {
            $ruleIds = [(int)$ruleIds];
        }

        $where = [];
        if (!empty($ruleIds)) {
            $where[] = $writeAdapter->quoteInto($entityInfo['rule_id_field'] . ' IN (?)', $ruleIds);
        }
        if (!empty($entityIds)) {
            $where[] = $writeAdapter->quoteInto($entityInfo['entity_id_field'] . ' IN (?)', $entityIds);
        }

        $writeAdapter->delete($this->getTable($entityInfo['associations_table']), implode(' AND ', $where));

        return $this;
    }

    /**
     * Retrieve rule's associated entity Ids by entity type
     *
     * @param int $ruleId
     * @param string $entityType
     *
     * @return array
     */
    public function getAssociatedEntityIds($ruleId, $entityType)
    {
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable($entityInfo['associations_table']), [$entityInfo['entity_id_field']])
            ->where($entityInfo['rule_id_field'] . ' = ?', $ruleId);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Retrieve website ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getWebsiteIds($ruleId)
    {
        return $this->getAssociatedEntityIds($ruleId, 'website');
    }

    /**
     * Retrieve customer group ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getCustomerGroupIds($ruleId)
    {
        return $this->getAssociatedEntityIds($ruleId, 'customer_group');
    }

    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of rule's associated entity by specified entity type
     *
     * @param string $entityType
     *
     * @return array
     */
    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        throw Mage::exception(
            'Mage_Core',
            Mage::helper('rule')->__('There is no information about associated entity type "%s".', $entityType)
        );
    }
}
