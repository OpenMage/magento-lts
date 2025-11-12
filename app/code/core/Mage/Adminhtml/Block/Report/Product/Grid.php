<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml products report grid block
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Reports_Model_Resource_Report_Collection getCollection()
 */
class Mage_Adminhtml_Block_Report_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productsReportGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Reports_Model_Resource_Product_Collection $collection */
        $collection = Mage::getResourceModel('reports/product_collection');
        $collection->getEntity()->setStore(0);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoadCollection()
    {
        $totalObj = new Mage_Reports_Model_Totals();
        $this->setTotals($totalObj->countTotals($this));

        return parent::_afterLoadCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'    => Mage::helper('reports')->__('ID'),
            'index'     => 'entity_id',
            'total'     => 'Total',
        ]);

        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Name'),
            'index'     => 'name',
        ]);

        $this->addColumn('viewed', [
            'header'    => Mage::helper('reports')->__('Number Viewed'),
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'viewed',
            'total'     => 'sum',
        ]);

        $this->addColumn('added', [
            'header'    => Mage::helper('reports')->__('Number Added'),
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'added',
            'total'     => 'sum',
        ]);

        $this->addColumn('purchased', [
            'header'    => Mage::helper('reports')->__('Number Purchased'),
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'purchased',
            'total'     => 'sum',
        ]);

        $this->addColumn('fulfilled', [
            'header'    => Mage::helper('reports')->__('Number Fulfilled'),
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'fulfilled',
            'total'     => 'sum',
        ]);

        $this->addColumn('revenue', [
            'header'    => Mage::helper('reports')->__('Revenue'),
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'revenue',
            'total'     => 'sum',
        ]);

        $this->setCountTotals(true);

        $this->addExportType('*/*/exportProductsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportProductsExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
