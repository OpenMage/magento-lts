<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Shopping Cart reports admin controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Report_ShopcartController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return $this
     */
    public function _initAction()
    {
        $this->getRequest()->getActionName();
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Shopping Cart'), Mage::helper('reports')->__('Shopping Cart'));
        return $this;
    }

    /**
     * @return void
     */
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
     * @return void
     */
    public function exportCustomerCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_customer_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('shopcart_customer.csv', -1));
    }

    /**
     * Export shopcart customer report to Excel XML format
     * @return void
     */
    public function exportCustomerExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_customer_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('shopcart_customer.xml', -1));
    }

    /**
     * @return void
     */
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
     * @return void
     */
    public function exportProductCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_product_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('shopcart_product.csv', -1));
    }

    /**
     * Export products report to Excel XML format
     * @return void
     */
    public function exportProductExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_product_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('shopcart_product.xml', -1));
    }

    /**
     * @return void
     */
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
     * @return void
     */
    public function exportAbandonedCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_abandoned_grid');
        $this->_prepareDownloadResponse(...$grid->getCsvFile('shopcart_abandoned.csv', -1));
    }

    /**
     * Export abandoned carts report to Excel XML format
     * @return void
     */
    public function exportAbandonedExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_shopcart_abandoned_grid');
        $this->_prepareDownloadResponse(...$grid->getExcelFile('shopcart_abandoned.xml', -1));
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _isAllowed(): bool
    {
        $action = strtolower($this->getRequest()->getActionName());
        $aclPath = match ($action) {
            'customer', 'exportcustomercsv', 'exportcustomerexcel' => 'report/shopcart/customer',
            'product', 'exportproductcsv', 'exportproductexcel' => 'report/shopcart/product',
            'abandoned', 'exportabandonedcsv', 'exportabandonedexcel' => 'report/shopcart/abandoned',
            default => 'report/shopcart',
        };

        return Mage::getSingleton('admin/session')->isAllowed($aclPath);
    }
}
