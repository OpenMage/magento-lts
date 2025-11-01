<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Source_Table extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Default values for option cache
     *
     * @var array
     */
    protected $_optionsDefault = [];

    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty       Add empty option to array
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $storeId = $this->getAttribute()->getStoreId();
        if (!is_array($this->_options)) {
            $this->_options = [];
        }

        if (!is_array($this->_optionsDefault)) {
            $this->_optionsDefault = [];
        }

        if (!isset($this->_options[$storeId])) {
            $idPrefix = 'ATTRIBUTE_OPTIONS_ID_' . $this->getAttribute()->getId();
            $tags = array_merge(
                ['eav', Mage_Core_Model_Translate::CACHE_TAG],
                $this->getAttribute()->getCacheTags(),
            );
            $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setPositionOrder('asc')
                ->setAttributeFilter($this->getAttribute()->getId())
                ->setStoreFilter($this->getAttribute()->getStoreId())
                ->initCache(Mage::app()->getCache(), $idPrefix, $tags)
                ->load();
            $this->_options[$storeId]        = $collection->toOptionArray();
            $this->_optionsDefault[$storeId] = $collection->toOptionArray('default_value');
        }

        $options = ($defaultValues ? $this->_optionsDefault[$storeId] : $this->_options[$storeId]);
        if ($withEmpty) {
            array_unshift($options, ['label' => '', 'value' => '']);
        }

        return $options;
    }

    /**
     * Get a text for option value
     *
     * @param int|string $value
     * @return array|false|string
     */
    public function getOptionText($value)
    {
        $isMultiple = false;
        if ($value && strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        $options = $this->getAllOptions(false);

        if ($isMultiple) {
            $values = [];
            foreach ($options as $item) {
                if (in_array($item['value'], $value)) {
                    $values[] = $item['label'];
                }
            }

            return $values;
        }

        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }

        return false;
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param string $dir
     *
     * @return $this
     */
    public function addValueSortToCollection($collection, $dir = Varien_Db_Select::SQL_ASC)
    {
        $valueTable1    = $this->getAttribute()->getAttributeCode() . '_t1';
        $valueTable2    = $this->getAttribute()->getAttributeCode() . '_t2';
        $collection->getSelect()
            ->joinLeft(
                [$valueTable1 => $this->getAttribute()->getBackend()->getTable()],
                "e.entity_id={$valueTable1}.entity_id"
                . " AND {$valueTable1}.attribute_id='{$this->getAttribute()->getId()}'"
                . " AND {$valueTable1}.store_id=0",
                [],
            )
            ->joinLeft(
                [$valueTable2 => $this->getAttribute()->getBackend()->getTable()],
                "e.entity_id={$valueTable2}.entity_id"
                . " AND {$valueTable2}.attribute_id='{$this->getAttribute()->getId()}'"
                . " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
                [],
            );
        $valueExpr = $collection->getSelect()->getAdapter()
            ->getCheckSql("{$valueTable2}.value_id > 0", "{$valueTable2}.value", "{$valueTable1}.value");

        Mage::getResourceModel('eav/entity_attribute_option')
            ->addOptionValueToCollection($collection, $this->getAttribute(), $valueExpr);

        $collection->getSelect()
            ->order("{$this->getAttribute()->getAttributeCode()} {$dir}");

        return $this;
    }

    /**
     * Retrieve Column(s) for Flat
     *
     * @return array
     */
    public function getFlatColums()
    {
        $columns = [];
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $isMulti = $this->getAttribute()->getFrontend()->getInputType() == 'multiselect';

        if (Mage::helper('core')->useDbCompatibleMode()) {
            $columns[$attributeCode] = [
                'type'      => $isMulti ? 'text' : 'int',
                'unsigned'  => false,
                'is_null'   => true,
                'default'   => null,
                'extra'     => null,
            ];
            if (!$isMulti) {
                $columns[$attributeCode . '_value'] = [
                    'type'      => 'varchar(255)',
                    'unsigned'  => false,
                    'is_null'   => true,
                    'default'   => null,
                    'extra'     => null,
                ];
            }
        } else {
            $type = ($isMulti) ? Varien_Db_Ddl_Table::TYPE_TEXT : Varien_Db_Ddl_Table::TYPE_INTEGER;
            $columns[$attributeCode] = [
                'type'      => $type,
                'length'    => $isMulti ? '65535' : null,
                'unsigned'  => false,
                'nullable'   => true,
                'default'   => null,
                'extra'     => null,
                'comment'   => $attributeCode . ' column',
            ];
            if (!$isMulti) {
                $columns[$attributeCode . '_value'] = [
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => 255,
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                    'extra'     => null,
                    'comment'   => $attributeCode . ' column',
                ];
            }
        }

        return $columns;
    }

    /**
     * Retrieve Indexes for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = [];

        $index = sprintf('IDX_%s', strtoupper($this->getAttribute()->getAttributeCode()));
        $indexes[$index] = [
            'type'      => 'index',
            'fields'    => [$this->getAttribute()->getAttributeCode()],
        ];

        $sortable   = $this->getAttribute()->getUsedForSortBy();
        if ($sortable && $this->getAttribute()->getFrontend()->getInputType() != 'multiselect') {
            $index = sprintf('IDX_%s_VALUE', strtoupper($this->getAttribute()->getAttributeCode()));

            $indexes[$index] = [
                'type'      => 'index',
                'fields'    => [$this->getAttribute()->getAttributeCode() . '_value'],
            ];
        }

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return null|Varien_Db_Select
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute_option')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
