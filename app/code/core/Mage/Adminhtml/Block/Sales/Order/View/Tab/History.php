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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order history tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tab_History extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
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
     * Compose and get order full history.
     * Consists of the status history comments as well as of invoices, shipments and creditmemos creations
     *
     * @return array
     */
    public function getFullHistory()
    {
        $order = $this->getOrder();

        $history = [];
        foreach ($order->getAllStatusHistory() as $orderComment) {
            $history[] = $this->_prepareHistoryItem(
                $orderComment->getStatusLabel(),
                $orderComment->getIsCustomerNotified(),
                $orderComment->getCreatedAtDate(),
                $orderComment->getComment()
            );
        }

        foreach ($order->getCreditmemosCollection() as $_memo) {
            $history[] = $this->_prepareHistoryItem(
                $this->__('Credit memo #%s created', $_memo->getIncrementId()),
                $_memo->getEmailSent(),
                $_memo->getCreatedAtDate()
            );

            foreach ($_memo->getCommentsCollection() as $_comment) {
                $history[] = $this->_prepareHistoryItem(
                    $this->__('Credit memo #%s comment added', $_memo->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $_comment->getCreatedAtDate(),
                    $_comment->getComment()
                );
            }
        }

        foreach ($order->getShipmentsCollection() as $_shipment) {
            $history[] = $this->_prepareHistoryItem(
                $this->__('Shipment #%s created', $_shipment->getIncrementId()),
                $_shipment->getEmailSent(),
                $_shipment->getCreatedAtDate()
            );

            foreach ($_shipment->getCommentsCollection() as $_comment) {
                $history[] = $this->_prepareHistoryItem(
                    $this->__('Shipment #%s comment added', $_shipment->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $_comment->getCreatedAtDate(),
                    $_comment->getComment()
                );
            }
        }

        foreach ($order->getInvoiceCollection() as $_invoice) {
            $history[] = $this->_prepareHistoryItem(
                $this->__('Invoice #%s created', $_invoice->getIncrementId()),
                $_invoice->getEmailSent(),
                $_invoice->getCreatedAtDate()
            );

            foreach ($_invoice->getCommentsCollection() as $_comment) {
                $history[] = $this->_prepareHistoryItem(
                    $this->__('Invoice #%s comment added', $_invoice->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $_comment->getCreatedAtDate(),
                    $_comment->getComment()
                );
            }
        }

        foreach ($order->getTracksCollection() as $_track) {
            $history[] = $this->_prepareHistoryItem(
                $this->__('Tracking number %s for %s assigned', $_track->getNumber(), $_track->getTitle()),
                false,
                $_track->getCreatedAtDate()
            );
        }

        usort($history, [__CLASS__, "_sortHistoryByTimestamp"]);
        return $history;
    }

    /**
     * Status history date/datetime getter
     *
     * @param array $item
     * @param string $dateType
     * @param string $format
     * @return string
     */
    public function getItemCreatedAt(array $item, $dateType = 'date', $format = 'medium')
    {
        if (!isset($item['created_at'])) {
            return '';
        }
        if ($dateType === 'date') {
            return $this->formatDate($item['created_at'], $format);
        }
        return $this->formatTime($item['created_at'], $format);
    }

    /**
     * Status history item title getter
     *
     * @param array $item
     * @return string
     */
    public function getItemTitle(array $item)
    {
        return (isset($item['title']) ? $this->escapeHtml($item['title']) : '');
    }

    /**
     * Check whether status history comment is with customer notification
     *
     * @param array $item
     * @param bool $isSimpleCheck
     * @return bool
     */
    public function isItemNotified(array $item, $isSimpleCheck = true)
    {
        if ($isSimpleCheck) {
            return !empty($item['notified']);
        }
        return isset($item['notified']) && $item['notified'] !== false;
    }

    /**
     * Status history item comment getter
     *
     * @param array $item
     * @return string
     */
    public function getItemComment(array $item)
    {
        $strItemComment = '';
        if (isset($item['comment'])) {
            $allowedTags = ['b', 'br', 'strong', 'i', 'u', 'a'];
            /** @var Mage_Adminhtml_Helper_Sales $salesHelper */
            $salesHelper = Mage::helper('adminhtml/sales');
            $strItemComment = $salesHelper->escapeHtmlWithLinks($item['comment'], $allowedTags);
        }
        return $strItemComment;
    }

    /**
     * Map history items as array
     *
     * @param string $label
     * @param bool $notified
     * @param Zend_Date $created
     * @param string $comment
     * @return array
     */
    protected function _prepareHistoryItem($label, $notified, $created, $comment = '')
    {
        return [
            'title'      => $label,
            'notified'   => $notified,
            'comment'    => $comment,
            'created_at' => $created
        ];
    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Comments History');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Order History');
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/commentsHistory', ['_current' => true]);
    }

    /**
     * Can Show Tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is Hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param array $historyItem
     * @return bool
     */
    public function isCustomerNotificationNotApplicable($historyItem)
    {
        return $historyItem['notified'] == Mage_Sales_Model_Order_Status_History::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }

    /**
     * Comparison For Sorting History By Timestamp
     *
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    private static function _sortHistoryByTimestamp($a, $b)
    {
        $createdAtA = $a['created_at'];
        $createdAtB = $b['created_at'];

        /** @var Zend_Date $createdAtA */
        if ($createdAtA->getTimestamp() == $createdAtB->getTimestamp()) {
            return 0;
        }
        return ($createdAtA->getTimestamp() < $createdAtB->getTimestamp()) ? -1 : 1;
    }
}
