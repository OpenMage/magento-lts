<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
 */

class Mage_Api_Model_Cron
{
    /**
     * Clean session table
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return $this
     */
    public function cleanOldSessions($schedule)
    {
        Mage::getResourceSingleton('api/user')->cleanOldSessions(null);
        return $this;
    }
}
