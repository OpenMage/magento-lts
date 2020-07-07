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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core Observer model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Observer
{
    /**
     * Check if synchronize process is finished and generate notification message
     *
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function addSynchronizeNotification(Varien_Event_Observer $observer)
    {
        $adminSession = Mage::getSingleton('admin/session');
        if (!$adminSession->hasSyncProcessStopWatch()) {
            $flag = Mage::getSingleton('core/file_storage')->getSyncFlag();
            $state = $flag->getState();
            if ($state == Mage_Core_Model_File_Storage_Flag::STATE_RUNNING) {
                $syncProcessStopWatch = true;
            } else {
                $syncProcessStopWatch = false;
            }

            $adminSession->setSyncProcessStopWatch($syncProcessStopWatch);
        }
        $adminSession->setSyncProcessStopWatch(false);

        if (!$adminSession->getSyncProcessStopWatch()) {
            if (!isset($flag)) {
                $flag = Mage::getSingleton('core/file_storage')->getSyncFlag();
            }

            $state = $flag->getState();
            if ($state == Mage_Core_Model_File_Storage_Flag::STATE_FINISHED) {
                $flagData = $flag->getFlagData();
                if (isset($flagData['has_errors']) && $flagData['has_errors']) {
                    $severity       = Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;
                    $title          = Mage::helper('adminhtml')->__('An error has occured while syncronizing media storages.');
                    $description    = Mage::helper('adminhtml')->__('One or more media files failed to be synchronized during the media storages syncronization process. Refer to the log file for details.');
                } else {
                    $severity       = Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE;
                    $title          = Mage::helper('adminhtml')->__('Media storages synchronization has completed!');
                    $description    = Mage::helper('adminhtml')->__('Synchronization of media storages has been successfully completed.');
                }

                $date = date('Y-m-d H:i:s');
                Mage::getModel('adminnotification/inbox')->parse(array(
                    array(
                        'severity'      => $severity,
                        'date_added'    => $date,
                        'title'         => $title,
                        'description'   => $description,
                        'url'           => '',
                        'internal'      => true
                    )
                ));

                $flag->setState(Mage_Core_Model_File_Storage_Flag::STATE_NOTIFIED)->save();
            }

            $adminSession->setSyncProcessStopWatch(false);
        }

        return $this;
    }

    /**
     * Cron job method to clean old cache resources
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function cleanCache(Mage_Cron_Model_Schedule $schedule)
    {
        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_OLD);
        Mage::dispatchEvent('core_clean_cache');
    }


    /**
     * Cleans cache by tags
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function cleanCacheByTags(Varien_Event_Observer $observer)
    {
        /** @var array $tags */
        $tags = $observer->getEvent()->getTags();
        if (empty($tags)) {
            Mage::app()->cleanCache();
            return $this;
        }

        Mage::app()->cleanCache($tags);
        return $this;
    }

    /**
     * Checks method availability for processing in variable
     *
     * @param Varien_Event_Observer $observer
     * @throws Exception
     * @return Mage_Core_Model_Observer
     */
    public function secureVarProcessing(Varien_Event_Observer $observer)
    {
        if (Mage::registry('varProcessing')) {
            Mage::throwException(Mage::helper('core')->__('Disallowed template variable method.'));
        }
        return $this;
    }
}
