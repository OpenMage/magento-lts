<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/**
 * Log Model
 *
 * @package    Mage_Log
 *
 * @method Mage_Log_Model_Resource_Log _getResource()
 * @method string                      getFirstVisitAt()
 * @method int                         getLastUrlId()
 * @method string                      getLastVisitAt()
 * @method Mage_Log_Model_Resource_Log getResource()
 * @method string                      getSessionId()
 * @method int                         getStoreId()
 * @method $this                       setFirstVisitAt(string $value)
 * @method $this                       setLastUrlId(int $value)
 * @method $this                       setLastVisitAt(string $value)
 * @method $this                       setSessionId(string $value)
 * @method $this                       setStoreId(int $value)
 */
class Mage_Log_Model_Log extends Mage_Core_Model_Abstract
{
    public const XML_LOG_CLEAN_DAYS    = 'system/log/clean_after_day';

    /**
     * @inheritDoc
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
