<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml bestsellers products report grid block
 *
 * @deprecated after 1.4.0.1
 */
class Mage_Adminhtml_Block_Report_Product_Ordered_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridOrderedProducts');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/product_ordered_collection');
        return $this;
    }

    /**
     * @inheritDoc
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
