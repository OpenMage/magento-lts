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
 * Adminhtml product downloads report grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Product_Downloads_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('downloadsGrid');
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } elseif ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } elseif ($this->getRequest()->getParam('store')) {
            $storeId = (int)$this->getRequest()->getParam('store');
        } else {
            $storeId = '';
        }

        $collection = Mage::getResourceModel('reports/product_downloads_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addAttributeToFilter('type_id', [Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE])
            ->addSummary();

        if ($storeId) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Product Name'),
            'index'     => 'name'
        ]);

        $this->addColumn('link_title', [
            'header'    => Mage::helper('reports')->__('Link'),
            'index'     => 'link_title'
        ]);

        $this->addColumn('sku', [
            'header'    => Mage::helper('reports')->__('Product SKU'),
            'index'     => 'sku'
        ]);

        $this->addColumn('purchases', [
            'header'    => Mage::helper('reports')->__('Purchases'),
            'width'     => '215px',
            'filter'    => false,
            'index'     => 'purchases',
            'type'      => 'number',
            'renderer'  => 'adminhtml/report_product_downloads_renderer_purchases',
        ]);

        $this->addColumn('downloads', [
            'header'    => Mage::helper('reports')->__('Downloads'),
            'width'     => '215px',
            'filter'    => false,
            'index'     => 'downloads',
            'type'      => 'number'
        ]);

        $this->addExportType('*/*/exportDownloadsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportDownloadsExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
