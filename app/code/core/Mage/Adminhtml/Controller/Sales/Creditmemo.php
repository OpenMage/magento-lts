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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Sales_Creditmemo extends Mage_Adminhtml_Controller_Action
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
     * @return Mage_Adminhtml_Sales_CreditmemoController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Credit Memos'),$this->__('Credit Memos'));
        return $this;
    }

    /**
     * Creditmemos grid
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_creditmemo'))
            ->renderLayout();
    }

    /**
     * Creditmemo information page
     */
    public function viewAction()
    {
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            $this->_forward('view', 'sales_order_creditmemo', null, array('come_from' => 'sales_creditmemo'));
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            if ($creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId)) {
                $creditmemo->sendEmail();
                $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                    ->getUnnotifiedForInstance($creditmemo, Mage_Sales_Model_Order_Creditmemo::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }

                $this->_getSession()->addSuccess(Mage::helper('sales')->__('The message was sent.'));
                $this->_redirect('*/sales_order_creditmemo/view', array(
                    'creditmemo_id' => $creditmemoId
                ));
            }
        }
    }

    public function pdfcreditmemosAction(){
        $creditmemosIds = $this->getRequest()->getPost('creditmemo_ids');
        if (!empty($creditmemosIds)) {
            $invoices = Mage::getResourceModel('sales/order_creditmemo_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $creditmemosIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($invoices);
            } else {
                $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }

    public function printAction()
    {
        /** @see Mage_Adminhtml_Sales_Order_InvoiceController */
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            if ($creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId)) {
                $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf(array($creditmemo));
                $this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/creditmemo');
    }
}
