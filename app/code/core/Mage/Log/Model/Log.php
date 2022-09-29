<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log Model
 *
 * @category   Mage
 * @package    Mage_Log
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Log_Model_Resource_Log _getResource()
 * @method Mage_Log_Model_Resource_Log getResource()
 * @method string getSessionId()
 * @method Mage_Log_Model_Log setSessionId(string $value)
 * @method string getFirstVisitAt()
 * @method Mage_Log_Model_Log setFirstVisitAt(string $value)
 * @method string getLastVisitAt()
 * @method Mage_Log_Model_Log setLastVisitAt(string $value)
 * @method int getLastUrlId()
 * @method Mage_Log_Model_Log setLastUrlId(int $value)
 * @method int getStoreId()
 * @method Mage_Log_Model_Log setStoreId(int $value)
 */
class Mage_Log_Model_Log extends Mage_Core_Model_Abstract
{
    const XML_LOG_CLEAN_DAYS    = 'system/log/clean_after_day';

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
        return (int)Mage::getStoreConfig(self::XML_LOG_CLEAN_DAYS) * 60 * 60 * 24;
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
