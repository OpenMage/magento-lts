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
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Indexer resource model abstraction
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * Enter description here ...
     *
     * @param unknown_type $data
     * @param unknown_type $storeId
     * @param unknown_type $productId
     * @return unknown
     */
    public function saveIndex($data, $storeId, $productId)
    {
        return $this->saveIndices(array($data), $storeId, $productId);
    }

    /**
     * Enter description here ...
     *
     * @param array $data
     * @param unknown_type $storeId
     * @param unknown_type $productId
     */
    public function saveIndices(array $data, $storeId, $productId)
    {
        $this->_executeReplace($data, $storeId, $productId);
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $data
     * @param unknown_type $storeId
     * @param unknown_type $productId
     * @return Mage_CatalogIndex_Model_Resource_Indexer_Abstract
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
     * Enter description here ...
     *
     * @param unknown_type $productId
     * @param unknown_type $storeId
     * @param unknown_type $attributeId
     */
    public function cleanup($productId, $storeId = null, $attributeId = null)
    {
        $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_entityIdFieldName} = ?", $productId);

        if (!is_null($storeId))
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_storeIdFieldName} = ?", $storeId);

        if (!is_null($attributeId))
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_attributeIdFieldName} = ?", $attributeId);

        $conditions = implode (' AND ', $conditions);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $conditions);
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $conditions
     * @return unknown
     */
    public function loadAttributeCodesByCondition($conditions)
    {
        $table = $this->getTable('eav/attribute');
        $select = $this->_getReadAdapter()->select();
        $select->from(array('main_table' => $table), 'attribute_id')
            ->join(array('additional_table' => $this->getTable('catalog/eav_attribute')), 'additional_table.attribute_id=main_table.attribute_id');
        $select->distinct(true);

        if (is_array($conditions)) {
            foreach ($conditions as $k=>$condition) {
                if (is_array($condition)) {
                    if ($k == 'or') {
                        $function = 'where';
                        foreach ($condition as $field=>$value) {
                            if (is_array($value))
                                $select->$function("{$field} in (?)", $value);
                            else
                                $select->$function("{$field} = ?", $value);

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
