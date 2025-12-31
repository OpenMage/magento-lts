<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Indexer resource model abstraction
 *
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Resource_Indexer_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @var string
     */
    protected $_attributeIdFieldName;

    /**
     * @var string
     */
    protected $_entityIdFieldName;

    /**
     * @var string
     */
    protected $_storeIdFieldName;

    /**
     * @inheritDoc
     */
    protected function _construct() {}

    /**
     * @param  array $data
     * @param  int   $storeId
     * @param  int   $productId
     * @return void
     */
    public function saveIndex($data, $storeId, $productId)
    {
        $this->saveIndices([$data], $storeId, $productId);
    }

    /**
     * @param int $storeId
     * @param int $productId
     */
    public function saveIndices(array $data, $storeId, $productId)
    {
        $this->_executeReplace($data, $storeId, $productId);
    }

    /**
     * @param  array $data
     * @param  int   $storeId
     * @param  int   $productId
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
        } catch (Exception $exception) {
            $this->rollBack();
            throw $exception;
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
     * @param  array|string $conditions
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
