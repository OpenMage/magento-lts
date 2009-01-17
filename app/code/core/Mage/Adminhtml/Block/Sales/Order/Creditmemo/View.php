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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml creditmemo view
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Creditmemo_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

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
            $this->_addButton('cancel', array(
                'label'     => Mage::helper('sales')->__('Cancel'),
                'class'     => 'delete',
                'onclick'   => 'setLocation(\''.$this->getCancelUrl().'\')'
                )
            );
        }

        if ($this->getCreditmemo()->canRefund()) {
            $this->_addButton('refund', array(
                'label'     => Mage::helper('sales')->__('Refund'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getRefundUrl().'\')'
                )
            );
        }

        if ($this->getCreditmemo()->canVoid()) {
            $this->_addButton('void', array(
                'label'     => Mage::helper('sales')->__('Void'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getVoidUrl().'\')'
                )
            );
        }
        
        if ($this->getCreditmemo()->getId()) {
            $this->_addButton('print', array(
                'label'     => Mage::helper('sales')->__('Print'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
                )
            );
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

    public function getHeaderText()
    {
        if ($this->getCreditmemo()->getEmailSent()) {
            $emailSent = Mage::helper('sales')->__('Credit Memo email sent');
        }
        else {
            $emailSent = Mage::helper('sales')->__('Credit Memo email not sent');
        }

        $header = Mage::helper('sales')->__('Credit Memo #%s | Date %s | Status %s (%s)',
            $this->getCreditmemo()->getIncrementId(),
            $this->formatDate($this->getCreditmemo()->getCreatedAt(), 'medium', true),
            $this->getCreditmemo()->getStateName(),
            $emailSent
        );
        return $header;
    }

    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            array(
                'order_id'  => $this->getCreditmemo()->getOrderId(),
                'active_tab'=> 'order_creditmemos'
            ));
    }

    public function getCaptureUrl()
    {
        return $this->getUrl('*/*/capture', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
    }

    public function getVoidUrl()
    {
        return $this->getUrl('*/*/void', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
    }

    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', array(
            'invoice_id' => $this->getCreditmemo()->getId()
        ));
    }
    
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/sales_creditmemo/') . '\')');
        }
        return $this;
    }
}
