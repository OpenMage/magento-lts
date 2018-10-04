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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV attribute resource collection
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Add attribute set info flag
     *
     * @var boolean
     */
    protected $_addSetInfoFlag   = false;

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
        return array(
            'attribute_id',
            'entity_type_id',
            'attribute_code',
            'attribute_model',
            'backend_model',
            'backend_type',
            'backend_table',
            'frontend_input',
            'source_model',
        );
    }

    /**
     * Specify select columns which are used for load arrtibute values
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
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
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
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
                array('additional_table' => $additionalTable),
                'additional_table.attribute_id = main_table.attribute_id'
            );
        }

        return $this;
    }

    /**
     * Specify attribute set filter
     *
     * @param int $setId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function setAttributeSetFilter($setId)
    {
        if (is_array($setId)) {
            if (!empty($setId)) {
                $this->join(
                    'entity_attribute',
                    'entity_attribute.attribute_id = main_table.attribute_id',
                    'attribute_id'
                );
                $this->addFieldToFilter('entity_attribute.attribute_set_id', array('in' => $setId));
                $this->addAttributeGrouping();
                $this->_useAnalyticFunction = true;
            }
        } elseif ($setId) {
            $this->join(
                'entity_attribute',
                'entity_attribute.attribute_id = main_table.attribute_id'
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
     * @param array $setIds
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function setAttributeSetsFilter(array $setIds)
    {
        $this->getSelect()->distinct(true);
        $this->join(
            array('entity_attribute' => $this->getTable('eav/entity_attribute')),
            'entity_attribute.attribute_id = main_table.attribute_id',
            'attribute_id'
        );
        $this->addFieldToFilter('entity_attribute.attribute_set_id', array('in' => $setIds));
        $this->setOrder('entity_attribute.sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Filter for selecting of attributes that is in all sets
     *
     * @param array $setIds
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
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
                array($alias => 'eav/entity_attribute'),
                $joinCondition,
                'attribute_id'
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
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function setAttributeSetExcludeFilter($setId)
    {
        $this->join(
            'entity_attribute',
            'entity_attribute.attribute_id = main_table.attribute_id'
        );
        $this->addFieldToFilter('entity_attribute.attribute_set_id', array('neq' => $setId));
        $this->setOrder('entity_attribute.sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Exclude attributes filter
     *
     * @param array $attributes
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function setAttributesExcludeFilter($attributes)
    {
        return $this->addFieldToFilter('main_table.attribute_id', array('nin' => $attributes));
    }

    /**
     * Filter by attribute group id
     *
     * @param int $groupId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function setAttributeGroupFilter($groupId)
    {
        $this->join(
            'entity_attribute',
            'entity_attribute.attribute_id = main_table.attribute_id'
        );
        $this->addFieldToFilter('entity_attribute.attribute_group_id', $groupId);
        $this->setOrder('entity_attribute.sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Declare group by attribute id condition for collection select
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function addAttributeGrouping()
    {
        $this->getSelect()->group('entity_attribute.attribute_id');
        return $this;
    }

    /**
     * Specify "is_unique" filter as true
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function addIsUniqueFilter()
    {
        return $this->addFieldToFilter('is_unique', array('gt' => 0));
    }

    /**
     * Specify "is_unique" filter as false
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function addIsNotUniqueFilter()
    {
        return $this->addFieldToFilter('is_unique', 0);
    }

    /**
     * Specify filter to select just attributes with options
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function addHasOptionsFilter()
    {
        $adapter = $this->getConnection();
        $orWhere = implode(' OR ', array(
            $adapter->quoteInto('(main_table.frontend_input = ? AND ao.option_id > 0)', 'select'),
            $adapter->quoteInto('(main_table.frontend_input <> ?)', 'select'),
            '(main_table.is_user_defined = 0)'
        ));

        $this->getSelect()
            ->joinLeft(
                array('ao' => $this->getTable('eav/attribute_option')),
                'ao.attribute_id = main_table.attribute_id',
                'option_id')
            ->group('main_table.attribute_id')
            ->where($orWhere);

        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Apply filter by attribute frontend input type
     *
     * @param string $frontendInputType
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function setFrontendInputTypeFilter($frontendInputType)
    {
        return $this->addFieldToFilter('frontend_input', $frontendInputType);
    }

    /**
     * Flag for adding information about attributes sets to result
     *
     * @param bool $flag
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function addSetInfo($flag = true)
    {
        $this->_addSetInfoFlag = (bool)$flag;
        return $this;
    }

    /**
     * Ad information about attribute sets to collection result data
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected function _addSetInfo()
    {
        if ($this->_addSetInfoFlag) {
            $attributeIds = array();
            foreach ($this->_data as &$dataItem) {
                $attributeIds[] = $dataItem['attribute_id'];
            }
            $attributeToSetInfo = array();

            $adapter = $this->getConnection();
            if (count($attributeIds) > 0) {
                $select = $adapter->select()
                    ->from(
                        array('entity' => $this->getTable('eav/entity_attribute')),
                        array('attribute_id', 'attribute_set_id', 'attribute_group_id', 'sort_order')
                    )
                    ->joinLeft(
                        array('group' => $this->getTable('eav/attribute_group')),
                        'entity.attribute_group_id = group.attribute_group_id',
                        array('group_sort_order' => 'sort_order')
                    )
                    ->where('attribute_id IN (?)', $attributeIds);
                $result = $adapter->fetchAll($select);

                foreach ($result as $row) {
                    $data = array(
                        'group_id'      => $row['attribute_group_id'],
                        'group_sort'    => $row['group_sort_order'],
                        'sort'          => $row['sort_order']
                    );
                    $attributeToSetInfo[$row['attribute_id']][$row['attribute_set_id']] = $data;
                }
            }

            foreach ($this->_data as &$attributeData) {
                $setInfo = array();
                if (isset($attributeToSetInfo[$attributeData['attribute_id']])) {
                    $setInfo = $attributeToSetInfo[$attributeData['attribute_id']];
                }

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
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function checkConfigurableProducts()
    {
        return $this;
    }

    /**
     * Specify collection attribute codes filter
     *
     * @param string || array $code
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function setCodeFilter($code)
    {
        if (empty($code)) {
            return $this;
        }
        if (!is_array($code)) {
            $code = array($code);
        }

        return $this->addFieldToFilter('attribute_code', array('in' => $code));
    }

    /**
     * Add store label to attribute by specified store id
     *
     * @param integer $storeId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function addStoreLabel($storeId)
    {
        $adapter        = $this->getConnection();
        $joinExpression = $adapter
            ->quoteInto('al.attribute_id = main_table.attribute_id AND al.store_id = ?', (int) $storeId);
        $this->getSelect()->joinLeft(
            array('al' => $this->getTable('eav/attribute_label')),
            $joinExpression,
            array('store_label' => $adapter->getIfNullSql('al.value', 'main_table.frontend_label'))
        );

        return $this;
    }
}
