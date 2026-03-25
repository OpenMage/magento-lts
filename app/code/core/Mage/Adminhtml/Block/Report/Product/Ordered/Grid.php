<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml bestsellers products report grid block
 *
 * @deprecated after 1.4.0.1
 */
class Mage_Adminhtml_Block_Report_Product_Ordered_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    protected string $_eventPrefix = 'adminhtml_report_product_ordered_grid';

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridOrderedProducts');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/product_ordered_collection');
        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Product Name'),
            'index'     => 'name',
        ]);

        $baseCurrencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('price', [
            'width'         => '120px',
            'type'          => 'currency',
            'currency_code' => $baseCurrencyCode,
            'rate'          => $this->getRate($baseCurrencyCode),
        ]);

        $this->addColumn('ordered_qty', [
            'header'    => Mage::helper('reports')->__('Quantity Ordered'),
            'width'     => '120px',
            'index'     => 'ordered_qty',
            'total'     => 'sum',
            'type'      => 'number',
        ]);

        $this->addExportType('*/*/exportOrderedCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportOrderedExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
