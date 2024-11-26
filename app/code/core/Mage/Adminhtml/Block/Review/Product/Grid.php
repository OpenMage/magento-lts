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
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml product grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Review_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reviewProductGrid');
        $this->setRowClickCallback('review.gridRowClick');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
                'header'    => Mage::helper('review')->__('ID'),
                'index'     => 'entity_id',
        ]);

        $this->addColumn('name', [
                'header'    => Mage::helper('review')->__('Name'),
                'index'     => 'name',
        ]);

        if ((int)$this->getRequest()->getParam('store', 0)) {
            $this->addColumn('custom_name', [
                    'header'    => Mage::helper('review')->__('Name in Store'),
                    'index'     => 'custom_name'
            ]);
        }

        $this->addColumn('sku', [
                'header'    => Mage::helper('review')->__('SKU'),
                'width'     => '80px',
                'index'     => 'sku'
        ]);

        $this->addColumn('price', [
                'type'      => 'currency',
        ]);

        $this->addColumn('qty', [
                'header'    => Mage::helper('review')->__('Qty'),
                'type'      => 'number',
                'index'     => 'qty'
        ]);

        $this->addColumn('status', [
                'header'    => Mage::helper('review')->__('Status'),
                'width'     => '90px',
                'index'     => 'status',
                'type'      => 'options',
                'source'    => 'catalog/product_status',
                'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ]);

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                [
                    'header' => Mage::helper('review')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                ]
            );
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', ['_current' => true]);
    }

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/jsonProductInfo', ['id' => $row->getId()]);
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }
}
