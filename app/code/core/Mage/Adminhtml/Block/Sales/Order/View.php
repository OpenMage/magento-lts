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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order view
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId    = 'order_id';
        $this->_controller  = 'sales_order';
        $this->_mode        = 'view';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('sales_order_view');

        if ($this->_isAllowedAction('edit') && $this->getOrder()->canEdit()) {
            $message = Mage::helper('sales')->__('Are you sure? This order will be cancelled and a new one will be created instead');
            $this->_addButton('order_edit', array(
                 'label'    => Mage::helper('sales')->__('Edit'),
                 'onclick'  => 'deleteConfirm(\''.$message.'\', \'' . $this->getEditUrl() . '\')',
            ));
        }

        if ($this->_isAllowedAction('cancel') && $this->getOrder()->canCancel()) {
            $message = Mage::helper('sales')->__('Are you sure you want to cancel this order?');
            $this->_addButton('order_cancel', array(
                'label'     => Mage::helper('sales')->__('Cancel'),
                'onclick'   => 'deleteConfirm(\''.$message.'\', \'' . $this->getCancelUrl() . '\')',
            ));
        }

        if ($this->_isAllowedAction('creditmemo') && $this->getOrder()->canCreditmemo()) {
            $this->_addButton('order_creditmemo', array(
                'label'     => Mage::helper('sales')->__('Credit Memo'),
                'onclick'   => 'setLocation(\'' . $this->getCreditmemoUrl() . '\')',
            ));
        }

        if ($this->_isAllowedAction('hold') && $this->getOrder()->canHold()) {
            $this->_addButton('order_hold', array(
                'label'     => Mage::helper('sales')->__('Hold'),
                'onclick'   => 'setLocation(\'' . $this->getHoldUrl() . '\')',
            ));
        }

        if ($this->_isAllowedAction('unhold') && $this->getOrder()->canUnhold()) {
            $this->_addButton('order_unhold', array(
                'label'     => Mage::helper('sales')->__('Unhold'),
                'onclick'   => 'setLocation(\'' . $this->getUnholdUrl() . '\')',
            ));
        }

        if ($this->_isAllowedAction('invoice') && $this->getOrder()->canInvoice()) {
            $this->_addButton('order_invoice', array(
                'label'     => Mage::helper('sales')->__('Invoice'),
                'onclick'   => 'setLocation(\'' . $this->getInvoiceUrl() . '\')',
            ));
        }

        if ($this->_isAllowedAction('ship') && $this->getOrder()->canShip()) {
            $this->_addButton('order_ship', array(
                'label'     => Mage::helper('sales')->__('Ship'),
                'onclick'   => 'setLocation(\'' . $this->getShipUrl() . '\')',
            ));
        }

        if ($this->_isAllowedAction('reorder') && $this->getOrder()->canReorder()) {
            $this->_addButton('order_reorder', array(
                'label'     => Mage::helper('sales')->__('Reorder'),
                'onclick'   => 'setLocation(\'' . $this->getReorderUrl() . '\')',
            ));
        }
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    /**
     * Retrieve Order Identifier
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    public function getHeaderText()
    {
        $text = Mage::helper('sales')->__('Order # %s | Order Date %s',
            $this->getOrder()->getRealOrderId(),
            $this->formatDate($this->getOrder()->getCreatedAt(), 'medium', true)
        );
        /*if ($this->getOrder()->getRelationParentRealId()) {
            $text = Mage::helper('sales')->__('Order # %s | Order Date %s',
                $this->getOrder()->getRealOrderId(),
                $this->formatDate($this->getOrder()->getCreatedAt(), 'medium', true)
            );
        }
        else {
            $text = Mage::helper('sales')->__('Order # %s | Order Date %s',
                $this->getOrder()->getRealOrderId(),
                $this->formatDate($this->getOrder()->getCreatedAt(), 'medium', true)
            );
        }*/
        return $text;
    }

    public function getUrl($params='', $params2=array())
    {
        $params2['order_id'] = $this->getOrderId();
        return parent::getUrl($params, $params2);
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/sales_order_edit/start');
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel');
    }

    public function getInvoiceUrl()
    {
        return $this->getUrl('*/sales_order_invoice/start');
    }

    public function getCreditmemoUrl()
    {
        return $this->getUrl('*/sales_order_creditmemo/start');
    }

    public function getHoldUrl()
    {
        return $this->getUrl('*/*/hold');
    }

    public function getUnholdUrl()
    {
        return $this->getUrl('*/*/unhold');
    }

    public function getShipUrl()
    {
        return $this->getUrl('*/sales_order_shipment/start');
    }

    public function getCommentUrl()
    {
        return $this->getUrl('*/*/comment');
    }

    public function getReorderUrl()
    {
        return $this->getUrl('*/sales_order_create/reorder');
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/' . $action);
    }
}