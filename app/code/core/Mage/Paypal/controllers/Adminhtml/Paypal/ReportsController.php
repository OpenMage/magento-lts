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
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PayPal Settlement Reports Controller
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Adminhtml_Paypal_ReportsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Grid action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('paypal/adminhtml_settlement_report'))
            ->renderLayout();
    }

    /**
     * Ajax callback for grid actions
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('paypal/adminhtml_settlement_report_grid')->toHtml()
        );
    }

    /**
     * View transaction details action
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
     */
    public function fetchAction()
    {
        try {
            $reports = Mage::getModel('paypal/report_settlement');
            /** @var Mage_Paypal_Model_Report_Settlement $reports */
            $credentials = $reports->getSftpCredentials();
            if (empty($credentials)) {
                Mage::throwException(Mage::helper('paypal')->__('Nothing to fetch because of an empty configuration.'));
            }
            foreach ($credentials as $config) {
                try {
                    $fetched = $reports->fetchAndSave($config);
                    $this->_getSession()->addSuccess(
                        Mage::helper('paypal')->__("Fetched %s report rows from '%s@%s'.", $fetched, $config['username'], $config['hostname'])
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError(
                        Mage::helper('paypal')->__("Failed to fetch reports from '%s@%s'.", $config['username'], $config['hostname'])
                    );
                    Mage::logException($e);
                }
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
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
            ->_setActiveMenu('report/sales')
            ->_addBreadcrumb(Mage::helper('paypal')->__('Reports'), Mage::helper('paypal')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('paypal')->__('Sales'), Mage::helper('paypal')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('paypal')->__('PayPal Settlement Reports'), Mage::helper('paypal')->__('PayPal Settlement Reports'));
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'index':
            case 'details':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/paypal_settlement_reports/view');
            case 'fetch':
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/paypal_settlement_reports/fetch');
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/paypal_settlement_reports');
        }
    }
}
