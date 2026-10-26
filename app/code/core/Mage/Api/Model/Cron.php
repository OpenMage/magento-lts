<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

class Mage_Api_Model_Cron
{
    /**
     * Clean session table
     *
     * @param  Mage_Cron_Model_Schedule $schedule
     * @return $this
     */
    public function cleanOldSessions($schedule)
    {
        Mage::getResourceSingleton('api/user')->cleanOldSessions(null);
        return $this;
    }
}
