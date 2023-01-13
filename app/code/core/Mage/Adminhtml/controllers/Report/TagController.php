<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag report admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Report_TagController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if (!$act) {
            $act = 'default';
        }

        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Tag'), Mage::helper('reports')->__('Tag'));
        return $this;
    }

    public function customerAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Tags'))
             ->_title($this->__('Customers'));

        $this->_initAction()
            ->_setActiveMenu('report/tag/customer')
            ->_addBreadcrumb(Mage::helper('reports')->__('Customers Report'), Mage::helper('reports')->__('Customers Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_tag_customer'))
            ->renderLayout();
    }

    /**
     * Export customer's tags report to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $fileName   = 'tag_customer.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_customer_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customer's tags report to Excel XML format
     */
    public function exportCustomerExcelAction()
    {
        $fileName   = 'tag_customer.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_customer_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function productAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Tags'))
             ->_title($this->__('Products'));

        $this->_initAction()
            ->_setActiveMenu('report/tag/product')
            ->_addBreadcrumb(Mage::helper('reports')->__('Poducts Report'), Mage::helper('reports')->__('Products Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_tag_product'))
            ->renderLayout();
    }

    /**
     * Export product's tags report to CSV format
     */
    public function exportProductCsvAction()
    {
        $fileName   = 'tag_product.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_product_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export product's tags report to Excel XML format
     */
    public function exportProductExcelAction()
    {
        $fileName   = 'tag_product.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_product_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function popularAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Tags'))
             ->_title($this->__('Popular'));

        $this->_initAction()
            ->_setActiveMenu('report/tag/popular')
            ->_addBreadcrumb(Mage::helper('reports')->__('Popular Tags'), Mage::helper('reports')->__('Popular Tags'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_tag_popular'))
            ->renderLayout();
    }

    /**
     * Export popular tags report to CSV format
     */
    public function exportPopularCsvAction()
    {
        $fileName   = 'tag_popular.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_popular_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export popular tags report to Excel XML format
     */
    public function exportPopularExcelAction()
    {
        $fileName   = 'tag_popular.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_popular_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function customerDetailAction()
    {
        $detailBlock = $this->getLayout()->createBlock('adminhtml/report_tag_customer_detail');

        $this->_title($this->__('Reports'))
             ->_title($this->__('Tags'))
             ->_title($this->__('Customers'))
             ->_title($detailBlock->getHeaderText());

        $this->_initAction()
            ->_setActiveMenu('report/tag/customerDetail')
            ->_addBreadcrumb(Mage::helper('reports')->__('Customers Report'), Mage::helper('reports')->__('Customers Report'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Customer Tags'), Mage::helper('reports')->__('Customer Tags'))
            ->_addContent($detailBlock)
            ->renderLayout();
    }

    /**
     * Export customer's tags detail report to CSV format
     */
    public function exportCustomerDetailCsvAction()
    {
        $fileName   = 'tag_customer_detail.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_customer_detail_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customer's tags detail report to Excel XML format
     */
    public function exportCustomerDetailExcelAction()
    {
        $fileName   = 'tag_customer_detail.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_customer_detail_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function productDetailAction()
    {
        $detailBlock = $this->getLayout()->createBlock('adminhtml/report_tag_product_detail');

        $this->_title($this->__('Reports'))
             ->_title($this->__('Tags'))
             ->_title($this->__('Products'))
             ->_title($detailBlock->getHeaderText());

        $this->_initAction()
            ->_setActiveMenu('report/tag/productDetail')
            ->_addBreadcrumb(Mage::helper('reports')->__('Products Report'), Mage::helper('reports')->__('Products Report'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Product Tags'), Mage::helper('reports')->__('Product Tags'))
            ->_addContent($detailBlock)
            ->renderLayout();
    }

    /**
     * Export product's tags detail report to CSV format
     */
    public function exportProductDetailCsvAction()
    {
        $fileName   = 'tag_product_detail.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_product_detail_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export product's tags detail report to Excel XML format
     */
    public function exportProductDetailExcelAction()
    {
        $fileName   = 'tag_product_detail.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_product_detail_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function tagDetailAction()
    {
        $detailBlock = $this->getLayout()->createBlock('adminhtml/report_tag_popular_detail');

        $this->_title($this->__('Reports'))
             ->_title($this->__('Tags'))
             ->_title($this->__('Popular'))
             ->_title($detailBlock->getHeaderText());

        $this->_initAction()
            ->_setActiveMenu('report/tag/tagDetail')
            ->_addBreadcrumb(Mage::helper('reports')->__('Popular Tags'), Mage::helper('reports')->__('Popular Tags'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Tag Detail'), Mage::helper('reports')->__('Tag Detail'))
            ->_addContent($detailBlock)
            ->renderLayout();
    }

    /**
     * Export tag detail report to CSV format
     */
    public function exportTagDetailCsvAction()
    {
        $fileName   = 'tag_detail.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_popular_detail_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export tag detail report to Excel XML format
     */
    public function exportTagDetailExcelAction()
    {
        $fileName   = 'tag_detail.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_tag_popular_detail_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'customer':
                return Mage::getSingleton('admin/session')->isAllowed('report/tags/customer');
            case 'productall':
            case 'product':
                return Mage::getSingleton('admin/session')->isAllowed('report/tags/product');
            case 'popular':
                return Mage::getSingleton('admin/session')->isAllowed('report/tags/popular');
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/tags');
        }
    }
}
