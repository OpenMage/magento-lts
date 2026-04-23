<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * PayPal Settlement Reports Controller
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Adminhtml_Paypal_ReportsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Grid action
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('paypal/adminhtml_settlement_report'))
            ->renderLayout();
    }

    /**
     * Ajax callback for grid actions
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('paypal/adminhtml_settlement_report_grid')->toHtml(),
        );
    }

    /**
     * View transaction details action
     * @return void
     */
    public function detailsAction()
    {
        $rowId = $this->getRequest()->getParam('id');
        $row = Mage::getModel('paypal/report_settlement_row')->load($rowId);
        if (!$row->getId()) {
            $this->_redirect('*/*/');
            return;
        }

        Mage::register('current_transaction', $row);
        $this->_initAction()
            ->_title($this->__('View Transaction'))
            ->_addContent($this->getLayout()->createBlock('paypal/adminhtml_settlement_details', 'settlementDetails'))
            ->renderLayout();
    }

    /**
     * Forced fetch reports action
     * @return void
     */
    public function fetchAction()
    {
        try {
            /** @var Mage_Paypal_Model_Report_Settlement $reports */
            $reports = Mage::getModel('paypal/report_settlement');
            $credentials = $reports->getSftpCredentials();
            if (empty($credentials)) {
                Mage::throwException(Mage::helper('paypal')->__('Nothing to fetch because of an empty configuration.'));
            }

            foreach ($credentials as $config) {
                try {
                    $fetched = $reports->fetchAndSave($config);
                    $this->_getSession()->addSuccess(
                        Mage::helper('paypal')->__("Fetched %s report rows from '%s@%s'.", $fetched, $config['username'], $config['hostname']),
                    );
                } catch (Exception $exception) {
                    $this->_getSession()->addError(
                        Mage::helper('paypal')->__("Failed to fetch reports from '%s@%s'.", $config['username'], $config['hostname']),
                    );
                    Mage::logException($exception);
                }
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            Mage::logException($exception);
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Initialize titles, navigation
     * @return $this
     */
    protected function _initAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('PayPal Settlement Reports'));
        $this->loadLayout()
            ->_setActiveMenu('report/salesroot/paypal_settlement_reports')
            ->_addBreadcrumb(Mage::helper('paypal')->__('Reports'), Mage::helper('paypal')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('paypal')->__('Sales'), Mage::helper('paypal')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('paypal')->__('PayPal Settlement Reports'), Mage::helper('paypal')->__('PayPal Settlement Reports'));
        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _isAllowed(): bool
    {
        $action = strtolower($this->getRequest()->getActionName());
        $aclPath = match ($action) {
            'index', 'details' => 'report/salesroot/paypal_settlement_reports/view',
            'fetch' => 'report/salesroot/paypal_settlement_reports/fetch',
            default => 'report/salesroot/paypal_settlement_reports',
        };

        return Mage::getSingleton('admin/session')->isAllowed($aclPath);
    }
}
