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
 * Adminhtml customer view wishlist block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View_Wishlist extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_view_wishlist_grid');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setEmptyText(Mage::helper('customer')->__("There are no items in customer's wishlist at the moment"));
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('wishlist/item')->getCollection()
            ->addCustomerIdFilter(Mage::registry('current_customer')->getId())
            ->addDaysInWishlist(true)
            ->addStoreData()
            ->setInStockFilter(true);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_id', [
            'header'    => Mage::helper('customer')->__('Product ID'),
            'index'     => 'product_id',
            'type'      => 'number',
            'width'     => '100px'
        ]);

        $this->addColumn('product_name', [
            'header'    => Mage::helper('customer')->__('Product Name'),
            'index'     => 'product_name',
            'renderer'  => 'adminhtml/customer_edit_tab_view_grid_renderer_item'
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store', [
                'header'    => Mage::helper('customer')->__('Added From'),
                'type'      => 'store',
            ]);
        }

        $this->addColumn('added_at', [
            'header'    => Mage::helper('customer')->__('Date Added'),
            'index'     => 'added_at',
            'type'      => 'date',
        ]);

        $this->addColumn('days', [
            'header'    => Mage::helper('customer')->__('Days in Wishlist'),
            'index'     => 'days_in_wishlist',
            'type'      => 'number',
            'width'     => '140px',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Get headers visibility
     *
     * @return bool
     */
    public function getHeadersVisibility()
    {
        return ($this->getCollection()->getSize() > 0);
    }

    /**
     * Get row url
     *
     * @param Mage_Wishlist_Model_Item $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', ['id' => $row->getProductId()]);
    }
}
