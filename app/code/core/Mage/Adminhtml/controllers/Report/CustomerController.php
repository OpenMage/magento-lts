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
 *
 * Customer reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Report_CustomerController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if(!$act)
            $act = 'default';

        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Customers'), Mage::helper('reports')->__('Customers'));
        return $this;
    }

    public function accountsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('New Accounts'));

        $this->_initAction()
            ->_setActiveMenu('report/customer/accounts')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('New Accounts'), Mage::helper('adminhtml')->__('New Accounts'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_customer_accounts'))
            ->renderLayout();
    }

    /**
     * Export new accounts report grid to CSV format
     */
    public function exportAccountsCsvAction()
    {
        $fileName   = 'new_accounts.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_customer_accounts_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export new accounts report grid to Excel XML format
     */
    public function exportAccountsExcelAction()
    {
        $fileName   = 'accounts.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_customer_accounts_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function ordersAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('Customers by Number of Orders'));

        $this->_initAction()
            ->_setActiveMenu('report/customer/orders')
            ->_addBreadcrumb(Mage::helper('reports')->__('Customers by number of orders'),
                Mage::helper('reports')->__('Customers by number of orders'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_customer_orders'))
            ->renderLayout();
    }

    /**
     * Export customers most ordered report to CSV format
     */
    public function exportOrdersCsvAction()
    {
        $fileName   = 'customers_orders.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_customer_orders_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customers most ordered report to Excel XML format
     */
    public function exportOrdersExcelAction()
    {
        $fileName   = 'customers_orders.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_customer_orders_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function totalsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('Customers By Orders Total'));

        $this->_initAction()
            ->_setActiveMenu('report/customer/totals')
            ->_addBreadcrumb(Mage::helper('reports')->__('Customers by orders total'),
                Mage::helper('reports')->__('Customers by orders total'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_customer_totals'))
            ->renderLayout();
    }

    /**
     * Export customers biggest totals report to CSV format
     */
    public function exportTotalsCsvAction()
    {
        $fileName   = 'cuatomer_totals.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_customer_totals_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customers biggest totals report to Excel XML format
     */
    public function exportTotalsExcelAction()
    {
        $fileName   = 'customer_totals.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_customer_totals_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'accounts':
                return Mage::getSingleton('admin/session')->isAllowed('report/customers/accounts');
                break;
            case 'orders':
                return Mage::getSingleton('admin/session')->isAllowed('report/customers/orders');
                break;
            case 'totals':
                return Mage::getSingleton('admin/session')->isAllowed('report/customers/totals');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/customers');
                break;
        }
    }
}
