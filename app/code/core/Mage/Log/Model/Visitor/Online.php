<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/**
 * Prepare Log Online Visitors Model
 *
 * @package    Mage_Log
 *
 * @method Mage_Log_Model_Resource_Visitor_Online _getResource()
 * @method Mage_Log_Model_Resource_Visitor_Online_Collection getCollection()
 * @method int getCustomerId()
 * @method string getFirstVisitAt()
 * @method string getLastUrl()
 * @method string getLastVisitAt()
 * @method int getRemoteAddr()
 * @method Mage_Log_Model_Resource_Visitor_Online getResource()
 * @method Mage_Log_Model_Resource_Visitor_Online_Collection getResourceCollection()
 * @method string getVisitorType()
 * @method $this setCustomerId(int $value)
 * @method $this setFirstVisitAt(string $value)
 * @method $this setLastUrl(string $value)
 * @method $this setLastVisitAt(string $value)
 * @method $this setRemoteAddr(int $value)
 * @method $this setVisitorType(string $value)
 */
class Mage_Log_Model_Visitor_Online extends Mage_Core_Model_Abstract
{
    public const XML_PATH_ONLINE_INTERVAL      = 'customer/online_customers/online_minutes_interval';

    public const XML_PATH_UPDATE_FREQUENCY     = 'log/visitor/online_update_frequency';

    protected function _construct()
    {
        $this->_init('log/visitor_online');
    }

    /**
     * Prepare Online visitors collection
     *
     * @return $this
     */
    public function prepare()
    {
        $this->_getResource()->prepare($this);
        return $this;
    }

    /**
     * Retrieve last prepare at timestamp
     *
     * @return false|string
     */
    public function getPrepareAt()
    {
        return Mage::app()->loadCache('log_visitor_online_prepare_at');
    }

    /**
     * Set Prepare at timestamp (if time is null, set current timestamp)
     *
     * @param int $time
     * @return $this
     */
    public function setPrepareAt($time = null)
    {
        if (is_null($time)) {
            $time = time();
        }

        Mage::app()->saveCache($time, 'log_visitor_online_prepare_at');
        return $this;
    }

    /**
     * Retrieve data update Frequency in second
     *
     * @return int
     */
    public function getUpdateFrequency()
    {
        return Mage::getStoreConfig(self::XML_PATH_UPDATE_FREQUENCY);
    }

    /**
     * Retrieve Online Interval (in minutes)
     *
     * @return int
     */
    public function getOnlineInterval()
    {
        $value = Mage::getStoreConfigAsInt(self::XML_PATH_ONLINE_INTERVAL);
        if (!$value) {
            $value = Mage_Log_Model_Visitor::DEFAULT_ONLINE_MINUTES_INTERVAL;
        }

        return $value;
    }
}
