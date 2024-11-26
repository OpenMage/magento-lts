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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml assigned products grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method Mage_Catalog_Model_Resource_Product_Collection getCollection()
 */
class Mage_Adminhtml_Block_Tag_Assigned_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_currentTagModel;

    /**
     * Set grid params
     */
    public function __construct()
    {
        parent::__construct();
        $this->_currentTagModel = Mage::registry('current_tag');
        $this->setId('tag_assigned_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ($this->_getTagId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
    }

    /**
     * Tag ID getter
     *
     * @return int
     */
    protected function _getTagId()
    {
        return $this->_currentTagModel->getId();
    }

    /**
     * Store getter
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * Add filter to grid columns
     *
     * @param mixed $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() === 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif ($productIds) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Retrieve Products Collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            //->addAttributeToFilter('status', array(''))
            ->joinField(
                'qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );

        if ($store->getId()) {
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', [
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'field_name'        => 'in_products',
            'values'            => $this->_getSelectedProducts(),
            'align'             => 'center',
            'index'             => 'entity_id'
        ]);

        $this->addColumn(
            'entity_id',
            [
                'header' => Mage::helper('catalog')->__('ID'),
                'index' => 'entity_id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => Mage::helper('catalog')->__('Name'),
                'index' => 'name',
            ]
        );

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn(
                'custom_name',
                [
                    'header' => Mage::helper('catalog')->__('Name in %s', $this->escapeHtml($store->getName())),
                    'index' => 'custom_name',
                ]
            );
        }

        $this->addColumn(
            'type',
            [
                'header'    => Mage::helper('catalog')->__('Type'),
                'width'     => 100,
                'index'     => 'type_id',
                'type'      => 'options',
                'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ]
        );

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header'    => Mage::helper('catalog')->__('Attrib. Set Name'),
                'width'     => 100,
                'index'     => 'attribute_set_id',
                'type'      => 'options',
                'options'   => $sets,
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => Mage::helper('catalog')->__('SKU'),
                'width' => 80,
                'index' => 'sku',
            ]
        );

        $store = $this->_getStore();
        $this->addColumn(
            'price',
            [
                'type'          => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
            ]
        );

        $this->addColumn(
            'visibility',
            [
                'header'    => Mage::helper('catalog')->__('Visibility'),
                'width'     => 100,
                'index'     => 'visibility',
                'type'      => 'options',
                'options'   => Mage::getModel('catalog/product_visibility')->getOptionArray(),
            ]
        );

        $this->addColumn(
            'status',
            [
                'header'    => Mage::helper('catalog')->__('Status'),
                'width'     => 70,
                'index'     => 'status',
                'type'      => 'options',
                'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('assigned_products', null);
        if (!is_array($products)) {
            $products = $this->getRelatedProducts();
        }
        return $products;
    }

    /**
     * Retrieve Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/assignedGridOnly', ['_current' => true]);
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getRelatedProducts()
    {
        return $this->_currentTagModel
            ->setStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)
            ->getRelatedProductIds();
    }
}
