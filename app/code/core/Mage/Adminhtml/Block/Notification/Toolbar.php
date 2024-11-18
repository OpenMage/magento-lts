<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml AdminNotification toolbar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Notification_Toolbar extends Mage_Adminhtml_Block_Template
{
    /**
     * Initialize Toolbar block
     *
     */
    protected function _construct() {}

    /**
     * Retrieve helper
     *
     * @return Mage_AdminNotification_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('adminnotification');
    }

    /**
     * Check is show toolbar
     *
     * @return bool
     * @throws Exception
     */
    public function isShow()
    {
        if (!$this->isOutputEnabled('Mage_AdminNotification')) {
            return false;
        }
        if ($this->getRequest()->getControllerName() === 'notification') {
            return false;
        }
        if ($this->getCriticalCount() == 0 && $this->getMajorCount() == 0 && $this->getMinorCount() == 0
            && $this->getNoticeCount() == 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve count of critical errors
     *
     * @return int
     */
    public function getCriticalCount()
    {
        return $this->_getHelper()
            ->getUnreadNoticeCount(Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL);
    }

    /**
     * Retrieve count of major errors
     *
     * @return int
     */
    public function getMajorCount()
    {
        return $this->_getHelper()
            ->getUnreadNoticeCount(Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR);
    }

    /**
     * Retrieve count of minor errors
     *
     * @return int
     */
    public function getMinorCount()
    {
        return $this->_getHelper()
            ->getUnreadNoticeCount(Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR);
    }

    /**
     * Retrieve count of notices
     *
     * @return int
     */
    public function getNoticeCount()
    {
        return $this->_getHelper()
            ->getUnreadNoticeCount(Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE);
    }

    /**
     * Retrieve Notices Inbox URL
     *
     * @return string
     */
    public function getNoticesInboxUrl()
    {
        return $this->getUrl('adminhtml/notification');
    }

    /**
     * Retrieve last notice Title
     *
     * @return string
     */
    public function getLatestNotice()
    {
        return  $this->_getHelper()
            ->getLatestNotice()->getTitle();
    }

    /**
     * Retrieve Last Notice URL
     *
     * @return string
     */
    public function getLatestNoticeUrl()
    {
        return $this->_getHelper()->getLatestNotice()->getUrl();
    }

    /**
     * Check is Message Window Available
     *
     * @return bool
     */
    public function isMessageWindowAvailable()
    {
        /** @var Mage_Adminhtml_Block_Notification_Window $block */
        $block = $this->getLayout()->getBlock('notification_window');
        if ($block) {
            return $block->canShow();
        }
        return false;
    }
}
