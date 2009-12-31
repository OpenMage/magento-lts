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
 * @category    Mage
 * @package     Mage_Cron
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Schedule mysql4 resource
 *
 * @category   Mage
 * @package    Mage_Cron
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cron_Model_Mysql4_Schedule extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('cron/schedule', 'schedule_id');
    }

    /**
     * If job is currently in $currentStatus, set it to $newStatus
     * and return true. Otherwise, return false and do not change the job.
     *
     * This method is used to implement locking for cron jobs.
     *
     * @param String $newStatus
     * @param String $currentStatus
     */
    public function trySetJobStatusAtomic($scheduleId, $newStatus, $currentStatus)
    {
        $write = $this->_getWriteAdapter();
        $sql = 'UPDATE ' . $this->getTable('cron/schedule')
                . ' SET status = ' . $write->quote($newStatus)
                . ' WHERE schedule_id =' . $write->quote($scheduleId)
                . ' AND status =  ' . $write->quote($currentStatus);
        $result = $write->query($sql);

        if ($result->rowCount() == 1) {
            return true;
        }
        return false;
    }
}
