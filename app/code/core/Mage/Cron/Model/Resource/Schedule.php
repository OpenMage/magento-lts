<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Cron
 */

/**
 * Schedule mysql4 resource
 *
 * @category   Mage
 * @package    Mage_Cron
 */
class Mage_Cron_Model_Resource_Schedule extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     *
     */
    public function _construct()
    {
        $this->_init('cron/schedule', 'schedule_id');
    }

    /**
     * If job is currently in $currentStatus, set it to $newStatus
     * and return true. Otherwise, return false and do not change the job.
     * This method is used to implement locking for cron jobs.
     *
     * @param int $scheduleId
     * @param String $newStatus
     * @param String $currentStatus
     * @return bool
     */
    public function trySetJobStatusAtomic($scheduleId, $newStatus, $currentStatus)
    {
        $write = $this->_getWriteAdapter();
        $result = $write->update(
            $this->getTable('cron/schedule'),
            ['status' => $newStatus],
            ['schedule_id = ?' => $scheduleId, 'status = ?' => $currentStatus],
        );
        if ($result == 1) {
            return true;
        }
        return false;
    }
}
