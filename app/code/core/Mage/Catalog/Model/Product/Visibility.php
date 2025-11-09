<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product visibilite model and attribute source model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Visibility extends Varien_Object
{
    public const VISIBILITY_NOT_VISIBLE    = 1;

    public const VISIBILITY_IN_CATALOG     = 2;

    public const VISIBILITY_IN_SEARCH      = 3;

    public const VISIBILITY_BOTH           = 4;

    /**
     * Reference to the attribute instance
     *
     * @var Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected $_attribute;

    /**
     * Initialize object
     */
    public function __construct()
    {
        parent::__construct();
        $this->setIdFieldName('visibility_id');
    }

    /**
     * Add visible in catalog filter to collection
     *
     * @return $this
     * @deprecated
     */
    public function addVisibleInCatalogFilterToCollection(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        $collection->setVisibility($this->getVisibleInCatalogIds());
        return $this;
    }

    /**
     * Add visibility in searchfilter to collection
     *
     * @return $this
     * @deprecated
     */
    public function addVisibleInSearchFilterToCollection(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        $collection->setVisibility($this->getVisibleInSearchIds());
        return $this;
    }

    /**
     * Add visibility in site filter to collection
     *
     * @return $this
     * @deprecated
     */
    public function addVisibleInSiteFilterToCollection(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        $collection->setVisibility($this->getVisibleInSiteIds());
        return $this;
    }

    /**
     * Retrieve visible in catalog ids array
     *
     * @return array
     */
    public function getVisibleInCatalogIds()
    {
        return [self::VISIBILITY_IN_CATALOG, self::VISIBILITY_BOTH];
    }

    /**
     * Retrieve visible in search ids array
     *
     * @return array
     */
    public function getVisibleInSearchIds()
    {
        return [self::VISIBILITY_IN_SEARCH, self::VISIBILITY_BOTH];
    }

    /**
     * Retrieve visible in site ids array
     *
     * @return array
     */
    public function getVisibleInSiteIds()
    {
        return [self::VISIBILITY_IN_SEARCH, self::VISIBILITY_IN_CATALOG, self::VISIBILITY_BOTH];
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::VISIBILITY_NOT_VISIBLE => Mage::helper('catalog')->__('Not Visible Individually'),
            self::VISIBILITY_IN_CATALOG => Mage::helper('catalog')->__('Catalog'),
            self::VISIBILITY_IN_SEARCH  => Mage::helper('catalog')->__('Search'),
            self::VISIBILITY_BOTH       => Mage::helper('catalog')->__('Catalog, Search'),
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
     * Retrieve all options
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
     * Retireve all options
     *
     * @return array
     */
    public static function getAllOptions()
    {
        $res = [];
        $res[] = ['value' => '', 'label' => Mage::helper('catalog')->__('-- Please Select --')];
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = [
                'value' => $index,
                'label' => $value,
            ];
        }

        return $res;
    }

    /**
     * Retrieve option text
     *
     * @param int $optionId
     * @return string
     */
    public static function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return $options[$optionId] ?? null;
    }

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
            $column['comment']  = 'Catalog Product Visibility ' . $attributeCode . ' column';
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
        return [];
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return null|Varien_Db_Select
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
     * @return Mage_Catalog_Model_Product_Visibility
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
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param string $dir direction
     * @return Mage_Catalog_Model_Product_Visibility
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
