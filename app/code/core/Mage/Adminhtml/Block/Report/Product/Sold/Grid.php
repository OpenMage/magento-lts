<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Report Sold Products Grid Block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Product_Sold_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    /**
     * Sub report size
     *
     * @var int
     */
    protected $_subReportSize = 0;

    /**
     * Initialize Grid settings
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridProductsSold');
    }

    /**
     * Prepare collection object for grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()
            ->initReport('reports/product_sold_collection');
        return $this;
    }

    /**
     * Prepare Grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Product Name'),
            'index'     => 'order_items_name',
        ]);

        $this->addColumn('ordered_qty', [
            'header'    => Mage::helper('reports')->__('Quantity Ordered'),
            'width'     => '120px',
            'index'     => 'ordered_qty',
            'total'     => 'sum',
            'type'      => 'number',
        ]);

        $this->addExportType('*/*/exportSoldCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportSoldExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
