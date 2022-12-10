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
 *
 * Customer reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Report_CustomerController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if (!$act) {
            $act = 'default';
        }

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
            ->_addBreadcrumb(
                Mage::helper('reports')->__('Customers by Number of Orders'),
                Mage::helper('reports')->__('Customers by Number of Orders')
            )
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
             ->_title($this->__('Customers by Orders Total'));

        $this->_initAction()
            ->_setActiveMenu('report/customer/totals')
            ->_addBreadcrumb(
                Mage::helper('reports')->__('Customers by Orders Total'),
                Mage::helper('reports')->__('Customers by Orders Total')
            )
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

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'accounts':
                return Mage::getSingleton('admin/session')->isAllowed('report/customers/accounts');
            case 'orders':
                return Mage::getSingleton('admin/session')->isAllowed('report/customers/orders');
            case 'totals':
                return Mage::getSingleton('admin/session')->isAllowed('report/customers/totals');
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/customers');
        }
    }
}
