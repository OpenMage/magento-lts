<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Tag report admin controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Report_TagController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return $this
     */
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

    /**
     * @return void
     */
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
     * @return void
     */
    public function exportCustomerCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_customer_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('tag_customer.csv', -1));
    }

    /**
     * Export customer's tags report to Excel XML format
     * @return void
     */
    public function exportCustomerExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_customer_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('tag_customer.xml', -1));
    }

    /**
     * @return void
     */
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
     * @return void
     */
    public function exportProductCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_product_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('tag_product.csv', -1));
    }

    /**
     * Export product's tags report to Excel XML format
     * @return void
     */
    public function exportProductExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_product_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('tag_product.xml', -1));
    }

    /**
     * @return void
     */
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
     * @return void
     */
    public function exportPopularCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_popular_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('tag_popular.csv', -1));
    }

    /**
     * Export popular tags report to Excel XML format
     * @return void
     */
    public function exportPopularExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_popular_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('tag_popular.xml', -1));
    }

    /**
     * @return void
     */
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
     * @return void
     */
    public function exportCustomerDetailCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_customer_detail_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('tag_customer_detail.csv', -1));
    }

    /**
     * Export customer's tags detail report to Excel XML format
     * @return void
     */
    public function exportCustomerDetailExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_customer_detail_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('tag_customer_detail.xml', -1));
    }

    /**
     * @return void
     */
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
     * @return void
     */
    public function exportProductDetailCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_product_detail_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('tag_product_detail.csv', -1));
    }

    /**
     * Export product's tags detail report to Excel XML format
     * @return void
     */
    public function exportProductDetailExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_product_detail_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('tag_product_detail.xml', -1));
    }

    /**
     * @return void
     */
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
     * @return void
     */
    public function exportTagDetailCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_popular_detail_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('tag_detail.csv', -1));
    }

    /**
     * Export tag detail report to Excel XML format
     * @return void
     */
    public function exportTagDetailExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_tag_popular_detail_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('tag_detail.xml', -1));
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _isAllowed(): bool
    {
        $action = strtolower($this->getRequest()->getActionName());
        $aclPath = match ($action) {
            'customer', 'exportcustomercsv', 'exportcustomerexcel',
            'customerdetail', 'exportcustomerdetailcsv', 'exportcustomerdetailexcel' => 'report/tags/customer',
            'productall', 'product', 'exportproductcsv', 'exportproductexcel',
            'productdetail', 'exportproductdetailcsv', 'exportproductdetailexcel' => 'report/tags/product',
            'popular', 'exportpopularcsv', 'exportpopularexcel',
            'tagdetail', 'exporttagdetailcsv', 'exporttagdetailexcel' => 'report/tags/popular',
            default => 'report/tags',
        };

        return Mage::getSingleton('admin/session')->isAllowed($aclPath);
    }
}
