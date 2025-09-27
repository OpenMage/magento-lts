<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Price indexer resource model
 *
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
