<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product Flat resource model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Flat extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Store scope Id
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Store flag which defines if Catalog Product Flat Data has been initialized
     *
     * @var array
     */
    protected $_isBuilt                  = [];

    /**
     * Init connection and resource table
     */
    protected function _construct()
    {
        $this->_init('catalog/product_flat', 'entity_id');
        $this->_storeId = (int) Mage::app()->getStore()->getId();
    }

    /**
     * Retrieve store for resource model
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Set store for resource model
     *
     * @param mixed $store
     * @return $this
     */
    public function setStoreId($store)
    {
        if (is_int($store)) {
            $this->_storeId = $store;
        } else {
            $this->_storeId = (int) Mage::app()->getStore($store)->getId();
        }

        return $this;
    }

    /**
     * Retrieve Flat Table name
     *
     * @param mixed $store
     * @return string
     */
    public function getFlatTableName($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }

        return $this->getTable(['catalog/product_flat', $store]);
    }

    /**
     * Retrieve entity type id
     *
     * @return int
     */
    public function getTypeId()
    {
        return Mage::getSingleton('catalog/config')
            ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
            ->getEntityTypeId();
    }

    /**
     * Retrieve attribute columns for collection select
     *
     * @param string $attributeCode
     * @return null|array
     */
    public function getAttributeForSelect($attributeCode)
    {
        $describe = $this->_getReadAdapter()->describeTable($this->getFlatTableName());
        if (!isset($describe[$attributeCode])) {
            return null;
        }

        $columns = [$attributeCode => $attributeCode];

        $attributeIndex = sprintf('%s_value', $attributeCode);
        if (isset($describe[$attributeIndex])) {
            $columns[$attributeIndex] = $attributeIndex;
        }

        return $columns;
    }

    /**
     * Retrieve Attribute Sort column name
     *
     * @param string $attributeCode
     * @return null|string
     */
    public function getAttributeSortColumn($attributeCode)
    {
        $describe = $this->_getReadAdapter()->describeTable($this->getFlatTableName());
        if (!isset($describe[$attributeCode])) {
            return null;
        }

        $attributeIndex = sprintf('%s_value', $attributeCode);
        if (isset($describe[$attributeIndex])) {
            return $attributeIndex;
        }

        return $attributeCode;
    }

    /**
     * Retrieve Flat Table columns list
     *
     * @return array
     */
    public function getAllTableColumns()
    {
        $describe = $this->_getReadAdapter()->describeTable($this->getFlatTableName());
        return array_keys($describe);
    }

    /**
     * Check whether the attribute is a real field in entity table
     * Rewritten for EAV Collection
     *
     * @param int|Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     * @return bool
     */
    public function isAttributeStatic($attribute)
    {
        $attributeCode = null;
        if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Interface) {
            $attributeCode = $attribute->getAttributeCode();
        } elseif (is_string($attribute)) {
            $attributeCode = $attribute;
        } elseif (is_numeric($attribute)) {
            $attributeCode = $this->getAttribute($attribute)
                ->getAttributeCode();
        }

        if ($attributeCode) {
            $columns = $this->getAllTableColumns();
            if (in_array($attributeCode, $columns)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve entity id field name in entity table
     * Rewritten for EAV collection compatible
     *
     * @return string
     */
    public function getEntityIdField()
    {
        return $this->getIdFieldName();
    }

    /**
     * Retrieve attribute instance
     * Special for non static flat table
     *
     * @param mixed $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($attribute)
    {
        return Mage::getSingleton('catalog/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attribute);
    }

    /**
     * Retrieve main resource table name
     *
     * @return string
     */
    public function getMainTable()
    {
        return $this->getFlatTableName($this->getStoreId());
    }

    /**
     * Check if Catalog Product Flat Data has been initialized
     *
     * @param null|bool|int|\Mage_Core_Model_Store $storeView Store(id) for which the value is checked
     * @return bool
     */
    public function isBuilt($storeView = null)
    {
        if ($storeView === null) {
            $storeId = Mage::app()->getAnyStoreView()->getId();
        } elseif (is_int($storeView)) {
            $storeId = $storeView;
        } else {
            $storeId = Mage::app()->getStore($storeView)->getId();
        }

        if (!isset($this->_isBuilt[$storeId])) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getFlatTableName($storeId), 'entity_id')
                ->limit(1);
            try {
                $this->_isBuilt[$storeId] = (bool) $this->_getReadAdapter()->fetchOne($select);
            } catch (Exception) {
                $this->_isBuilt[$storeId] = false;
            }
        }

        return $this->_isBuilt[$storeId];
    }
}
