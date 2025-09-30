<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tags detail for product report grid block
 *
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
