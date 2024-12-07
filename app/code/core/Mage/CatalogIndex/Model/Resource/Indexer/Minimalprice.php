<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Price indexer resource model
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Resource_Indexer_Minimalprice extends Mage_CatalogIndex_Model_Resource_Indexer_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogindex/minimal_price', 'index_id');

        $this->_entityIdFieldName   = 'entity_id';
        $this->_storeIdFieldName    = 'store_id';
    }

    /**
     * @param array $conditions
     * @return string
     */
    public function getMinimalValue($conditions)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getTable('catalogindex/price'), 'MIN(value)');
        foreach ($conditions as $field => $value) {
            $condition = "{$field} = ?";
            if (is_array($value)) {
                $condition = "{$field} in (?)";
            }

            $select->where($condition, $value);
        }

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @param int $attributeId
     */
    public function cleanup($productId, $storeId = null, $attributeId = null)
    {
        $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_entityIdFieldName} = ?", $productId);

        if (!is_null($storeId)) {
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_storeIdFieldName} = ?", $storeId);
        }

        $conditions = implode(' AND ', $conditions);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $conditions);
    }
}
