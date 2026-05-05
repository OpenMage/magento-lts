<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/**
 * Customer log model
 *
 * @package    Mage_Log
 *
 * @method Mage_Log_Model_Resource_Customer _getResource()
 * @method Mage_Log_Model_Resource_Customer getResource()
 */
class Mage_Log_Model_Customer extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('log/customer');
    }

    public function getCustomerId(): int
    {
        return (int) $this->_getData('customer_id');
    }

    public function getLoginAt(): string
    {
        return (string) $this->_getData('login_at');
    }

    public function getLogoutAt(): ?string
    {
        $value = $this->_getData('logout_at');
        return $value !== null ? (string) $value : null;
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function getVisitorId(): ?int
    {
        $value = $this->_getData('visitor_id');
        return $value !== null ? (int) $value : null;
    }

    public function setCustomerId(int $value): static
    {
        return $this->setData('customer_id', $value);
    }

    public function setLoginAt(string $value): static
    {
        return $this->setData('login_at', $value);
    }

    public function setLogoutAt(?string $value): static
    {
        return $this->setData('logout_at', $value);
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }

    public function setVisitorId(?int $value): static
    {
        return $this->setData('visitor_id', $value);
    }

    /**
     * Load last log by customer id
     *
     * @param  int|Mage_Log_Model_Customer $customer
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
     * @return null|int
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
