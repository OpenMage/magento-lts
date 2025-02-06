<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Log
 */

/**
 * Log Model
 *
 * @category   Mage
 * @package    Mage_Log
 *
 * @method Mage_Log_Model_Resource_Log _getResource()
 * @method Mage_Log_Model_Resource_Log getResource()
 * @method string getSessionId()
 * @method $this setSessionId(string $value)
 * @method string getFirstVisitAt()
 * @method $this setFirstVisitAt(string $value)
 * @method string getLastVisitAt()
 * @method $this setLastVisitAt(string $value)
 * @method int getLastUrlId()
 * @method $this setLastUrlId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 */
class Mage_Log_Model_Log extends Mage_Core_Model_Abstract
{
    public const XML_LOG_CLEAN_DAYS    = 'system/log/clean_after_day';

    /**
     * Init Resource Model
     *
     */
    protected function _construct()
    {
        $this->_init('log/log');
    }

    /**
     * @return int
     */
    public function getLogCleanTime()
    {
        return Mage::getStoreConfigAsInt(self::XML_LOG_CLEAN_DAYS) * 60 * 60 * 24;
    }

    /**
     * Clean Logs
     *
     * @return $this
     */
    public function clean()
    {
        $this->getResource()->clean($this);
        return $this;
    }
}
