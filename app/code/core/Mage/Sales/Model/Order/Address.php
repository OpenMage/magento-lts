<?php

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
 * @method Mage_Sales_Model_Resource_Order_Address _getResource()
 * @method string getAddressType()
 * @method string getCity()
 * @method Mage_Sales_Model_Resource_Order_Address_Collection getCollection()
 * @method string getCompany()
 * @method string getCountryId()
 * @method Mage_Customer_Model_Address getCustomerAddress()
 * @method int getCustomerAddressId()
 * @method int getCustomerId()
 * @method string getEmail()
 * @method string getFax()
 * @method string getFirstname()
 * @method string getLastname()
 * @method string getMiddlename()
 * @method int getParentId()
 * @method string getPostcode()
 * @method string getPrefix()
 * @method int getQuoteAddressId()
 * @method Mage_Sales_Model_Resource_Order_Address getResource()
 * @method Mage_Sales_Model_Resource_Order_Address_Collection getResourceCollection()
 * @method bool getSameAsBilling()
 * @method $this getStoreId(int $value)
 * @method string getSuffix()
 * @method string getTelephone()
 * @method $this setAddressType(string $value)
 * @method $this setCity(string $value)
 * @method $this setCompany(string $value)
 * @method $this setCountryId(string $value)
 * @method $this setCustomerAddress(Mage_Customer_Model_Address $value)
 * @method $this setCustomerAddressId(int $value)
 * @method $this setCustomerId(int $value)
 * @method $this setEmail(string $value)
 * @method $this setFax(string $value)
 * @method $this setFirstname(string $value)
 * @method $this setLastname(string $value)
 * @method $this setMiddlename(string $value)
 * @method $this setParentId(int $value)
 * @method $this setPostcode(string $value)
 * @method $this setPrefix(string $value)
 * @method $this setQuoteAddressId(int $value)
 * @method $this setRegion(string $value)
 * @method $this setRegionId(int $value)
 * @method $this setSameAsBilling(bool $value)
 * @method $this setSuffix(string $value)
 * @method $this setTelephone(string $value)
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
     * Initialize resource
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
}
