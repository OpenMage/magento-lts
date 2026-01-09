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
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Shopping Cart'), Mage::helper('reports')->__('Shopping Cart'));
        return $this;
    }

    /**
     * @throws Mage_Core_Exception
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
     *
     * @throws Exception
     * @throws Zend_Controller_Response_Exception
     */
    public function exportCustomerCsvAction()
    {
        $fileName   = 'shopcart_customer.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_shopcart_customer_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export shopcart customer report to Excel XML format
     *
     * @throws Exception
     * @throws Zend_Controller_Response_Exception
     */
    public function exportCustomerExcelAction()
    {
        $fileName   = 'shopcart_customer.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_shopcart_customer_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @throws Mage_Core_Exception
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
     *
     * @throws Exception
     * @throws Zend_Controller_Response_Exception
     */
    public function exportProductCsvAction()
    {
        $fileName   = 'shopcart_product.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_shopcart_product_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products report to Excel XML format
     *
     * @throws Exception
     * @throws Zend_Controller_Response_Exception
     */
    public function exportProductExcelAction()
    {
        $fileName   = 'shopcart_product.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_shopcart_product_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @throws Mage_Core_Exception
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
     *
     * @throws Exception
     * @throws Zend_Controller_Response_Exception
     */
    public function exportAbandonedCsvAction()
    {
        $fileName   = 'shopcart_abandoned.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_shopcart_abandoned_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export abandoned carts report to Excel XML format
     *
     * @throws Exception
     * @throws Zend_Controller_Response_Exception
     */
    public function exportAbandonedExcelAction()
    {
        $fileName   = 'shopcart_abandoned.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_shopcart_abandoned_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        $aclPath = match ($action) {
            'customer' => 'report/shopcart/customer',
            'product' => 'report/shopcart/product',
            'abandoned' => 'report/shopcart/abandoned',
            default => 'report/shopcart',
        };

        return Mage::getSingleton('admin/session')->isAllowed($aclPath);
    }
}
