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
 * Adminhtml tags detail for product report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Tag_Product_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/tag_product_collection');

        $collection->addTagedCount()
            ->addProductFilter($this->getRequest()->getParam('id'))
            ->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus())
            ->addStoresVisibility()
            ->setActiveFilter()
            ->addGroupByTag()
            ->setRelationId();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('tag_name', [
            'header'    => Mage::helper('reports')->__('Tag Name'),
            'index'     => 'tag_name',
        ]);

        $this->addColumn('taged', [
            'header'    => Mage::helper('reports')->__('Tag Use'),
            'index'     => 'taged',
            'align'     => 'right',
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible', [
                'header'    => Mage::helper('reports')->__('Visible In'),
                'sortable'  => false,
                'index'     => 'stores',
                'type'      => 'store',
            ]);
        }

        $this->addExportType('*/*/exportProductDetailCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportProductDetailExcel', Mage::helper('reports')->__('Excel XML'));

        $this->setFilterVisibility(false);

        return parent::_prepareColumns();
    }
}
