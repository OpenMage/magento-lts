<?php

/**
 * Clean session table
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @param Mage_Cron_Model_Schedule $schedule
 * @return $this
 */
class Mage_Api_Model_Cron
{
    public function cleanOldSessions($schedule)
    {
        Mage::getResourceSingleton('api/user')->cleanOldSessions(null);
        return $this;
    }
}
