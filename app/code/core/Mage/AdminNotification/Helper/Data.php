<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_AdminNotification
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AdminNotification Data helper
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Last Notice object
     *
     * @var Mage_AdminNotification_Model_Inbox
     */
    protected $_latestNotice;

    /**
     * count of unread notes by type
     *
     * @var array
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
        return isset($this->_unreadNoticeCounts[$severity]) ? $this->_unreadNoticeCounts[$severity] : 0;
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
