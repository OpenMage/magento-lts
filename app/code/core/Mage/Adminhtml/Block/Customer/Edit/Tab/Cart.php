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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer orders grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Cart extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var string
     */
    protected $_parentTemplate;

    /**
     * Mage_Adminhtml_Block_Customer_Edit_Tab_Cart constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setUseAjax(true);
        $this->_parentTemplate = $this->getTemplate();
        $this->setTemplate('customer/tab/cart.phtml');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareGrid()
    {
        $this->setId('customer_cart_grid' . $this->getWebsiteId());
        return parent::_prepareGrid();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $customer = Mage::registry('current_customer');
        $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();

        $quote = Mage::getModel('sales/quote')
            ->setSharedStoreIds($storeIds)
            ->loadByCustomer($customer);

        if ($quote) {
            $collection = $quote->getItemsCollection(false);
        } else {
            $collection = new Varien_Data_Collection();
        }

        $collection->addFieldToFilter('parent_item_id', ['null' => true]);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_id', [
            'header'    => Mage::helper('catalog')->__('Product ID'),
            'index'     => 'product_id',
            'width'     => '100px',
        ]);

        $this->addColumn('name', [
            'header'    => Mage::helper('catalog')->__('Product Name'),
            'index'     => 'name',
            'renderer'  => 'adminhtml/customer_edit_tab_view_grid_renderer_item',
        ]);

        $this->addColumn('sku', [
            'header'    => Mage::helper('catalog')->__('SKU'),
            'index'     => 'sku',
            'width'     => '100px',
        ]);

        $this->addColumn('qty', [
            'header'    => Mage::helper('catalog')->__('Qty'),
            'index'     => 'qty',
            'type'      => 'number',
        ]);

        $this->addColumn('price', [
            'type'          => 'currency',
            'currency_code' => Mage_Directory_Helper_Data::getConfigCurrencyBase(),
        ]);

        $this->addColumn('total', [
            'header'        => Mage::helper('sales')->__('Total'),
            'index'         => 'row_total',
            'type'          => 'currency',
            'currency_code' => Mage_Directory_Helper_Data::getConfigCurrencyBase(),
        ]);

        $this->addColumn('action', [
            'type'      => 'action',
            'index'     => 'quote_item_id',
            'renderer'  => 'adminhtml/customer_grid_renderer_multiaction',
            'actions'   => [
                [
                    'caption'           => Mage::helper('customer')->__('Configure'),
                    'url'               => 'javascript:void(0)',
                    'process'           => 'configurable',
                    'control_object'    => $this->getJsObjectName() . 'cartControl',
                ],
                [
                    'caption'   => Mage::helper('customer')->__('Delete'),
                    'url'       => '#',
                    'onclick'   => 'return ' . $this->getJsObjectName() . 'cartControl.removeItem($item_id);',
                ],
            ],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Gets customer assigned to this block
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/cart', ['_current' => true, 'website_id' => $this->getWebsiteId()]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, ['_relative' => true]);
        return $this->fetchView($templateName);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', ['id' => $row->getProductId()]);
    }
}
