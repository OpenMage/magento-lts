<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Cron
 */

/**
 * Schedules Collection
 *
 * @category   Mage
 * @package    Mage_Cron
 */
class Mage_Cron_Model_Resource_Schedule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource collection
     *
     */
    public function _construct()
    {
        $this->_init('cron/schedule');
    }

    /**
     * Sort order by scheduled_at time
     *
     * @param string $dir
     * @return $this
     */
    public function orderByScheduledAt($dir = self::SORT_ORDER_ASC)
    {
        $this->getSelect()->order('scheduled_at ' . $dir);
        return $this;
    }
}
