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
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * Enter description here...
     *
     */
    public function _construct()
    {
        $this->_init('eav/entity_attribute');
    }

    /**
     * Enter description here...
     *
     * @param int $typeId
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        $this->getSelect()->where('main_table.entity_type_id=?', $typeId);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param int $setId
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
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
     * Enter description here...
     *
     * @param array $setIds
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
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
     * Enter description here...
     *
     * @param int $setId
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setAttributeSetExcludeFilter($setId)
    {
        $this->join('entity_attribute', 'entity_attribute.attribute_id=main_table.attribute_id', '*');
        $this->getSelect()->where('entity_attribute.attribute_set_id != ?', $setId);
        $this->setOrder('sort_order', 'asc');
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param int $attributes
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setAttributesExcludeFilter($attributes)
    {
        #$this->join('entity_attribute', 'entity_attribute.attribute_id=main_table.attribute_id', 'sort_order');
        $this->getSelect()->where('main_table.attribute_id NOT IN(?)', $attributes);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param int $groupId
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function setAttributeGroupFilter($groupId)
    {
        $this->join('entity_attribute', 'entity_attribute.attribute_id=main_table.attribute_id', '*');
        $this->getSelect()->where('entity_attribute.attribute_group_id=?', $groupId);
        $this->setOrder('sort_order', 'asc');
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addAttributeGrouping()
    {
        $this->getSelect()->group('entity_attribute.attribute_id');
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addVisibleFilter()
    {
        $this->getSelect()->where('main_table.is_visible=?', 1);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addIsFilterableFilter()
    {
        $this->getSelect()->where('main_table.is_filterable>0');
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addIsUniqueFilter()
    {
        $this->getSelect()->where('main_table.is_unique>0');
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addIsNotUniqueFilter()
    {
        $this->getSelect()->where('main_table.is_unique=0');
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addIsSearchableFilter()
    {
        $this->getSelect()->where('main_table.is_searchable=1');
        return $this;
    }

    /**
     * Enter description here...
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
     * Enter description here...
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function addDisplayInAdvancedSearchFilter(){
        $this->getSelect()
            ->where('main_table.is_visible_in_advanced_search = ?', 1);

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

    public function addSetInfo($flag=true)
    {
        $this->_addSetInfoFlag = $flag;
        return $this;
    }

    protected function _addSetInfo()
    {
        if ($this->_addSetInfoFlag) {
            $attributeIds = array_keys($this->_items);
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

            foreach ($this->getItems() as $attribute) {
                if (isset($attributeToSetInfo[$attribute->getId()])) {
                    $setInfo = $attributeToSetInfo[$attribute->getId()];
                } else {
                    $setInfo = array();
                }

                $attribute->setAttributeSetInfo($setInfo);
            }

            unset($attributeToSetInfo);
            unset($attributeIds);
        }
    }

    /**
     * TODO: issue #5126
     *
     * @return unknown
     */
    public function checkConfigurableProducts()
    {
// was:
/*
SELECT `main_table`.*, `entity_attribute`.*
FROM `eav_attribute` AS `main_table`
    INNER JOIN `eav_entity_attribute` AS `entity_attribute` ON entity_attribute.attribute_id=main_table.attribute_id
WHERE (entity_attribute.attribute_group_id='46') AND (main_table.is_visible=1)
*/
// to be done: left join catalog_product_super_attribute and count appropriate lines
/*
SELECT `main_table`.*, `entity_attribute`.*, COUNT(`super`.attribute_id) AS `is_used_in_configurable`
FROM `eav_attribute` AS `main_table`
    INNER JOIN `eav_entity_attribute` AS `entity_attribute` ON entity_attribute.attribute_id=main_table.attribute_id
    LEFT JOIN `catalog_product_super_attribute` AS `super` ON `main_table`.attribute_id=`super`.attribute_id
WHERE (entity_attribute.attribute_group_id='46') AND (main_table.is_visible=1)
GROUP BY `main_table`.attribute_id
*/
        return $this;
    }

    protected function _afterLoad()
    {
        $this->_addSetInfo();
        return parent::_afterLoad();
    }

}
