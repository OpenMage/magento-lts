<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Prepare Log Online Visitors Model
 *
 * @category   Mage
 * @package    Mage_Log
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Log_Model_Resource_Visitor_Online _getResource()
 * @method Mage_Log_Model_Resource_Visitor_Online getResource()
 * @method string getVisitorType()
 * @method $this setVisitorType(string $value)
 * @method int getRemoteAddr()
 * @method $this setRemoteAddr(int $value)
 * @method string getFirstVisitAt()
 * @method $this setFirstVisitAt(string $value)
 * @method string getLastVisitAt()
 * @method $this setLastVisitAt(string $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getLastUrl()
 * @method $this setLastUrl(string $value)
 *
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
     * @return int
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
        $value = intval(Mage::getStoreConfig(self::XML_PATH_ONLINE_INTERVAL));
        if (!$value) {
            $value = Mage_Log_Model_Visitor::DEFAULT_ONLINE_MINUTES_INTERVAL;
        }
        return $value;
    }
}
