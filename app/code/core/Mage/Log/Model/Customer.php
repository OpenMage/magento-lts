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
 * @copyright  Copyright (c) 2019-2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer log model
 *
 * @category   Mage
 * @package    Mage_Log
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Log_Model_Resource_Customer _getResource()
 * @method Mage_Log_Model_Resource_Customer getResource()
 * @method int getVisitorId()
 * @method $this setVisitorId(int $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getLoginAt()
 * @method $this setLoginAt(string $value)
 * @method string getLogoutAt()
 * @method $this setLogoutAt(string $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 */
class Mage_Log_Model_Customer extends Mage_Core_Model_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('log/customer');
    }

    /**
     * Load last log by customer id
     *
     * @param Mage_Log_Model_Customer|int $customer
     * @return $this
     */
    public function loadByCustomer($customer)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        }

        return $this->load($customer, 'customer_id');
    }

    /**
     * Return last login at in Unix time format
     *
     * @return int|null
     */
    public function getLoginAtTimestamp()
    {
        $loginAt = $this->getLoginAt();
        if ($loginAt) {
            return Varien_Date::toTimestamp($loginAt);
        }

        return null;
    }
}
