<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Sales abstract model
 * Provide date processing functionality
 *
 *
 * @method Mage_Sales_Model_Resource_Order_Abstract _getResource()
 * @method $this setTransactionId(int $value)
 * @method bool getForceUpdateGridRecords()
 */
abstract class Mage_Sales_Model_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Get object store identifier
     *
     * @return int|string|Mage_Core_Model_Store
     */
    abstract public function getStore();

    /**
     * Processing object after save data
     * Updates relevant grid table records.
     *
     * @return Mage_Core_Model_Abstract
     */
    public function afterCommitCallback()
    {
        if (!$this->getForceUpdateGridRecords()) {
            $this->_getResource()->updateGridRecords($this->getId());
        }
        return parent::afterCommitCallback();
    }

    /**
     * Get object created at date affected current active store timezone
     *
     * @return Zend_Date
     */
    public function getCreatedAtDate()
    {
        return Mage::app()->getLocale()->date(
            Varien_Date::toTimestamp($this->getCreatedAt()),
            null,
            null,
            true,
        );
    }

    /**
     * Get object created at date affected with object store timezone
     *
     * @return Zend_Date
     */
    public function getCreatedAtStoreDate()
    {
        return Mage::app()->getLocale()->storeDate(
            $this->getStore(),
            Varien_Date::toTimestamp($this->getCreatedAt()),
            true,
        );
    }
}
