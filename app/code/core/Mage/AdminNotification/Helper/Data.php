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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * AdminNotification Data helper
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_POPUP_URL    = 'system/adminnotification/popup_url';

    /**
     * Widget Popup Notification Object URL
     *
     * @var string
     */
    protected $_popupUrl;

    /**
     * Is readable Popup Notification Object flag
     *
     * @var bool
     */
    protected $_popupReadable;

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
     */
    public function getPopupObjectUrl($withExt = false)
    {
        if (is_null($this->_popupUrl)) {
            $sheme = Mage::app()->getFrontController()->getRequest()->isSecure()
                ? 'https://'
                : 'http://';

            $this->_popupUrl = $sheme . Mage::getStoreConfig(self::XML_PATH_POPUP_URL);
        }
        return $this->_popupUrl . ($withExt ? '.swf' : '');
    }

    /**
     * Check is readable Popup Notification Object
     * @deprecated after 1.4.2.0
     *
     * @return bool
     */
    public function isReadablePopupObject()
    {
        if (is_null($this->_popupReadable)) {
            $this->_popupReadable = false;
            $curl = new Varien_Http_Adapter_Curl();
            $curl->setConfig(array(
                'timeout'   => 2
            ));
            $curl->write(Zend_Http_Client::GET, $this->getPopupObjectUrl(true));
            if ($curl->read()) {
                if ($curl->getInfo(CURLINFO_HTTP_CODE) == 200) {
                    $this->_popupReadable = true;
                }
            }

            $curl->close();
        }
        return $this->_popupReadable;
    }
}
