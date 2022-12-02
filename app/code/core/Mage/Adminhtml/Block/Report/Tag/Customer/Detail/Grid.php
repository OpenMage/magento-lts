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
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml tags detail for customer report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Tag_Customer_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customers_grid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tag/tag')
            ->getEntityCollection()
            ->joinAttribute('original_name', 'catalog_product/name', 'entity_id')
            ->addCustomerFilter($this->getRequest()->getParam('id'))
            ->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)
            ->addStoresVisibility()
            ->setActiveFilter()
            ->addGroupByTag()
            ->setRelationId();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Product Name'),
            'index'     => 'original_name'
        ]);

        $this->addColumn('tag_name', [
            'header'    => Mage::helper('reports')->__('Tag Name'),
            'index'     => 'tag_name'
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible', [
                'header'    => Mage::helper('reports')->__('Visible In'),
                'index'     => 'stores',
                'type'      => 'store',
                'sortable'  => false,
                'store_view' => true
            ]);

            $this->addColumn('added_in', [
                'header'    => Mage::helper('reports')->__('Submitted In'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view' => true
            ]);
        }

        $this->addColumn('created_at', [
            'header'    => Mage::helper('reports')->__('Submitted On'),
            'type'      => 'datetime',
            'index'     => 'created_at'
        ]);

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportCustomerDetailCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportCustomerDetailExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
