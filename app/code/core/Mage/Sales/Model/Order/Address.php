<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales order address model
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Address            _getResource()
 * @method Mage_Sales_Model_Resource_Order_Address_Collection getCollection()
 * @method Mage_Customer_Model_Address                        getCustomerAddress()
 * @method Mage_Sales_Model_Resource_Order_Address            getResource()
 * @method Mage_Sales_Model_Resource_Order_Address_Collection getResourceCollection()
 * @method bool                                               getSameAsBilling()
 * @method $this                                              getStoreId(int $value)
 * @method $this                                              setCustomerAddress(Mage_Customer_Model_Address $value)
 * @method $this                                              setSameAsBilling(bool $value)
 */
class Mage_Sales_Model_Order_Address extends Mage_Customer_Model_Address_Abstract
{
    /**
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    protected $_eventPrefix = 'sales_order_address';

    protected $_eventObject = 'address';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_address');
    }

    /**
     * Init mapping array of short fields to its full names
     *
     * @return $this
     */
    protected function _initOldFieldsMap()
    {
        return $this;
    }

    /**
     * Set order
     *
     * @return $this
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = Mage::getModel('sales/order')->load($this->getParentId());
        }

        return $this->_order;
    }

    /**
     * Before object save manipulations
     *
     * @return $this
     */
    #[Override]
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getOrder()) {
            $this->setParentId($this->getOrder()->getId());
        }

        // Init customer address id if customer address is assigned
        if ($this->getCustomerAddress()) {
            $this->setCustomerAddressId($this->getCustomerAddress()->getId());
        }

        return $this;
    }

    public function getAddressType(): string
    {
        return (string) $this->_getData('address_type');
    }

    public function getCity(): string
    {
        return (string) $this->_getData('city');
    }

    public function getCompany(): string
    {
        return (string) $this->_getData('company');
    }

    public function getCountryId(): string
    {
        return (string) $this->_getData('country_id');
    }

    public function getCustomerAddressId(): int
    {
        return (int) $this->_getData('customer_address_id');
    }

    public function getCustomerId(): int
    {
        return (int) $this->_getData('customer_id');
    }

    public function getEmail(): string
    {
        return (string) $this->_getData('email');
    }

    public function getFax(): string
    {
        return (string) $this->_getData('fax');
    }

    public function getFirstname(): string
    {
        return (string) $this->_getData('firstname');
    }

    public function getLastname(): string
    {
        return (string) $this->_getData('lastname');
    }

    public function getMiddlename(): string
    {
        return (string) $this->_getData('middlename');
    }

    public function getParentId(): int
    {
        return (int) $this->_getData('parent_id');
    }

    public function getPostcode(): string
    {
        return (string) $this->_getData('postcode');
    }

    public function getPrefix(): string
    {
        return (string) $this->_getData('prefix');
    }

    public function getQuoteAddressId(): int
    {
        return (int) $this->_getData('quote_address_id');
    }

    public function getSuffix(): string
    {
        return (string) $this->_getData('suffix');
    }

    public function getTelephone(): string
    {
        return (string) $this->_getData('telephone');
    }

    public function setAddressType(string $value): static
    {
        return $this->setData('address_type', $value);
    }

    public function setCity(string $value): static
    {
        return $this->setData('city', $value);
    }

    public function setCompany(string $value): static
    {
        return $this->setData('company', $value);
    }

    public function setCountryId(string $value): static
    {
        return $this->setData('country_id', $value);
    }

    public function setCustomerAddressId(int $value): static
    {
        return $this->setData('customer_address_id', $value);
    }

    public function setCustomerId(int $value): static
    {
        return $this->setData('customer_id', $value);
    }

    public function setEmail(string $value): static
    {
        return $this->setData('email', $value);
    }

    public function setFax(string $value): static
    {
        return $this->setData('fax', $value);
    }

    public function setFirstname(string $value): static
    {
        return $this->setData('firstname', $value);
    }

    public function setLastname(string $value): static
    {
        return $this->setData('lastname', $value);
    }

    public function setMiddlename(string $value): static
    {
        return $this->setData('middlename', $value);
    }

    public function setParentId(int $value): static
    {
        return $this->setData('parent_id', $value);
    }

    public function setPostcode(string $value): static
    {
        return $this->setData('postcode', $value);
    }

    public function setPrefix(string $value): static
    {
        return $this->setData('prefix', $value);
    }

    public function setQuoteAddressId(int $value): static
    {
        return $this->setData('quote_address_id', $value);
    }

    public function setRegion(string $value): static
    {
        return $this->setData('region', $value);
    }

    public function setRegionId(int $value): static
    {
        return $this->setData('region_id', $value);
    }

    public function setSuffix(string $value): static
    {
        return $this->setData('suffix', $value);
    }

    public function setTelephone(string $value): static
    {
        return $this->setData('telephone', $value);
    }
}
