<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cron
 */

/**
 * Schedules Collection
 *
 * @package    Mage_Cron
 */
class Mage_Cron_Model_Resource_Schedule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource collection
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
