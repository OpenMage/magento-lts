<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Report_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init
     *
     * @return Mage_Adminhtml_Report_ProductController
     */
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if(!$act)
            $act = 'default';
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Products'), Mage::helper('reports')->__('Products'));
        return $this;
    }

    /**
     * Bestsellers
     *
     */
    public function orderedAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/product/ordered')
            ->_addBreadcrumb(Mage::helper('reports')->__('Bestsellers'), Mage::helper('reports')->__('Bestsellers'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_ordered'))
            ->renderLayout();
    }

    /**
     * Export products bestsellers report to CSV format
     *
     */
    public function exportOrderedCsvAction()
    {
        $fileName   = 'products_bestsellers.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_ordered_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products bestsellers report to XML format
     *
     */
    public function exportOrderedExcelAction()
    {
        $fileName   = 'products_bestsellers.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_ordered_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Sold Products Report Action
     *
     */
    public function soldAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/product/sold')
            ->_addBreadcrumb(Mage::helper('reports')->__('Products Ordered'), Mage::helper('reports')->__('Products Ordered'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_sold'))
            ->renderLayout();
    }

    /**
     * Export Sold Products report to CSV format action
     *
     */
    public function exportSoldCsvAction()
    {
        $fileName   = 'products_ordered.csv';
        $content    = $this->getLayout()
            ->createBlock('adminhtml/report_product_sold_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Sold Products report to XML format action
     *
     */
    public function exportSoldExcelAction()
    {
        $fileName   = 'products_ordered.xml';
        $content    = $this->getLayout()
            ->createBlock('adminhtml/report_product_sold_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Most viewed products
     *
     */
    public function viewedAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/product/viewed')
            ->_addBreadcrumb(Mage::helper('reports')->__('Most viewed'), Mage::helper('reports')->__('Most viewed'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_viewed'))
            ->renderLayout();
    }

    /**
     * Export products most viewed report to CSV format
     *
     */
    public function exportViewedCsvAction()
    {
        $fileName   = 'products_mostviewed.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_viewed_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products most viewed report to XML format
     *
     */
    public function exportViewedExcelAction()
    {
        $fileName   = 'products_mostviewed.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_viewed_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Low stock action
     *
     */
    public function lowstockAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/product/lowstock')
            ->_addBreadcrumb(Mage::helper('reports')->__('Low stock'), Mage::helper('reports')->__('Low stock'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_lowstock'))
            ->renderLayout();
    }

    /**
     * Export low stock products report to CSV format
     *
     */
    public function exportLowstockCsvAction()
    {
        $fileName   = 'products_lowstock.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_lowstock_grid')
            ->setSaveParametersInSession(true)
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export low stock products report to XML format
     *
     */
    public function exportLowstockExcelAction()
    {
        $fileName   = 'products_lowstock.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_lowstock_grid')
            ->setSaveParametersInSession(true)
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Downloads action
     *
     */
    public function downloadsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/product/downloads')
            ->_addBreadcrumb(Mage::helper('reports')->__('Downloads'), Mage::helper('reports')->__('Downloads'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_downloads'))
            ->renderLayout();
    }

    /**
     * Export products downloads report to CSV format
     *
     */
    public function exportDownloadsCsvAction()
    {
        $fileName   = 'products_downloads.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_downloads_grid')
            ->setSaveParametersInSession(true)
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products downloads report to XLS format
     *
     */
    public function exportDownloadsExcelAction()
    {
        $fileName   = 'products_downloads.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_downloads_grid')
            ->setSaveParametersInSession(true)
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check is allowed for report
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'ordered':
                return Mage::getSingleton('admin/session')->isAllowed('report/products/ordered');
                break;
            case 'viewed':
                return Mage::getSingleton('admin/session')->isAllowed('report/products/viewed');
                break;
            case 'sold':
                return Mage::getSingleton('admin/session')->isAllowed('report/products/sold');
                break;
            case 'lowstock':
                return Mage::getSingleton('admin/session')->isAllowed('report/products/lowstock');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/products');
                break;
        }
    }
}
