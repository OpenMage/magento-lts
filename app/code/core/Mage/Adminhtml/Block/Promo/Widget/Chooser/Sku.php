<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create search products block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method Mage_Catalog_Model_Resource_Product_Collection getCollection()
 */
class Mage_Adminhtml_Block_Promo_Widget_Chooser_Sku extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Mage_Adminhtml_Block_Promo_Widget_Chooser_Sku constructor.
     * @param array $arguments
     * @throws Exception
     */
    public function __construct($arguments = [])
    {
        parent::__construct($arguments);

        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('skuChooserGrid_' . $this->getId());
        }

        $form = $this->getJsFormObject();
        $this->setRowClickCallback("$form.chooserGridRowClick.bind($form)");
        $this->setCheckboxCheckCallback("$form.chooserGridCheckboxCheck.bind($form)");
        $this->setRowInitCallback("$form.chooserGridRowInit.bind($form)");
        $this->setDefaultSort('sku');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Retrieve quote store object
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() === 'in_products') {
            $selected = $this->_getSelectedProducts();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('sku', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('sku', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare Catalog Product Collection for attribute SKU in Promo Conditions SKU chooser
     *
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId(0)
            ->addAttributeToSelect(['name', 'type_id', 'attribute_set_id']);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Define Cooser Grid Columns and filters
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', [
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'sku',
            'use_index' => true,
        ]);

        $this->addColumn('entity_id', [
            'header'    => Mage::helper('sales')->__('ID'),
            'index'     => 'entity_id'
        ]);

        $this->addColumn(
            'type',
            [
                'header' => Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ]
        );

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
            ]
        );

        $this->addColumn('chooser_sku', [
            'header'    => Mage::helper('sales')->__('SKU'),
            'name'      => 'chooser_sku',
            'width'     => '80px',
            'index'     => 'sku'
        ]);
        $this->addColumn('chooser_name', [
            'header'    => Mage::helper('sales')->__('Product Name'),
            'name'      => 'chooser_name',
            'index'     => 'name'
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/chooser', [
            '_current'          => true,
            'current_grid_id'   => $this->getId(),
            'collapse'          => null
        ]);
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function _getSelectedProducts()
    {
        return $this->getRequest()->getPost('selected', []);
    }
}
