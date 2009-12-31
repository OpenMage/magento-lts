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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV attribute resource collection
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Mysql4_Entity_Attribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Add attribute set info flag
     *
     * @var boolean
     */
    protected $_addSetInfoFlag = false;

    /**
     * Resource model initialization
     */
    public function _construct()
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
        $fields = array(
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
        return $fields;
    }

    /**
     * Specify select columns which are used for load arrtibute values
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
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
     * @param   int $typeId
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        $this->getSelect()->where('main_table.entity_type_id=?', $typeId);
        if ($additionalTable = $this->getResource()->getAdditionalAttributeTable($typeId)) {
            $this->getSelect()->join(
                array('additional_table' => $this->getTable($additionalTable)),
                'additional_table.attribute_id=main_table.attribute_id'
            );
        }
        return $this;
    }

    /**
     * Specify attribute set filter
     *
     * @param   int $setId
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setAttributeSetFilter($setId)
    {
        if (is_array($setId)) {
            if (!empty($setId)) {
                $this->join('entity_attribute', 'entity_attribute.attribute_id=main_table.attribute_id', 'attribute_id');
                $this->getSelect()->where('entity_attribute.attribute_set_id IN(?)', $setId);
            }
        }
        elseif($setId) {
            $this->join('entity_attribute', 'entity_attribute.attribute_id=main_table.attribute_id', '*');
            $this->getSelect()->where('entity_attribute.attribute_set_id=?', $setId);
            $this->setOrder('sort_order', 'asc');
        }
        return $this;
    }

    /**
     * Specify multiple attribute sets filter
     * Result will be ordered by sort_order
     *
     * @param   array $setIds
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setAttributeSetsFilter(array $setIds)
    {
        $this->join('entity_attribute', 'entity_attribute.attribute_id=main_table.attribute_id', 'attribute_id');
        $this->getSelect()->distinct(true);
        $this->getSelect()->where('entity_attribute.attribute_set_id IN(?)', $setIds);
        $this->setOrder('sort_order', 'asc');
        return $this;
    }

    /**
     * Filter for selecting of attributes that is in all sets
     *
     * @param array $setIds
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setInAllAttributeSetsFilter(array $setIds)
    {
        foreach ($setIds as $setId) {
            $setId = (int) $setId;
            if (!$setId) {
                continue;
            }
            $this->getSelect()->join(array('entity_attribute_'.$setId=>$this->getTable('entity_attribute')), 'entity_attribute_' . $setId . '.attribute_id=main_table.attribute_id and entity_attribute_' . $setId . '.attribute_set_id=' . $setId, 'attribute_id');
        }

        $this->getSelect()->distinct(true);
        $this->setOrder('is_user_defined', 'asc');
        return $this;
    }

    /**
     * Add filter which exclude attributes assigned to attribute set
     *
     * @param   int $setId
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setAttributeSetExcludeFilter($setId)
    {
        $this->join('entity_attribute', 'entity_attribute.attribute_id=main_table.attribute_id', '*');
        $this->getSelect()->where('entity_attribute.attribute_set_id != ?', $setId);
        $this->setOrder('sort_order', 'asc');
        return $this;
    }

    /**
     * Exclude attributes filter
     *
     * @param   array $attributes
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setAttributesExcludeFilter($attributes)
    {
        $this->getSelect()->where('main_table.attribute_id NOT IN(?)', $attributes);
        return $this;
    }

    /**
     * Filter by attribute group id
     *
     * @param   int $groupId
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setAttributeGroupFilter($groupId)
    {
        $this->join('entity_attribute', 'entity_attribute.attribute_id=main_table.attribute_id', '*');
        $this->getSelect()->where('entity_attribute.attribute_group_id=?', $groupId);
        $this->setOrder('sort_order', 'asc');
        return $this;
    }

    /**
     * Declare group by attribute id condition for collection select
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addAttributeGrouping()
    {
        $this->getSelect()->group('entity_attribute.attribute_id');
        return $this;
    }

    /**
     * Specify "is_unique" filter as true
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addIsUniqueFilter()
    {
        $this->getSelect()->where('main_table.is_unique>0');
        return $this;
    }

    /**
     * Specify "is_unique" filter as false
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addIsNotUniqueFilter()
    {
        $this->getSelect()->where('main_table.is_unique=0');
        return $this;
    }

    /**
     * Specify filter to select just attributes with options
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addHasOptionsFilter()
    {
        $this->getSelect()
            ->joinLeft(
                array('ao'=>$this->getTable('eav/attribute_option')), 'ao.attribute_id = main_table.attribute_id', 'option_id'
            )
            ->group('main_table.attribute_id')
            ->where('(main_table.frontend_input = ? and option_id > 0) or (main_table.frontend_input <> ?) or (main_table.is_user_defined = 0)', 'select', 'select');

        return $this;
    }

    /**
     * Apply filter by attribute frontend input type
     *
     * @param string $frontendInputType
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setFrontendInputTypeFilter($frontendInputType)
    {
        $this->getSelect()
            ->where('main_table.frontend_input = ?', $frontendInputType);
        return $this;
    }

    /**
     * Flag for adding information about attributes sets to result
     *
     * @param   bool $flag
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addSetInfo($flag=true)
    {
        $this->_addSetInfoFlag = $flag;
        return $this;
    }

    /**
     * Ad information about attribute sets to collection result data
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected function _addSetInfo()
    {
        if ($this->_addSetInfoFlag) {
            $attributeIds = array();
            foreach ($this->_data as &$dataItem) {
                $attributeIds[] = $dataItem['attribute_id'];
            }
            $attributeToSetInfo = array();

            if (count($attributeIds) > 0) {
                $select = $this->getConnection()->select()
                    ->from(
                        array('entity' => $this->getTable('entity_attribute')),
                        array('attribute_id','attribute_set_id', 'attribute_group_id', 'sort_order')
                    )
                    ->joinLeft(
                        array('group' => $this->getTable('attribute_group')),
                        'entity.attribute_group_id=group.attribute_group_id',
                        array('group_sort_order' => 'sort_order')
                    )
                    ->where('attribute_id IN (?)', $attributeIds);
                $result = $this->getConnection()->fetchAll($select);

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
                if (isset($attributeToSetInfo[$attributeData['attribute_id']])) {
                    $setInfo = $attributeToSetInfo[$attributeData['attribute_id']];
                } else {
                    $setInfo = array();
                }

                $attributeData['attribute_set_info'] = $setInfo;
            }

            unset($attributeToSetInfo);
            unset($attributeIds);
        }
        return $this;
    }

    protected function _afterLoadData()
    {
        $this->_addSetInfo();

        return parent::_afterLoadData();
    }

    /**
     * Load is used in configurable products flag
     *
     * @deprecated
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function checkConfigurableProducts()
    {
        return $this;
    }

    /**
     * Specify collection attribute codes filter
     *
     * @param   string || array $code
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setCodeFilter($code)
    {
        if (empty($code)) {
            return $this;
        }
        if (!is_array($code)) {
            $code = array($code);
        }
        $this->getSelect()->where('main_table.attribute_code IN(?)', $code);
        return $this;
    }

    /**
     * Add store label to attribute by specified store id
     *
     * @param integer $storeId
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addStoreLabel($storeId)
    {
        $this->getSelect()->joinLeft(
            array('al' => $this->getTable('eav/attribute_label')),
            'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int) $storeId,
            array('store_label' => new Zend_Db_Expr('IFNULL(al.value, main_table.frontend_label)'))
        );
        return $this;
    }
}
