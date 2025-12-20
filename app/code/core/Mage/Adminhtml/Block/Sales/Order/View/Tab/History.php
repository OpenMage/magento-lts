<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Order history tab
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tab_History extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @inheritDoc
     */
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
                $orderComment->getComment(),
            );
        }

        foreach ($order->getCreditmemosCollection() as $memo) {
            $history[] = $this->_prepareHistoryItem(
                $this->__('Credit memo #%s created', $memo->getIncrementId()),
                $memo->getEmailSent(),
                $memo->getCreatedAtDate(),
            );

            foreach ($memo->getCommentsCollection() as $comment) {
                $history[] = $this->_prepareHistoryItem(
                    $this->__('Credit memo #%s comment added', $memo->getIncrementId()),
                    $comment->getIsCustomerNotified(),
                    $comment->getCreatedAtDate(),
                    $comment->getComment(),
                );
            }
        }

        foreach ($order->getShipmentsCollection() as $shipment) {
            $history[] = $this->_prepareHistoryItem(
                $this->__('Shipment #%s created', $shipment->getIncrementId()),
                $shipment->getEmailSent(),
                $shipment->getCreatedAtDate(),
            );

            foreach ($shipment->getCommentsCollection() as $comment) {
                $history[] = $this->_prepareHistoryItem(
                    $this->__('Shipment #%s comment added', $shipment->getIncrementId()),
                    $comment->getIsCustomerNotified(),
                    $comment->getCreatedAtDate(),
                    $comment->getComment(),
                );
            }
        }

        foreach ($order->getInvoiceCollection() as $invoice) {
            $history[] = $this->_prepareHistoryItem(
                $this->__('Invoice #%s created', $invoice->getIncrementId()),
                $invoice->getEmailSent(),
                $invoice->getCreatedAtDate(),
            );

            foreach ($invoice->getCommentsCollection() as $comment) {
                $history[] = $this->_prepareHistoryItem(
                    $this->__('Invoice #%s comment added', $invoice->getIncrementId()),
                    $comment->getIsCustomerNotified(),
                    $comment->getCreatedAtDate(),
                    $comment->getComment(),
                );
            }
        }

        foreach ($order->getTracksCollection() as $track) {
            $history[] = $this->_prepareHistoryItem(
                $this->__('Tracking number %s for %s assigned', $track->getNumber(), $track->getTitle()),
                false,
                $track->getCreatedAtDate(),
            );
        }

        usort($history, [self::class, '_sortHistoryByTimestamp']);
        return $history;
    }

    /**
     * Status history date/datetime getter
     *
     * @param  string $dateType
     * @param  string $format
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
     * @return string
     */
    public function getItemTitle(array $item)
    {
        return (isset($item['title']) ? $this->escapeHtml($item['title']) : '');
    }

    /**
     * Check whether status history comment is with customer notification
     *
     * @param  bool $isSimpleCheck
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
     * @param  string    $label
     * @param  bool      $notified
     * @param  Zend_Date $created
     * @param  string    $comment
     * @return array
     */
    protected function _prepareHistoryItem($label, $notified, $created, $comment = '')
    {
        return [
            'title'      => $label,
            'notified'   => $notified,
            'comment'    => $comment,
            'created_at' => $created,
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
        return Mage::helper('sales')->__('Comments History');
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
     * @param  array $historyItem
     * @return bool
     */
    public function isCustomerNotificationNotApplicable($historyItem)
    {
        return $historyItem['notified'] == Mage_Sales_Model_Order_Status_History::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }

    /**
     * Comparison For Sorting History By Timestamp
     *
     * @param  mixed $a
     * @param  mixed $b
     * @return int
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private static function _sortHistoryByTimestamp($a, $b)
    {
        $createdAtA = $a['created_at'];
        $createdAtB = $b['created_at'];
        return $createdAtA->getTimestamp() <=> $createdAtB->getTimestamp();
    }
}
