<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * Adminhtml invoice create
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Invoice_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Admin session
     *
     * @var Mage_Admin_Model_Session
     */
    protected $_session;

    /**
     * Mage_Adminhtml_Block_Sales_Order_Invoice_View constructor.
     */
    public function __construct()
    {
        $this->_objectId    = 'invoice_id';
        $this->_controller  = 'sales_order_invoice';
        $this->_mode        = 'view';
        $this->_session = Mage::getSingleton('admin/session');

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        if ($this->_isAllowedAction('cancel') && $this->getInvoice()->canCancel()) {
            $this->_addButton('cancel', [
                'label'     => Mage::helper('sales')->__('Cancel'),
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getCancelUrl()),
                'class'     => 'delete',
            ]);
        }

        if ($this->_isAllowedAction('emails')) {
            $this->addButton('send_notification', [
                'label'     => Mage::helper('sales')->__('Send Email'),
                'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs(
                    $this->getEmailUrl(),
                    Mage::helper('sales')->__('Are you sure you want to send Invoice email to customer?'),
                ),
            ]);
        }

        $orderPayment = $this->getInvoice()->getOrder()->getPayment();

        if ($this->_isAllowedAction('creditmemo') && $this->getInvoice()->getOrder()->canCreditmemo()) {
            if (($orderPayment->canRefundPartialPerInvoice()
                && $this->getInvoice()->canRefund()
                && $orderPayment->getAmountPaid() > $orderPayment->getAmountRefunded())
                || ($orderPayment->canRefund() && !$this->getInvoice()->getIsUsedForRefund())
            ) {
                $this->_addButton('capture', [ // capture?
                    'label'     => Mage::helper('sales')->__('Credit Memo'),
                    'class'     => 'go',
                    'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getCreditMemoUrl()),
                ]);
            }
        }

        if ($this->_isAllowedAction('capture') && $this->getInvoice()->canCapture()) {
            $this->_addButton('capture', [
                'label'     => Mage::helper('sales')->__('Capture'),
                'class'     => 'save',
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getCaptureUrl()),
            ]);
        }

        if ($this->getInvoice()->canVoid()) {
            $this->_addButton('void', [
                'label'     => Mage::helper('sales')->__('Void'),
                'class'     => 'save',
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getVoidUrl()),
            ]);
        }

        if ($this->getInvoice()->getId()) {
            $this->_addButton('print', [
                'label'     => Mage::helper('sales')->__('Print'),
                'class'     => 'save',
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getPrintUrl()),
            ]);
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

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getInvoice()->getEmailSent()) {
            $emailSent = Mage::helper('sales')->__('the invoice email was sent');
        } else {
            $emailSent = Mage::helper('sales')->__('the invoice email is not sent');
        }
        return Mage::helper('sales')->__(
            'Invoice #%1$s | %2$s | %4$s (%3$s)',
            $this->getInvoice()->getIncrementId(),
            $this->getInvoice()->getStateName(),
            $emailSent,
            $this->formatDate(
                $this->getInvoice()->getCreatedAtDate(),
                'medium',
                true,
            ),
        );
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            [
                'order_id'  => $this->getInvoice()->getOrderId(),
                'active_tab' => 'order_invoices',
            ],
        );
    }

    /**
     * @return string
     */
    public function getCaptureUrl()
    {
        return $this->getUrl('*/*/capture', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * @return string
     */
    public function getVoidUrl()
    {
        return $this->getUrl('*/*/void', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl('*/*/email', [
            'order_id'  => $this->getInvoice()->getOrder()->getId(),
            'invoice_id' => $this->getInvoice()->getId(),
        ]);
    }

    /**
     * @return string
     */
    public function getCreditMemoUrl()
    {
        return $this->getUrl('*/sales_order_creditmemo/start', [
            'order_id'  => $this->getInvoice()->getOrder()->getId(),
            'invoice_id' => $this->getInvoice()->getId(),
        ]);
    }

    /**
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', [
            'invoice_id' => $this->getInvoice()->getId(),
        ]);
    }

    /**
     * @param string $flag
     * @return $this
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getInvoice()->getBackUrl()) {
                return $this->_updateButton(
                    'back',
                    'onclick',
                    Mage::helper('core/js')->getSetLocationJs($this->getInvoice()->getBackUrl()),
                );
            }
            return $this->_updateButton(
                'back',
                'onclick',
                Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/sales_invoice/')),
            );
        }
        return $this;
    }

    /**
     * Check whether is allowed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return $this->_session->isAllowed('sales/order/actions/' . $action);
    }
}
