<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping Cart reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Report_ShopcartController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Shopping Cart'), Mage::helper('reports')->__('Shopping Cart'));
        return $this;
    }

    public function customerAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Shopping Cart'))
             ->_title($this->__('Customer Shopping Carts'));

        $this->_initAction()
            ->_setActiveMenu('report/shopcart/customer')
            ->_addBreadcrumb(Mage::helper('reports')->__('Customers Report'), Mage::helper('reports')->__('Customers Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_shopcart_customer'))
            ->renderLayout();
    }

    /**
     * Export shopcart customer report to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_customer_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('shopcart_customer.csv', -1));
    }

    /**
     * Export shopcart customer report to Excel XML format
     */
    public function exportCustomerExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_customer_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('shopcart_customer.xml', -1));
    }

    public function productAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Shopping Cart'))
             ->_title($this->__('Products in Carts'));

        $this->_initAction()
            ->_setActiveMenu('report/shopcart/product')
            ->_addBreadcrumb(Mage::helper('reports')->__('Products Report'), Mage::helper('reports')->__('Products Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_shopcart_product'))
            ->renderLayout();
    }

    /**
     * Export products report grid to CSV format
     */
    public function exportProductCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_product_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('shopcart_product.csv', -1));
    }

    /**
     * Export products report to Excel XML format
     */
    public function exportProductExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_product_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('shopcart_product.xml', -1));
    }

    public function abandonedAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Shopping Cart'))
             ->_title($this->__('Abandoned Carts'));

        $this->_initAction()
            ->_setActiveMenu('report/shopcart/abandoned')
            ->_addBreadcrumb(Mage::helper('reports')->__('Abandoned Carts'), Mage::helper('reports')->__('Abandoned Carts'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_shopcart_abandoned'))
            ->renderLayout();
    }

    /**
     * Export abandoned carts report grid to CSV format
     */
    public function exportAbandonedCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_abandoned_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('shopcart_abandoned.csv', -1));
    }

    /**
     * Export abandoned carts report to Excel XML format
     */
    public function exportAbandonedExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_abandoned_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('shopcart_abandoned.xml', -1));
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'customer':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart/customer');
            case 'product':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart/product');
            case 'abandoned':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart/abandoned');
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart');
        }
    }
}
