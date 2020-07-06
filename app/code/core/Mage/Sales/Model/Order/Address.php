<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order address model
 *
 * @method Mage_Sales_Model_Resource_Order_Address _getResource()
 * @method Mage_Sales_Model_Resource_Order_Address getResource()
 * @method Mage_Sales_Model_Resource_Order_Address_Collection getCollection()
 *
 * @method string getAddressType()
 * @method $this setAddressType(string $value)
 *
 * @method string getCity()
 * @method $this setCity(string $value)
 * @method string getCompany()
 * @method $this setCompany(string $value)
 * @method string getCountryId()
 * @method $this setCountryId(string $value)
 * @method Mage_Customer_Model_Address getCustomerAddress()
 * @method $this setCustomerAddress(Mage_Customer_Model_Address $value)
 * @method int getCustomerAddressId()
 * @method $this setCustomerAddressId(int $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 *
 * @method string getEmail()
 * @method $this setEmail(string $value)
 *
 * @method string getFax()
 * @method $this setFax(string $value)
 * @method string getFirstname()
 * @method $this setFirstname(string $value)
 *
 * @method string getLastname()
 * @method $this setLastname(string $value)
 *
 * @method string getMiddlename()
 * @method $this setMiddlename(string $value)
 *
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method string getPostcode()
 * @method $this setPostcode(string $value)
 * @method string getPrefix()
 * @method $this setPrefix(string $value)
 *
 * @method int getQuoteAddressId()
 * @method $this setQuoteAddressId(int $value)
 *
 * @method $this setRegionId(int $value)
 * @method $this setRegion(string $value)
 *
 * @method bool getSameAsBilling()
 * @method $this setSameAsBilling(bool $value)
 * @method $this getStoreId(int $value)
 * @method string getSuffix()
 * @method $this setSuffix(string $value)
 *
 * @method string getTelephone()
 * @method $this setTelephone(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $this->_oldFieldsMap = Mage::helper('sales')->getOldFieldMap('order_address');
        return $this;
    }

    /**
     * Set order
     *
     * @param Mage_Sales_Model_Order $order
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
