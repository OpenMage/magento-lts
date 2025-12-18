<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml most viewed products report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Product_Viewed_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    /**
     * Column for grid to be grouped by
     *
     * @var string
     */
    protected $_columnGroupBy = 'period';

    /**
     * Grid resource collection name
     *
     * @var string
     */
    protected $_resourceCollectionName  = 'reports/report_product_viewed_collection';

    /**
     * Init grid parameters
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
    }

    /**
     * Custom columns preparation
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('period', [
            'header'        => Mage::helper('adminhtml')->__('Period'),
            'index'         => 'period',
            'width'         => 100,
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'  => Mage::helper('adminhtml')->__('Total'),
            'html_decorators' => ['nobr'],
        ]);

        $this->addColumn('product_name', [
            'header'    => Mage::helper('adminhtml')->__('Product Name'),
            'index'     => 'product_name',
            'type'      => 'string',
            'sortable'  => false,
        ]);

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }

        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('product_price', [
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'product_price',
            'sortable'      => false,
            'rate'          => $this->getRate($currencyCode),
        ]);

        $this->addColumn('views_num', [
            'header'    => Mage::helper('adminhtml')->__('Number of Views'),
            'index'     => 'views_num',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false,
        ]);

        $this->addExportType('*/*/exportViewedCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportViewedExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Don't use orders in collection
     *
     * @param  Mage_Reports_Model_Resource_Report_Collection_Abstract $collection
     * @param  Varien_Object                                          $filterData
     * @return Mage_Adminhtml_Block_Report_Grid_Abstract
     */
    protected function _addOrderStatusFilter($collection, $filterData)
    {
        return $this;
    }
}
