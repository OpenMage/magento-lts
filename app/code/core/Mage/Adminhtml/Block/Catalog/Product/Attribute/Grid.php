<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product attributes grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'sortable' => true,
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
            'sortable' => true,
            'index' => 'is_global',
            'type' => 'options',
            'options' => [
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE => Mage::helper('catalog')->__('Store View'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE => Mage::helper('catalog')->__('Website'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL => Mage::helper('catalog')->__('Global'),
            ],
            'align' => 'center',
        ], 'is_visible');

        $this->addColumn('is_searchable', [
            'header' => Mage::helper('catalog')->__('Searchable'),
            'sortable' => true,
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
            'sortable' => true,
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
            'sortable' => true,
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
