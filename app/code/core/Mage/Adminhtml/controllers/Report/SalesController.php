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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales report admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Report_SalesController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if(!$act)
            $act = 'default';
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Sales'), Mage::helper('reports')->__('Sales'));
        return $this;
    }

    public function salesAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/sales/sales')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_sales_sales'))
            ->renderLayout();
    }

    /**
     * Export sales report grid to CSV format
     */
    public function exportSalesCsvAction()
    {
        $fileName   = 'sales.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_sales_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportSalesExcelAction()
    {
        $fileName   = 'sales.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_sales_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function taxAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/sales/tax')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tax'), Mage::helper('adminhtml')->__('Tax'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_sales_tax'))
            ->renderLayout();
    }

    /**
     * Export tax report grid to CSV format
     */
    public function exportTaxCsvAction()
    {
        $fileName   = 'tax.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_tax_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export tax report grid to Excel XML format
     */
    public function exportTaxExcelAction()
    {
        $fileName   = 'tax.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_tax_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function invoicedAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/sales/invoiced')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Total Invoiced'), Mage::helper('adminhtml')->__('Total Invoiced'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_sales_invoiced'))
            ->renderLayout();
    }

    /**
     * Export invoiced report grid to CSV format
     */
    public function exportInvoicedCsvAction()
    {
        $fileName   = 'invoiced.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_invoiced_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export invoiced report grid to Excel XML format
     */
    public function exportInvoicedExcelAction()
    {
        $fileName   = 'invoiced.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_invoiced_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function refundedAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/sales/refunded')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Total Refunded'), Mage::helper('adminhtml')->__('Total Refunded'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_sales_refunded'))
            ->renderLayout();
    }

    /**
     * Export refunded report grid to CSV format
     */
    public function exportRefundedCsvAction()
    {
        $fileName   = 'refunded.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_refunded_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export refunded report grid to Excel XML format
     */
    public function exportRefundedExcelAction()
    {
        $fileName   = 'refunded.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_refunded_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function couponsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/sales/coupons')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Coupons'), Mage::helper('adminhtml')->__('Coupons'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_sales_coupons'))
            ->renderLayout();
    }

    /**
     * Export coupons report grid to CSV format
     */
    public function exportCouponsCsvAction()
    {
        $fileName   = 'coupons.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_coupons_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export coupons report grid to Excel XML format
     */
    public function exportCouponsExcelAction()
    {
        $fileName   = 'coupons.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_coupons_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function shippingAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/sales/shipping')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Shipping'), Mage::helper('adminhtml')->__('Shipping'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_sales_shipping'))
            ->renderLayout();
    }

    /**
     * Export shipping report grid to CSV format
     */
    public function exportShippingCsvAction()
    {
        $fileName   = 'shipping.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_shipping_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export shipping report grid to Excel XML format
     */
    public function exportShippingExcelAction()
    {
        $fileName   = 'shipping.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_sales_shipping_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'sales':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/sales');
                break;
            case 'tax':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/tax');
                break;
            case 'shipping':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/shipping');
                break;
            case 'invoiced':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/invoiced');
                break;
            case 'refunded':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/refunded');
                break;
            case 'coupons':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/coupons');
                break;
            case 'shipping':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/shipping');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot');
                break;
        }
    }
}
