<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product status functionality model
 *
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Product_Status _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Status getResource()
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method int getVisibility()
 * @method $this setVisibility(int $value)
 */
class Mage_Catalog_Model_Product_Status extends Mage_Core_Model_Abstract
{
    public const STATUS_ENABLED    = 1;

    public const STATUS_DISABLED   = 2;

    /**
     * Reference to the attribute instance
     *
     * @var Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected $_attribute;

    protected function _construct()
    {
        $this->_init('catalog/product_status');
    }

    /**
     * Retrieve Product Attribute by code
     *
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getProductAttribute($attributeCode)
    {
        return $this->_getResource()->getProductAttribute($attributeCode);
    }

    /**
     * Add visible filter to Product Collection
     *
     * @deprecated remove on new builds
     * @return $this
     */
    public function addVisibleFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        return $this;
    }

    /**
     * Add saleable filter to Product Collection
     *
     * @deprecated remove on new builds
     * @return $this
     */
    public function addSaleableFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        return $this;
    }

    /**
     * Retrieve Visible Status Ids
     *
     * @return array
     */
    public function getVisibleStatusIds()
    {
        return [self::STATUS_ENABLED];
    }

    /**
     * Retrieve Saleable Status Ids
     * Default Product Enable status
     *
     * @return array
     */
    public function getSaleableStatusIds()
    {
        return [self::STATUS_ENABLED];
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::STATUS_ENABLED    => Mage::helper('catalog')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('catalog')->__('Disabled'),
        ];
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public static function toOptionArray()
    {
        return self::getOptionArray();
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array
     */
    public static function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, ['value' => '', 'label' => '']);
        return $options;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array
     */
    public static function getAllOptions()
    {
        $res = [
            [
                'value' => '',
                'label' => Mage::helper('catalog')->__('-- Please Select --'),
            ],
        ];
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = [
                'value' => $index,
                'label' => $value,
            ];
        }

        return $res;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public static function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return $options[$optionId] ?? null;
    }

    /**
     * Update status value for product
     *
     * @param   int $productId
     * @param   int $storeId
     * @param   int $value
     * @return  Mage_Catalog_Model_Product_Status
     */
    public function updateProductStatus($productId, $storeId, $value)
    {
        Mage::getSingleton('catalog/product_action')
            ->updateAttributes([$productId], ['status' => $value], $storeId);

        // add back compatibility event
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $status */
        $status = $this->_getResource()->getProductAttribute('status');
        if ($status->isScopeWebsite()) {
            $website = Mage::app()->getStore($storeId)->getWebsite();
            $stores  = $website->getStoreIds();
        } elseif ($status->isScopeStore()) {
            $stores = [$storeId];
        } else {
            $stores = array_keys(Mage::app()->getStores());
        }

        foreach ($stores as $storeId) {
            Mage::dispatchEvent('catalog_product_status_update', [
                'product_id'    => $productId,
                'store_id'      => $storeId,
                'status'        => $value,
            ]);
        }

        return $this;
    }

    /**
     * Retrieve Product(s) status for store
     * Return array where key is product, value - status
     *
     * @param int|array $productIds
     * @param int $storeId
     * @return array
     */
    public function getProductStatus($productIds, $storeId = null)
    {
        return $this->getResource()->getProductStatus($productIds, $storeId);
    }

    /**
     * ---------------- Eav Source methods for Flat data -----------------------
     */

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = [
            'unsigned'  => true,
            'default'   => null,
            'extra'     => null,
        ];

        if (Mage::helper('core')->useDbCompatibleMode()) {
            $column['type']     = 'tinyint';
            $column['is_null']  = true;
        } else {
            $column['type']     = Varien_Db_Ddl_Table::TYPE_SMALLINT;
            $column['nullable'] = true;
            $column['comment']  = 'Catalog Product Status ' . $attributeCode . ' column';
        }

        return [$attributeCode => $column];
    }

    /**
     * Retrieve Indexes for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = [];

        $index = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = [
            'type'      => 'index',
            'fields'    => [$this->getAttribute()->getAttributeCode()],
        ];

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceSingleton('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    /**
     * Set attribute instance
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Catalog_Model_Product_Status
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param string $dir direction
     * @return Mage_Catalog_Model_Product_Status
     * @throws Mage_Core_Exception
     */
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        $attributeCode  = $this->getAttribute()->getAttributeCode();
        $attributeId    = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();

        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $attributeCode . '_t';
            $collection->getSelect()
                ->joinLeft(
                    [$tableName => $attributeTable],
                    "e.entity_id={$tableName}.entity_id"
                        . " AND {$tableName}.attribute_id='{$attributeId}'"
                        . " AND {$tableName}.store_id='0'",
                    [],
                );
            $valueExpr = $tableName . '.value';
        } else {
            $valueTable1 = $attributeCode . '_t1';
            $valueTable2 = $attributeCode . '_t2';
            $collection->getSelect()
                ->joinLeft(
                    [$valueTable1 => $attributeTable],
                    "e.entity_id={$valueTable1}.entity_id"
                        . " AND {$valueTable1}.attribute_id='{$attributeId}'"
                        . " AND {$valueTable1}.store_id='0'",
                    [],
                )
                ->joinLeft(
                    [$valueTable2 => $attributeTable],
                    "e.entity_id={$valueTable2}.entity_id"
                        . " AND {$valueTable2}.attribute_id='{$attributeId}'"
                        . " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
                    [],
                );

            $valueExpr = $collection->getConnection()->getCheckSql(
                $valueTable2 . '.value_id > 0',
                $valueTable2 . '.value',
                $valueTable1 . '.value',
            );
        }

        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }
}
