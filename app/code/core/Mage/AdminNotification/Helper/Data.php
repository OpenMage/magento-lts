<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AdminNotification Data helper
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 */
class Mage_AdminNotification_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_AdminNotification';

    /**
     * Last Notice object
     *
     * @var Mage_AdminNotification_Model_Inbox|null
     */
    protected $_latestNotice;

    /**
     * count of unread notes by type
     *
     * @var array|null
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
