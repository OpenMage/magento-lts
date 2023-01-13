<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Indexer resource model abstraction
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @property string $_attributeIdFieldName
 * @property string $_entityIdFieldName
 * @property string $_storeIdFieldName
 */
class Mage_CatalogIndex_Model_Resource_Indexer_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * should be defined because abstract
     *
     */
    protected function _construct()
    {
    }

    /**
     * @param array $data
     * @param int $storeId
     * @param int $productId
     * @return void
     */
    public function saveIndex($data, $storeId, $productId)
    {
        return $this->saveIndices([$data], $storeId, $productId);
    }

    /**
     * @param array $data
     * @param int $storeId
     * @param int $productId
     */
    public function saveIndices(array $data, $storeId, $productId)
    {
        $this->_executeReplace($data, $storeId, $productId);
    }

    /**
     * @param array $data
     * @param int $storeId
     * @param int $productId
     * @return $this
     */
    protected function _executeReplace($data, $storeId, $productId)
    {
        $this->beginTransaction();
        try {
            foreach ($data as $row) {
                $row[$this->_entityIdFieldName] = $productId;
                $this->_getWriteAdapter()->insert($this->getMainTable(), $row);
            }
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
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

        if (!is_null($attributeId)) {
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_attributeIdFieldName} = ?", $attributeId);
        }

        $conditions = implode(' AND ', $conditions);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $conditions);
    }

    /**
     * @param array|string $conditions
     * @return array
     */
    public function loadAttributeCodesByCondition($conditions)
    {
        $table = $this->getTable('eav/attribute');
        $select = $this->_getReadAdapter()->select();
        $select->from(['main_table' => $table], 'attribute_id')
            ->join(['additional_table' => $this->getTable('catalog/eav_attribute')], 'additional_table.attribute_id=main_table.attribute_id');
        $select->distinct(true);

        if (is_array($conditions)) {
            foreach ($conditions as $k => $condition) {
                if (is_array($condition)) {
                    if ($k == 'or') {
                        $function = 'where';
                        foreach ($condition as $field => $value) {
                            if (is_array($value)) {
                                $select->$function("{$field} in (?)", $value);
                            } else {
                                $select->$function("{$field} = ?", $value);
                            }

                            $function = 'orWhere';
                        }
                    } else {
                        $select->where("{$k} in (?)", $condition);
                    }
                } else {
                    $select->where("{$k} = ?", $condition);
                }
            }
        } else {
            $select->where($conditions);
        }
        return $this->_getReadAdapter()->fetchCol($select);
    }
}
