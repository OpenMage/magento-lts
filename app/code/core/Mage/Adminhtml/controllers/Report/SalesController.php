<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Sales report admin controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Report_SalesController extends Mage_Adminhtml_Controller_Report_Abstract
{
    /**
     * Add report/sales breadcrumbs
     *
     * @return $this
     */
    #[Override]
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('reports')->__('Sales'), Mage::helper('reports')->__('Sales'));
        return $this;
    }

    /**
     * @return void
     */
    public function salesAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_setActiveMenu('report/salesroot/sales')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_sales.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
            $gridBlock,
            $filterFormBlock,
        ]);

        $this->renderLayout();
    }

    /**
     * @return void
     */
    public function bestsellersAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Products'))->_title($this->__('Bestsellers'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_BESTSELLERS_FLAG_CODE, 'bestsellers');

        $this->_initAction()
            ->_setActiveMenu('report/products/bestsellers')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Products Bestsellers Report'), Mage::helper('adminhtml')->__('Products Bestsellers Report'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_bestsellers.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
            $gridBlock,
            $filterFormBlock,
        ]);

        $this->renderLayout();
    }

    /**
     * Export bestsellers report grid to CSV format
     * @return void
     */
    public function exportBestsellersCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_bestsellers_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getCsvFile('bestsellers.csv', -1));
    }

    /**
     * Export bestsellers report grid to Excel XML format
     * @return void
     */
    public function exportBestsellersExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_bestsellers_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getExcelFile('bestsellers.xml', -1));
    }

    /**
     * Retrieve array of collection names by code specified in request
     *
     * @return array<void>
     */
    #[Deprecated(since: OpenMageVersionInterface::VERSION_1_4_0_1)]
    protected function _getCollectionNames()
    {
        return [];
    }

    /**
     * Refresh statistics for last 25 hours
     */
    #[Deprecated(since: OpenMageVersionInterface::VERSION_1_4_0_1)]
    public function refreshRecentAction()
    {
        return $this->_forward('refreshRecent', 'report_statistics');
    }

    /**
     * Refresh statistics for all period
     */
    #[Deprecated(since: OpenMageVersionInterface::VERSION_1_4_0_1)]
    public function refreshLifetimeAction()
    {
        return $this->_forward('refreshLifetime', 'report_statistics');
    }

    /**
     * Export sales report grid to CSV format
     * @return void
     */
    public function exportSalesCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_sales_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getCsvFile('sales.csv', -1));
    }

    /**
     * Export sales report grid to Excel XML format
     * @return void
     */
    public function exportSalesExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_sales_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getExcelFile('sales.xml', -1));
    }

    /**
     * @return void
     */
    public function taxAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Tax'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_TAX_FLAG_CODE, 'tax');

        $this->_initAction()
            ->_setActiveMenu('report/salesroot/tax')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tax'), Mage::helper('adminhtml')->__('Tax'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_tax.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
            $gridBlock,
            $filterFormBlock,
        ]);

        $this->renderLayout();
    }

    /**
     * Export tax report grid to CSV format
     * @return void
     */
    public function exportTaxCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_tax_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getCsvFile('tax.csv', -1));
    }

    /**
     * Export tax report grid to Excel XML format
     * @return void
     */
    public function exportTaxExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_tax_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getExcelFile('tax.xml', -1));
    }

    /**
     * @return void
     */
    public function shippingAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Shipping'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_SHIPPING_FLAG_CODE, 'shipping');

        $this->_initAction()
            ->_setActiveMenu('report/salesroot/shipping')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Shipping'), Mage::helper('adminhtml')->__('Shipping'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_shipping.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
            $gridBlock,
            $filterFormBlock,
        ]);

        $this->renderLayout();
    }

    /**
     * Export shipping report grid to CSV format
     * @return void
     */
    public function exportShippingCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_shipping_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getCsvFile('shipping.csv', -1));
    }

    /**
     * Export shipping report grid to Excel XML format
     * @return void
     */
    public function exportShippingExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_shipping_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getExcelFile('shipping.xml', -1));
    }

    /**
     * @return void
     */
    public function invoicedAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Total Invoiced'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_INVOICE_FLAG_CODE, 'invoiced');

        $this->_initAction()
            ->_setActiveMenu('report/salesroot/invoiced')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Total Invoiced'), Mage::helper('adminhtml')->__('Total Invoiced'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_invoiced.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
            $gridBlock,
            $filterFormBlock,
        ]);

        $this->renderLayout();
    }

    /**
     * Export invoiced report grid to CSV format
     * @return void
     */
    public function exportInvoicedCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_invoiced_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getCsvFile('invoiced.csv', -1));
    }

    /**
     * Export invoiced report grid to Excel XML format
     * @return void
     */
    public function exportInvoicedExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_invoiced_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getExcelFile('invoiced.xml', -1));
    }

    /**
     * @return void
     */
    public function refundedAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Total Refunded'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_REFUNDED_FLAG_CODE, 'refunded');

        $this->_initAction()
            ->_setActiveMenu('report/salesroot/refunded')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Total Refunded'), Mage::helper('adminhtml')->__('Total Refunded'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_refunded.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
            $gridBlock,
            $filterFormBlock,
        ]);

        $this->renderLayout();
    }

    /**
     * Export refunded report grid to CSV format
     * @return void
     */
    public function exportRefundedCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_refunded_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getCsvFile('refunded.csv', -1));
    }

    /**
     * Export refunded report grid to Excel XML format
     * @return void
     */
    public function exportRefundedExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_refunded_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getExcelFile('refunded.xml', -1));
    }

    /**
     * @return void
     */
    public function couponsAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Coupons'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE, 'coupons');

        $this->_initAction()
            ->_setActiveMenu('report/salesroot/coupons')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Coupons'), Mage::helper('adminhtml')->__('Coupons'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_coupons.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
            $gridBlock,
            $filterFormBlock,
        ]);

        $this->renderLayout();
    }

    /**
     * Export coupons report grid to CSV format
     * @return void
     */
    public function exportCouponsCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_coupons_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getCsvFile('coupons.csv', -1));
    }

    /**
     * Export coupons report grid to Excel XML format
     * @return void
     */
    public function exportCouponsExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_sales_coupons_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getExcelFile('coupons.xml', -1));
    }

    #[Deprecated(since: OpenMageVersionInterface::VERSION_1_4_0_1)]
    public function refreshStatisticsAction()
    {
        return $this->_forward('index', 'report_statistics');
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _isAllowed(): bool
    {
        $action = strtolower($this->getRequest()->getActionName());
        $aclPath = match ($action) {
            'sales', 'exportsalescsv', 'exportsalesexcel' => 'report/salesroot/sales',
            'tax', 'exporttaxcsv', 'exporttaxexcel' => 'report/salesroot/tax',
            'shipping', 'exportshippingcsv', 'exportshippingexcel' => 'report/salesroot/shipping',
            'invoiced', 'exportinvoicedcsv', 'exportinvoicedexcel' => 'report/salesroot/invoiced',
            'refunded', 'exportrefundedcsv', 'exportrefundedexcel' => 'report/salesroot/refunded',
            'coupons', 'exportcouponscsv', 'exportcouponsexcel' => 'report/salesroot/coupons',
            'bestsellers', 'exportbestsellerscsv', 'exportbestsellersexcel' => 'report/products/bestsellers',
            default => 'report/salesroot',
        };

        return $this->_getSession()->isAllowed($aclPath);
    }
}
