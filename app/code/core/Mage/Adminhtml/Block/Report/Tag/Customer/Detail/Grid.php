<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tags detail for customer report grid block
 *
 * @package    Mage_Adminhtml
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
            'index'     => 'original_name',
        ]);

        $this->addColumn('tag_name', [
            'header'    => Mage::helper('reports')->__('Tag Name'),
            'index'     => 'tag_name',
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible', [
                'header'    => Mage::helper('reports')->__('Visible In'),
                'index'     => 'stores',
                'type'      => 'store',
                'sortable'  => false,
            ]);

            $this->addColumn('added_in', [
                'header'    => Mage::helper('reports')->__('Submitted In'),
                'type'      => 'store',
            ]);
        }

        $this->addColumn('created_at', [
            'header'    => Mage::helper('reports')->__('Submitted On'),
            'type'      => 'datetime',
            'index'     => 'created_at',
        ]);

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportCustomerDetailCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportCustomerDetailExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
