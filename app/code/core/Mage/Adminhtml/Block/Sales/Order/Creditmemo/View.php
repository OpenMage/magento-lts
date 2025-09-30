<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * Adminhtml creditmemo view
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Creditmemo_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Add & remove control buttons
     */
    public function __construct()
    {
        $this->_objectId    = 'creditmemo_id';
        $this->_controller  = 'sales_order_creditmemo';
        $this->_mode        = 'view';

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        if ($this->getCreditmemo()->canCancel()) {
            $this->_addButton('cancel', [
                'label'     => Mage::helper('sales')->__('Cancel'),
                'class'     => 'delete',
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getCancelUrl()),
            ]);
        }

        if ($this->_isAllowedAction('emails')) {
            $this->addButton('send_notification', [
                'label'     => Mage::helper('sales')->__('Send Email'),
                'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs(
                    $this->getEmailUrl(),
                    Mage::helper('sales')->__('Are you sure you want to send Creditmemo email to customer?'),
                ),
            ]);
        }

        if ($this->getCreditmemo()->canRefund()) {
            $this->_addButton('refund', [
                'label'     => Mage::helper('sales')->__('Refund'),
                'class'     => 'save',
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getRefundUrl()),
            ]);
        }

        if ($this->getCreditmemo()->canVoid()) {
            $this->_addButton('void', [
                'label'     => Mage::helper('sales')->__('Void'),
                'class'     => 'save',
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getVoidUrl()),

            ]);
        }

        if ($this->getCreditmemo()->getId()) {
            $this->_addButton('print', [
                'label'     => Mage::helper('sales')->__('Print'),
                'class'     => 'save',
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getPrintUrl()),
            ]);
        }
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    /**
     * Retrieve text for header
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getCreditmemo()->getEmailSent()) {
            $emailSent = Mage::helper('sales')->__('the credit memo email was sent');
        } else {
            $emailSent = Mage::helper('sales')->__('the credit memo email is not sent');
        }
        return Mage::helper('sales')->__(
            'Credit Memo #%1$s | %3$s | %2$s (%4$s)',
            $this->getCreditmemo()->getIncrementId(),
            $this->formatDate(
                $this->getCreditmemo()->getCreatedAtDate(),
                'medium',
                true,
            ),
            $this->getCreditmemo()->getStateName(),
            $emailSent,
        );
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            [
                'order_id'  => $this->getCreditmemo()->getOrderId(),
                'active_tab' => 'order_creditmemos',
            ],
        );
    }

    /**
     * Retrieve capture url
     *
     * @return string
     */
    public function getCaptureUrl()
    {
        return $this->getUrl('*/*/capture', ['creditmemo_id' => $this->getCreditmemo()->getId()]);
    }

    /**
     * Retrieve void url
     *
     * @return string
     */
    public function getVoidUrl()
    {
        return $this->getUrl('*/*/void', ['creditmemo_id' => $this->getCreditmemo()->getId()]);
    }

    /**
     * Retrieve cancel url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', ['creditmemo_id' => $this->getCreditmemo()->getId()]);
    }

    /**
     * Retrieve email url
     *
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl('*/*/email', [
            'creditmemo_id' => $this->getCreditmemo()->getId(),
            'order_id'      => $this->getCreditmemo()->getOrderId(),
        ]);
    }

    /**
     * Retrieve print url
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', [
            'creditmemo_id' => $this->getCreditmemo()->getId(),
        ]);
    }

    /**
     * Update 'back' button url
     *
     * @return Mage_Adminhtml_Block_Widget_Container|Mage_Adminhtml_Block_Sales_Order_Creditmemo_View
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getCreditmemo()->getBackUrl()) {
                return $this->_updateButton(
                    'back',
                    'onclick',
                    Mage::helper('core/js')->getSetLocationJs($this->getCreditmemo()->getBackUrl()),
                );
            }

            return $this->_updateButton(
                'back',
                'onclick',
                Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/sales_creditmemo/')),
            );
        }
        return $this;
    }

    /**
     * Check whether action is allowed
     *
     * @param string $action
     * @return bool
     */
    public function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/' . $action);
    }
}
