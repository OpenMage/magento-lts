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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml invoice create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Invoice_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId    = 'invoice_id';
        $this->_controller  = 'sales_order_invoice';
        $this->_mode        = 'view';

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        if ($this->getInvoice()->canCancel()) {
            $this->_addButton('cancel', array(
                'label'     => Mage::helper('sales')->__('Cancel'),
                'class'     => 'delete',
                'onclick'   => 'setLocation(\''.$this->getCancelUrl().'\')'
                )
            );
        }

        if ($this->getInvoice()->getOrder()->canCreditmemo()) {
            if ($this->getInvoice()->getOrder()->getPayment()->canRefundPartialPerInvoice()
                || !$this->getInvoice()->getIsUsedForRefund())
            {
                $this->_addButton('capture', array(
                    'label'     => Mage::helper('sales')->__('Credit Memo'),
                    'class'     => 'save',
                    'onclick'   => 'setLocation(\''.$this->getCreditMemoUrl().'\')'
                    )
                );
            }
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/capture')
            && $this->getInvoice()->canCapture()) {
            $this->_addButton('capture', array(
                'label'     => Mage::helper('sales')->__('Capture'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getCaptureUrl().'\')'
                )
            );
        }

        if ($this->getInvoice()->canVoid()) {
            $this->_addButton('void', array(
                'label'     => Mage::helper('sales')->__('Void'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getVoidUrl().'\')'
                )
            );
        }

        if ($this->getInvoice()->getId()) {
            $this->_addButton('print', array(
                'label'     => Mage::helper('sales')->__('Print'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
                )
            );
        }
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    public function getHeaderText()
    {
        if ($this->getInvoice()->getEmailSent()) {
            $emailSent = Mage::helper('sales')->__('Invoice email sent');
        }
        else {
            $emailSent = Mage::helper('sales')->__('Invoice email not sent');
        }

        $header = Mage::helper('sales')->__('Invoice #%s | %s (%s)',
            $this->getInvoice()->getIncrementId(),
            $this->getInvoice()->getStateName(),
            $emailSent
        );
        /*$header = Mage::helper('sales')->__('Invoice #%s | Order Date: %s | Customer Name: %s',
            $this->getInvoice()->getIncrementId(),
            $this->formatDate($this->getInvoice()->getOrder()->getCreatedAt(), 'medium', true),
            $this->getInvoice()->getOrder()->getCustomerName()
        );*/
        return $header;
    }

    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            array(
                'order_id'  => $this->getInvoice()->getOrderId(),
                'active_tab'=> 'order_invoices'
            ));
    }

    public function getCaptureUrl()
    {
        return $this->getUrl('*/*/capture', array('invoice_id'=>$this->getInvoice()->getId()));
    }

    public function getVoidUrl()
    {
        return $this->getUrl('*/*/void', array('invoice_id'=>$this->getInvoice()->getId()));
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', array('invoice_id'=>$this->getInvoice()->getId()));
    }

    public function getCreditMemoUrl()
    {
        return $this->getUrl('*/sales_order_creditmemo/start', array(
            'order_id'  => $this->getInvoice()->getOrder()->getId(),
            'invoice_id'=> $this->getInvoice()->getId(),
        ));
    }

    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', array(
            'invoice_id' => $this->getInvoice()->getId()
        ));
    }

    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/sales_invoice/') . '\')');
        }
        return $this;
    }
}