<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_AdminNotification
 */

/**
 * AdminNotification Data helper
 *
 * @package    Mage_AdminNotification
 */
class Mage_AdminNotification_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_AdminNotification';

    /**
     * Last Notice object
     *
     * @var null|Mage_AdminNotification_Model_Inbox
     */
    protected $_latestNotice;

    /**
     * count of unread notes by type
     *
     * @var null|array
     */
    protected $_unreadNoticeCounts;

    /**
     * Retrieve latest notice model
     *
     * @return Mage_AdminNotification_Model_Inbox
     */
    public function getLatestNotice()
    {
        if (is_null($this->_latestNotice)) {
            $this->_latestNotice = Mage::getModel('adminnotification/inbox')->loadLatestNotice();
        }

        return $this->_latestNotice;
    }

    /**
     * Retrieve count of unread notes by type
     *
     * @param int $severity
     * @return int
     */
    public function getUnreadNoticeCount($severity)
    {
        if (is_null($this->_unreadNoticeCounts)) {
            $this->_unreadNoticeCounts = Mage::getModel('adminnotification/inbox')->getNoticeStatus();
        }

        return $this->_unreadNoticeCounts[$severity] ?? 0;
    }

    /**
     * Retrieve Widget Popup Notification Object URL
     *
     * @param bool $withExt
     * @return string
     * @deprecated v19.4.16
     */
    public function getPopupObjectUrl($withExt = false)
    {
        return '';
    }
}
