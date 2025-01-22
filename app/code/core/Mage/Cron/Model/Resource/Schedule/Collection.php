<?php

/**
 * @category   Mage
 * @package    Mage_Cron
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
