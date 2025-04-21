<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product website resource model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Status extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Product attribute cache
     *
     * @var array
     */
    protected $_productAttributes  = [];

    protected function _construct()
    {
        $this->_init('catalog/product_enabled_index', 'product_id');
    }

    /**
     * Retrieve product attribute (public method for status model)
     *
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getProductAttribute($attributeCode)
    {
        return $this->_getProductAttribute($attributeCode);
    }

    /**
     * Retrieve product attribute
     *
     * @param string|int|Mage_Core_Model_Config_Element $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected function _getProductAttribute($attribute)
    {
        if (empty($this->_productAttributes[$attribute])) {
            $this->_productAttributes[$attribute] =
                Mage::getSingleton('catalog/product')->getResource()->getAttribute($attribute);
        }
        return $this->_productAttributes[$attribute];
    }

    /**
     * Refresh enabled index cache
     *
     * @param int $productId
     * @param int $storeId
     * @return $this
     */
    public function refreshEnabledIndex($productId, $storeId)
    {
        if ($storeId == Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
            foreach (Mage::app()->getStores() as $store) {
                $this->refreshEnabledIndex($productId, $store->getId());
            }

            return $this;
        }

        Mage::getResourceSingleton('catalog/product')->refreshEnabledIndex($storeId, $productId);

        return $this;
    }

    /**
     * Update product status for store
     *
     * @param int $productId
     * @param int $storeId
     * @param int $value
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function updateProductStatus($productId, $storeId, $value)
    {
        $statusAttributeId  = $this->_getProductAttribute('status')->getId();
        $statusEntityTypeId = $this->_getProductAttribute('status')->getEntityTypeId();
        $statusTable        = $this->_getProductAttribute('status')->getBackend()->getTable();
        $refreshIndex       = true;
        $adapter            = $this->_getWriteAdapter();

        $data = new Varien_Object([
            'entity_type_id' => $statusEntityTypeId,
            'attribute_id'   => $statusAttributeId,
            'store_id'       => $storeId,
            'entity_id'      => $productId,
            'value'          => $value,
        ]);

        $data = $this->_prepareDataForTable($data, $statusTable);

        $select = $adapter->select()
            ->from($statusTable)
            ->where('attribute_id = :attribute_id')
            ->where('store_id     = :store_id')
            ->where('entity_id    = :product_id');

        $row = $adapter->fetchRow($select);

        if ($row) {
            if ($row['value'] == $value) {
                $refreshIndex = false;
            } else {
                $condition = ['value_id = ?' => $row['value_id']];
                $adapter->update($statusTable, $data, $condition);
            }
        } else {
            $adapter->insert($statusTable, $data);
        }

        if ($refreshIndex) {
            $this->refreshEnabledIndex($productId, $storeId);
        }

        return $this;
    }

    /**
     * Retrieve Product(s) status for store
     * Return array where key is a product_id, value - status
     *
     * @param array|int $productIds
     * @param int $storeId
     * @return array
     */
    public function getProductStatus($productIds, $storeId = null)
    {
        $statuses = [];

        $attribute      = $this->_getProductAttribute('status');
        $attributeTable = $attribute->getBackend()->getTable();
        $adapter        = $this->_getReadAdapter();

        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }

        if ($storeId === null || $storeId == Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
            $select = $adapter->select()
                ->from($attributeTable, ['entity_id', 'value'])
                ->where('entity_id IN (?)', $productIds)
                ->where('attribute_id = ?', $attribute->getAttributeId())
                ->where('store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);

            $rows = $adapter->fetchPairs($select);
        } else {
            $valueCheckSql = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');

            $select = $adapter->select()
                ->from(
                    ['t1' => $attributeTable],
                    ['entity_id' => 't1.entity_id', 'value' => $valueCheckSql],
                )
                ->joinLeft(
                    ['t2' => $attributeTable],
                    't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id = '
                        . (int) $storeId,
                    [''],
                )
                ->where('t1.store_id = ?', Mage_Core_Model_App::ADMIN_STORE_ID)
                ->where('t1.attribute_id = ?', $attribute->getAttributeId())
                ->where('t1.entity_id IN(?)', $productIds);
            $rows = $adapter->fetchPairs($select);
        }

        foreach ($productIds as $productId) {
            if (isset($rows[$productId])) {
                $statuses[$productId] = $rows[$productId];
            } else {
                $statuses[$productId] = -1;
            }
        }

        return $statuses;
    }
}
