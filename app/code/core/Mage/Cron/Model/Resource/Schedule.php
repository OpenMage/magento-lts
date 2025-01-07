<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cron
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
