<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Product reports admin controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Report_ProductController extends Mage_Adminhtml_Controller_Report_Abstract
{
    /**
     * Add report/products breadcrumbs
     *
     * @return $this
     */
    #[Override]
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('reports')->__('Products'), Mage::helper('reports')->__('Products'));
        return $this;
    }

    /**
     * Bestsellers
     */
    #[Deprecated(since: OpenMageVersionInterface::VERSION_1_4_0_1)]
    public function orderedAction()
    {
        return $this->_forward('bestsellers', 'report_sales');
    }

    /**
     * Export products bestsellers report to CSV format
     */
    #[Deprecated(since: OpenMageVersionInterface::VERSION_1_4_0_1)]
    public function exportOrderedCsvAction()
    {
        return $this->_forward('exportBestsellersCsv', 'report_sales');
    }

    /**
     * Export products bestsellers report to XML format
     */
    #[Deprecated(since: OpenMageVersionInterface::VERSION_1_4_0_1)]
    public function exportOrderedExcelAction()
    {
        return $this->_forward('exportBestsellersExcel', 'report_sales');
    }

    /**
     * Sold Products Report Action
     * @return void
     */
    public function soldAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Products Ordered'));

        $this->_initAction()
            ->_setActiveMenu('report/products/sold')
            ->_addBreadcrumb(Mage::helper('reports')->__('Products Ordered'), Mage::helper('reports')->__('Products Ordered'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_sold'))
            ->renderLayout();
    }

    /**
     * Export Sold Products report to CSV format action
     * @return void
     */
    public function exportSoldCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_product_sold_grid');
        $this->_prepareDownloadResponse(...$grid->getCsv('products_ordered.csv', -1));
    }

    /**
     * Export Sold Products report to XML format action
     * @return void
     */
    public function exportSoldExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_product_sold_grid');
        $this->_prepareDownloadResponse(...$grid->getExcel('products_ordered.xml', -1));
    }

    /**
     * Most viewed products
     * @return void
     */
    public function viewedAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Products'))->_title($this->__('Most Viewed'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE, 'viewed');

        $this->_initAction()
            ->_setActiveMenu('report/products/viewed')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Products Most Viewed Report'), Mage::helper('adminhtml')->__('Products Most Viewed Report'));

        $gridBlock = $this->getLayout()->getBlock('report_product_viewed.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
            $gridBlock,
            $filterFormBlock,
        ]);

        $this->renderLayout();
    }

    /**
     * Export products most viewed report to CSV format
     * @return void
     */
    public function exportViewedCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_product_viewed_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getCsvFile('products_mostviewed.csv', -1));
    }

    /**
     * Export products most viewed report to XML format
     * @return void
     */
    public function exportViewedExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_product_viewed_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse(...$grid->getExcelFile('products_mostviewed.xml', -1));
    }

    /**
     * Low stock action
     * @return void
     */
    public function lowstockAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Low Stock'));

        $this->_initAction()
            ->_setActiveMenu('report/products/lowstock')
            ->_addBreadcrumb(Mage::helper('reports')->__('Low Stock'), Mage::helper('reports')->__('Low Stock'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_lowstock'))
            ->renderLayout();
    }

    /**
     * Export low stock products report to CSV format
     * @return void
     */
    public function exportLowstockCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_product_lowstock_grid');
        $this->_prepareDownloadResponse(...$grid->getCsv('products_lowstock.csv', -1));
    }

    /**
     * Export low stock products report to XML format
     * @return void
     */
    public function exportLowstockExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_product_lowstock_grid');
        $this->_prepareDownloadResponse(...$grid->getExcel('products_lowstock.xml', -1));
    }

    /**
     * Downloads action
     * @return void
     */
    public function downloadsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Downloads'));

        $this->_initAction()
            ->_setActiveMenu('report/products/downloads')
            ->_addBreadcrumb(Mage::helper('reports')->__('Downloads'), Mage::helper('reports')->__('Downloads'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_downloads'))
            ->renderLayout();
    }

    /**
     * Export products downloads report to CSV format
     * @return void
     */
    public function exportDownloadsCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_product_downloads_grid');
        $this->_prepareDownloadResponse(...$grid->getCsv('products_downloads.csv', -1));
    }

    /**
     * Export products downloads report to XLS format
     * @return void
     */
    public function exportDownloadsExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_product_downloads_grid');
        $this->_prepareDownloadResponse(...$grid->getExcel('products_downloads.xml', -1));
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _isAllowed(): bool
    {
        $action = strtolower($this->getRequest()->getActionName());
        $aclPath = match ($action) {
            'viewed', 'exportviewedcsv', 'exportviewedexcel' => 'report/products/viewed',
            'sold', 'exportsoldcsv', 'exportsoldexcel' => 'report/products/sold',
            'lowstock', 'exportlowstockcsv', 'exportlowstockexcel' => 'report/products/lowstock',
            'downloads', 'exportdownloadscsv', 'exportdownloadsexcel' => 'report/products/downloads',
            'ordered', 'exportorderedcsv', 'exportorderedexcel' => 'report/products/bestsellers',
            default => 'report/products',
        };

        return Mage::getSingleton('admin/session')->isAllowed($aclPath);
    }
}
