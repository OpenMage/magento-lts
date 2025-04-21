<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Product attributes grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Prepare product attributes grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare product attributes grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('is_visible', [
            'header' => Mage::helper('catalog')->__('Visible'),
            'index' => 'is_visible_on_front',
            'type' => 'options',
            'options' => [
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ],
            'align' => 'center',
        ], 'frontend_label');

        $this->addColumnAfter('is_global', [
            'header' => Mage::helper('catalog')->__('Scope'),
            'index' => 'is_global',
            'type' => 'options',
            'options' => [
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE => Mage::helper('catalog')->__('Store View'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE => Mage::helper('catalog')->__('Website'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL => Mage::helper('catalog')->__('Global'),
            ],
            'align' => 'center',
        ], 'is_visible');

        $this->addColumnAfter('is_searchable', [
            'header' => Mage::helper('catalog')->__('Searchable'),
            'index' => 'is_searchable',
            'type' => 'options',
            'options' => [
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ],
            'align' => 'center',
        ], 'is_user_defined');

        $this->addColumnAfter('is_filterable', [
            'header' => Mage::helper('catalog')->__('Use in Layered Navigation'),
            'index' => 'is_filterable',
            'type' => 'options',
            'options' => [
                '1' => Mage::helper('catalog')->__('Filterable (with results)'),
                '2' => Mage::helper('catalog')->__('Filterable (no results)'),
                '0' => Mage::helper('catalog')->__('No'),
            ],
            'align' => 'center',
        ], 'is_searchable');

        $this->addColumnAfter('is_comparable', [
            'header' => Mage::helper('catalog')->__('Comparable'),
            'index' => 'is_comparable',
            'type' => 'options',
            'options' => [
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ],
            'align' => 'center',
        ], 'is_filterable');

        return $this;
    }
}
