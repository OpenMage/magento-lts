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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product EAV additional attribute resource collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection extends Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
{
    /**
     * Resource model initialization
     */
    public function _construct()
    {
        $this->_init('catalog/resource_eav_attribute', 'eav/entity_attribute');
    }

    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()))
            ->where('main_table.entity_type_id=?', Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId())
            ->join(
                array('additional_table' => $this->getTable('catalog/eav_attribute')),
                'additional_table.attribute_id=main_table.attribute_id'
            );
        return $this;
    }

    /**
     * Specify attribute entity type filter
     *
     * @param   int $typeId
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }

    /**
     * Return array of fields to load attribute values
     *
     * @return array
     */
    protected function _getLoadDataFields()
    {
        $fields = array_merge(
            parent::_getLoadDataFields(),
            array(
                'additional_table.is_global',
                'additional_table.is_html_allowed_on_front',
                'additional_table.is_wysiwyg_enabled'
            )
        );

        return $fields;
    }

    /**
     * Remove price from attribute list
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function removePriceFilter()
    {
        $this->getSelect()->where('main_table.attribute_code <> ?', 'price');
        return $this;
    }

    /**
     * Specify "is_visible_in_advanced_search" filter
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function addDisplayInAdvancedSearchFilter()
    {
        $this->getSelect()->where('additional_table.is_visible_in_advanced_search = ?', 1);
        return $this;
    }

    /**
     * Specify "is_filterable" filter
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function addIsFilterableFilter()
    {
        $this->getSelect()->where('additional_table.is_filterable > ?', 0);
        return $this;
    }

    /**
     * Add filterable in search filter
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function addIsFilterableInSearchFilter()
    {
        $this->getSelect()->where('additional_table.is_filterable_in_search > ?', 0);
        return $this;
    }

    /**
     * Specify filter by "is_visible" field
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function addVisibleFilter()
    {
        $this->getSelect()->where('additional_table.is_visible = ?', 1);
        return $this;
    }

    /**
     * Specify "is_searchable" filter
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function addIsSearchableFilter()
    {
        $this->getSelect()->where('additional_table.is_searchable = ?', 1);
        return $this;
    }

    /**
     * Specify filter for attributes that have to be indexed using advanced index
     *
     * @param bool $addRequiredCodes
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function addToIndexFilter($addRequiredCodes = false)
    {
        $requiredCodesCondition = ($addRequiredCodes)
            ? $this->getConnection()->quoteInto(' OR main_table.attribute_code IN (?)', array('status', 'visibility'))
            : '';

        $this->getSelect()->where('(
            additional_table.is_searchable = 1 OR
            additional_table.is_visible_in_advanced_search = 1 OR
            additional_table.is_filterable > 0 OR
            additional_table.is_filterable_in_search = 1'.
            $requiredCodesCondition .
        ')');

        return $this;
    }

    /**
     * Specify filter for attributes used in quick search
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function addSearchableAttributeFilter()
    {
        $this->getSelect()->where(
            'additional_table.is_searchable = 1 OR '.
            $this->getConnection()->quoteInto('main_table.attribute_code IN (?)', array('status', 'visibility'))
        );

        return $this;
    }
}
