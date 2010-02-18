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
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Sales_Invoice extends Mage_Adminhtml_Controller_Action
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_InvoiceController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Invoices'),$this->__('Invoices'));
        return $this;
    }

    /**
     * Order grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/sales_invoice_grid')->toHtml()
        );
    }

    /**
     * Invoices grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Invoices'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_invoice'))
            ->renderLayout();
    }

    /**
     * Invoice information page
     */
    public function viewAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            $this->_forward('view', 'sales_order_invoice', null, array('come_from'=>'invoice'));
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            if ($invoice = Mage::getModel('sales/order_invoice')->load($invoiceId)) {
                $invoice->sendEmail();
                $this->_getSession()->addSuccess(Mage::helper('sales')->__('Message was successfully sent'));
                $this->_redirect('*/sales_invoice/view', array(
                    'order_id'  => $invoice->getOrder()->getId(),
                    'invoice_id'=> $invoiceId,
                ));
            }
        }
    }

    public function printAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            if ($invoice = Mage::getModel('sales/order_invoice')->load($invoiceId)) {
                if ($invoice->getStoreId()) {
                    Mage::app()->setCurrentStore($invoice->getStoreId());
                }
                $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoice));
                $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    public function pdfinvoicesAction(){
        $invoicesIds = $this->getRequest()->getPost('invoice_ids');
        if (!empty($invoicesIds)) {
            $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $invoicesIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
            } else {
                $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/invoice');
    }

}
