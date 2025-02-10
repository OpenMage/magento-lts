<?php
/**
 * Core Observer model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Core
 */
class Mage_Core_Model_Observer
{
    /**
     * Check if synchronize process is finished and generate notification message
     *
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

                $date = date(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT);
                Mage::getModel('adminnotification/inbox')->parse([
                    [
                        'severity'      => $severity,
                        'date_added'    => $date,
                        'title'         => $title,
                        'description'   => $description,
                        'url'           => '',
                        'internal'      => true,
                    ],
                ]);

                $flag->setState(Mage_Core_Model_File_Storage_Flag::STATE_NOTIFIED)->save();
            }

            $adminSession->setSyncProcessStopWatch(false);
        }

        return $this;
    }

    /**
     * Cron job method to clean old cache resources
     */
    public function cleanCache(Mage_Cron_Model_Schedule $schedule)
    {
        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_OLD);
        Mage::dispatchEvent('core_clean_cache');
    }

    /**
     * Cleans cache by tags
     *
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
