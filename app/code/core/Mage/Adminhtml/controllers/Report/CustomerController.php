<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Customer reports admin controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Report_CustomerController extends Mage_Adminhtml_Controller_Action
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
            ->_addBreadcrumb(Mage::helper('reports')->__('Customers'), Mage::helper('reports')->__('Customers'));
        return $this;
    }

    /**
     * @return void
     */
    public function accountsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('New Accounts'));

        $this->_initAction()
            ->_setActiveMenu('report/customers/accounts')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('New Accounts'), Mage::helper('adminhtml')->__('New Accounts'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_customer_accounts'))
            ->renderLayout();
    }

    /**
     * Export new accounts report grid to CSV format
     * @return void
     */
    public function exportAccountsCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_customer_accounts_grid');
        $this->_prepareDownloadResponse(...$grid->getCsv('new_accounts.csv', -1));
    }

    /**
     * Export new accounts report grid to Excel XML format
     * @return void
     */
    public function exportAccountsExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_customer_accounts_grid');
        $this->_prepareDownloadResponse(...$grid->getExcel('accounts.xml', -1));
    }

    /**
     * @return void
     */
    public function ordersAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('Customers by Number of Orders'));

        $this->_initAction()
            ->_setActiveMenu('report/customers/orders')
            ->_addBreadcrumb(
                Mage::helper('reports')->__('Customers by Number of Orders'),
                Mage::helper('reports')->__('Customers by Number of Orders'),
            )
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_customer_orders'))
            ->renderLayout();
    }

    /**
     * Export customers most ordered report to CSV format
     * @return void
     */
    public function exportOrdersCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_customer_orders_grid');
        $this->_prepareDownloadResponse(...$grid->getCsv('customers_orders.csv', -1));
    }

    /**
     * Export customers most ordered report to Excel XML format
     * @return void
     */
    public function exportOrdersExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_customer_orders_grid');
        $this->_prepareDownloadResponse(...$grid->getExcel('customers_orders.xml', -1));
    }

    /**
     * @return void
     */
    public function totalsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Customers'))
             ->_title($this->__('Customers by Orders Total'));

        $this->_initAction()
            ->_setActiveMenu('report/customers/totals')
            ->_addBreadcrumb(
                Mage::helper('reports')->__('Customers by Orders Total'),
                Mage::helper('reports')->__('Customers by Orders Total'),
            )
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_customer_totals'))
            ->renderLayout();
    }

    /**
     * Export customers biggest totals report to CSV format
     * @return void
     */
    public function exportTotalsCsvAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_customer_totals_grid');
        $this->_prepareDownloadResponse(...$grid->getCsv('cuatomer_totals.csv', -1));
    }

    /**
     * Export customers biggest totals report to Excel XML format
     * @return void
     */
    public function exportTotalsExcelAction()
    {
        $grid = $this->getLayout()->createBlock('adminhtml/report_customer_totals_grid');
        $this->_prepareDownloadResponse(...$grid->getExcel('customer_totals.xml', -1));
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _isAllowed(): bool
    {
        $action = strtolower($this->getRequest()->getActionName());
        $aclPath =  match ($action) {
            'accounts', 'exportaccountscsv', 'exportaccountsexcel' => 'report/customers/accounts',
            'orders', 'exportorderscsv', 'exportordersexcel' => 'report/customers/orders',
            'totals', 'exporttotalscsv', 'exporttotalsexcel' => 'report/customers/totals',
            default => 'report/customers',
        };

        return Mage::getSingleton('admin/session')->isAllowed($aclPath);
    }
}
