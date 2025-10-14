<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV attribute resource collection
 *
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute getResource()
 *
 * @method Mage_Eav_Model_Entity_Attribute getItemById(int $value)
 * @method Mage_Eav_Model_Entity_Attribute[] getItems()
 * @method Mage_Eav_Model_Entity_Attribute getFirstItem()
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Add attribute set info flag
     *
     * @var bool
     */
    protected $_addSetInfoFlag   = false;

    /**
     * Tracks if addStoreLabel has been called to avoid conflicts on duplicate calls
     *
     * @var bool|int
     */
    protected $_addedStoreLabelsFlag = false;

    /**
     * Resource model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('eav/entity_attribute');
    }

    /**
     * Return array of fields to load attribute values
     *
     * @return array
     */
    protected function _getLoadDataFields()
    {
        return [
            'attribute_id',
            'entity_type_id',
            'attribute_code',
            'attribute_model',
            'backend_model',
            'backend_type',
            'backend_table',
            'frontend_input',
            'source_model',
        ];
    }

    /**
     * Specify select columns which are used for load attribute values
     *
     * @return $this
     */
    public function useLoadDataFields()
    {
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $this->getSelect()->columns($this->_getLoadDataFields());

        return $this;
    }

    /**
     * Specify attribute entity type filter
     *
     * @param  Mage_Eav_Model_Entity_Type | int $type
     * @return $this
     */
    public function setEntityTypeFilter($type)
    {
        if ($type instanceof Mage_Eav_Model_Entity_Type) {
            $additionalTable = $type->getAdditionalAttributeTable();
            $id = $type->getId();
        } else {
            $additionalTable = $this->getResource()->getAdditionalAttributeTable($type);
            $id = $type;
        }

        $this->addFieldToFilter('main_table.entity_type_id', $id);
        if ($additionalTable) {
            $this->join(
                ['additional_table' => $additionalTable],
                'additional_table.attribute_id = main_table.attribute_id',
            );
        }

        return $this;
    }

    /**
     * Specify attribute set filter
     *
     * @param int $setId
     * @return $this
     */
    public function setAttributeSetFilter($setId)
    {
        if (is_array($setId)) {
            if (!empty($setId)) {
                $this->join(
                    'entity_attribute',
                    'entity_attribute.attribute_id = main_table.attribute_id',
                    'attribute_id',
                );
                $this->addFieldToFilter('entity_attribute.attribute_set_id', ['in' => $setId]);
                $this->addAttributeGrouping();
                $this->_useAnalyticFunction = true;
            }
        } elseif ($setId) {
            $this->join(
                'entity_attribute',
                'entity_attribute.attribute_id = main_table.attribute_id',
            );
            $this->addFieldToFilter('entity_attribute.attribute_set_id', $setId);
            $this->setOrder('entity_attribute.sort_order', self::SORT_ORDER_ASC);
        }

        return $this;
    }

    /**
     * Specify multiple attribute sets filter
     * Result will be ordered by sort_order
     *
     * @return $this
     */
    public function setAttributeSetsFilter(array $setIds)
    {
        $this->getSelect()->distinct(true);
        $this->join(
            ['entity_attribute' => $this->getTable('eav/entity_attribute')],
            'entity_attribute.attribute_id = main_table.attribute_id',
            'attribute_id',
        );
        $this->addFieldToFilter('entity_attribute.attribute_set_id', ['in' => $setIds]);
        $this->setOrder('entity_attribute.sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Filter for selecting of attributes that is in all sets
     *
     * @return $this
     */
    public function setInAllAttributeSetsFilter(array $setIds)
    {
        foreach ($setIds as $setId) {
            $setId = (int) $setId;
            if (!$setId) {
                continue;
            }

            $alias         = sprintf('entity_attribute_%d', $setId);
            $joinCondition = $this->getConnection()
                ->quoteInto("{$alias}.attribute_id = main_table.attribute_id AND {$alias}.attribute_set_id =?", $setId);
            $this->join(
                [$alias => 'eav/entity_attribute'],
                $joinCondition,
                'attribute_id',
            );
        }

        //$this->getSelect()->distinct(true);
        $this->setOrder('is_user_defined', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Add filter which exclude attributes assigned to attribute set
     *
     * @param int $setId
     * @return $this
     */
    public function setAttributeSetExcludeFilter($setId)
    {
        $this->join(
            'entity_attribute',
            'entity_attribute.attribute_id = main_table.attribute_id',
        );
        $this->addFieldToFilter('entity_attribute.attribute_set_id', ['neq' => $setId]);
        $this->setOrder('entity_attribute.sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Exclude attributes filter
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributesExcludeFilter($attributes)
    {
        return $this->addFieldToFilter('main_table.attribute_id', ['nin' => $attributes]);
    }

    /**
     * Filter by attribute group id
     *
     * @param int $groupId
     * @return $this
     */
    public function setAttributeGroupFilter($groupId)
    {
        $this->join(
            'entity_attribute',
            'entity_attribute.attribute_id = main_table.attribute_id',
        );
        $this->addFieldToFilter('entity_attribute.attribute_group_id', $groupId);
        $this->setOrder('entity_attribute.sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Declare group by attribute id condition for collection select
     *
     * @return $this
     */
    public function addAttributeGrouping()
    {
        $this->getSelect()->group('entity_attribute.attribute_id');
        return $this;
    }

    /**
     * Specify "is_unique" filter as true
     *
     * @return $this
     */
    public function addIsUniqueFilter()
    {
        return $this->addFieldToFilter('is_unique', ['gt' => 0]);
    }

    /**
     * Specify "is_unique" filter as false
     *
     * @return $this
     */
    public function addIsNotUniqueFilter()
    {
        return $this->addFieldToFilter('is_unique', 0);
    }

    /**
     * Specify filter to select just attributes with options
     *
     * @return $this
     */
    public function addHasOptionsFilter()
    {
        $adapter = $this->getConnection();
        $orWhere = implode(' OR ', [
            $adapter->quoteInto('(main_table.frontend_input = ? AND ao.option_id > 0)', 'select'),
            $adapter->quoteInto('(main_table.frontend_input <> ?)', 'select'),
            '(main_table.is_user_defined = 0)',
        ]);

        $this->getSelect()
            ->joinLeft(
                ['ao' => $this->getTable('eav/attribute_option')],
                'ao.attribute_id = main_table.attribute_id',
                'option_id',
            )
            ->group('main_table.attribute_id')
            ->where($orWhere);

        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Apply filter by attribute frontend input type
     *
     * @param string $frontendInputType
     * @return $this
     */
    public function setFrontendInputTypeFilter($frontendInputType)
    {
        return $this->addFieldToFilter('frontend_input', $frontendInputType);
    }

    /**
     * Flag for adding information about attributes sets to result
     *
     * @param bool $flag
     * @return $this
     */
    public function addSetInfo($flag = true)
    {
        $this->_addSetInfoFlag = (bool) $flag;
        return $this;
    }

    /**
     * Ad information about attribute sets to collection result data
     *
     * @return $this
     */
    protected function _addSetInfo()
    {
        if ($this->_addSetInfoFlag) {
            $attributeIds = [];
            foreach ($this->_data as &$dataItem) {
                $attributeIds[] = $dataItem['attribute_id'];
            }

            $attributeToSetInfo = [];

            $adapter = $this->getConnection();
            if ($attributeIds !== []) {
                $select = $adapter->select()
                    ->from(
                        ['entity' => $this->getTable('eav/entity_attribute')],
                        ['attribute_id', 'attribute_set_id', 'attribute_group_id', 'sort_order'],
                    )
                    ->joinLeft(
                        ['group' => $this->getTable('eav/attribute_group')],
                        'entity.attribute_group_id = group.attribute_group_id',
                        ['group_sort_order' => 'sort_order'],
                    )
                    ->where('attribute_id IN (?)', $attributeIds);
                $result = $adapter->fetchAll($select);

                foreach ($result as $row) {
                    $data = [
                        'group_id'      => $row['attribute_group_id'],
                        'group_sort'    => $row['group_sort_order'],
                        'sort'          => $row['sort_order'],
                    ];
                    $attributeToSetInfo[$row['attribute_id']][$row['attribute_set_id']] = $data;
                }
            }

            foreach ($this->_data as &$attributeData) {
                $setInfo = $attributeToSetInfo[$attributeData['attribute_id']] ?? [];
                $attributeData['attribute_set_info'] = $setInfo;
            }

            unset($attributeToSetInfo);
            unset($attributeIds);
        }

        return $this;
    }

    /**
     * Ad information about attribute sets to collection result data
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _afterLoadData()
    {
        $this->_addSetInfo();

        return parent::_afterLoadData();
    }

    /**
     * Load is used in configurable products flag
     * @deprecated
     *
     * @return $this
     */
    public function checkConfigurableProducts()
    {
        return $this;
    }

    /**
     * Specify collection attribute codes filter
     *
     * @param string | array $code
     * @return $this
     */
    public function setCodeFilter($code)
    {
        if (empty($code)) {
            return $this;
        }

        if (!is_array($code)) {
            $code = [$code];
        }

        return $this->addFieldToFilter('attribute_code', ['in' => $code]);
    }

    /**
     * Add store label to attribute by specified store id
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreLabel($storeId)
    {
        // if not called previously
        if ($this->_addedStoreLabelsFlag === false) {
            $adapter = $this->getConnection();
            $joinExpression = $adapter
                ->quoteInto('al.attribute_id = main_table.attribute_id AND al.store_id = ?', (int) $storeId);
            $this->getSelect()->joinLeft(
                ['al' => $this->getTable('eav/attribute_label')],
                $joinExpression,
                ['store_label' => $adapter->getIfNullSql('al.value', 'main_table.frontend_label')],
            );
            $this->_addedStoreLabelsFlag = $storeId;
        } elseif ($this->_addedStoreLabelsFlag !== $storeId) {
            // check that previous call $storeId matches current call
            throw new Exception('Cannot call addStoreLabel for different store views on the same collection');
        }

        return $this;
    }
}
