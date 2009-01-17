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
 * Order history tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tab_History
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/view/tab/history.phtml');
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Retrive full order history
     *
     */
    public function getFullHistory(){
        $order = $this->getOrder();

        $_fullHistory = array();
        foreach ($order->getAllStatusHistory() as $_history){
            $_fullHistory[$_history->getEntityId()] =
                $this->_prepareHistoryItem(
                    $_history->getStatusLabel(),
                    $_history->getIsCustomerNotified(),
                    $_history->getCreatedAt());
        }

        foreach ($order->getCreditmemosCollection() as $_memo){
            $_fullHistory[$_memo->getEntityId()] =
                $this->_prepareHistoryItem($this->__('Credit Memo #%s created', $_memo->getIncrementId()),
                    $_memo->getEmailSent(), $_memo->getCreatedAt());

            foreach ($_memo->getCommentsCollection() as $_comment){
                $_fullHistory[$_comment->getEntityId()] =
                    $this->_prepareHistoryItem($this->__('Credit Memo #%s comment added', $_memo->getIncrementId()),
                        $_comment->getIsCustomerNotified(), $_comment->getCreatedAt(), $_comment->getComment());
            }
        }

        foreach ($order->getShipmentsCollection() as $_shipment){
            $_fullHistory[$_shipment->getEntityId()] =
                $this->_prepareHistoryItem($this->__('Shipment #%s created', $_shipment->getIncrementId()),
                    $_shipment->getEmailSent(), $_shipment->getCreatedAt());

            foreach ($_shipment->getCommentsCollection() as $_comment){
                $_fullHistory[$_comment->getEntityId()] =
                    $this->_prepareHistoryItem($this->__('Shipment #%s comment added', $_shipment->getIncrementId()),
                        $_comment->getIsCustomerNotified(), $_comment->getCreatedAt(), $_comment->getComment());
            }
        }

        foreach ($order->getInvoiceCollection() as $_invoice){
            $_fullHistory[$_invoice->getEntityId()] =
                $this->_prepareHistoryItem($this->__('Invoice #%s created', $_invoice->getIncrementId()),
                    $_invoice->getEmailSent(), $_invoice->getCreatedAt());

            foreach ($_invoice->getCommentsCollection() as $_comment){
                $_fullHistory[$_comment->getEntityId()] =
                    $this->_prepareHistoryItem($this->__('Invoice #%s comment added', $_invoice->getIncrementId()),
                        $_comment->getIsCustomerNotified(), $_comment->getCreatedAt(), $_comment->getComment());
            }
        }

        foreach ($order->getTracksCollection() as $_track){
            $_fullHistory[$_track->getEntityId()] =
                $this->_prepareHistoryItem($this->__('Tracking number %s for %s assigned', $_track->getNumber(), $_track->getTitle()),
                    false, $_track->getCreatedAt());
        }

        krsort($_fullHistory);
        return $_fullHistory;
    }

    protected function _prepareHistoryItem($label, $notified, $created, $comment = '')
    {
        return array(
                'title' => $label,
                'notified' => $notified,
                'comment' => $comment,
                'created_at' => $created
            );
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Comments History');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Order History');
    }

    public function getTabClass()
    {
        return 'ajax only';
    }

    public function getClass()
    {
        return $this->getTabClass();
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/*/commentsHistory', array('_current' => true));
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}