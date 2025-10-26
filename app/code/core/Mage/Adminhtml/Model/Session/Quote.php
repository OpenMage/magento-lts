<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml quote session
 *
 * @package    Mage_Adminhtml
 *
 * @method array getAllowQuoteItemsGiftMessage()
 * @method $this setAllowQuoteItemsGiftMessage(array $value)
 * @method string getCurrencyId()
 * @method $this setCurrencyId(string $value)
 * @method bool hasCustomerId()
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method int getCustomerGroupId()
 * @method int|string getOrderId()
 * @method $this setOrderId(int|string $value)
 * @method int|string getQuoteId()
 * @method $this setQuoteId(int|string $value)
 * @method $this setReordered(int|string $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method bool getUseOldShippingMethod(bool $value)
 */
class Mage_Adminhtml_Model_Session_Quote extends Mage_Core_Model_Session_Abstract
{
    public const XML_PATH_DEFAULT_CREATEACCOUNT_GROUP = 'customer/create_account/default_group';

    /**
     * Quote model object
     *
     * @var Mage_Sales_Model_Quote|null
     */
    protected $_quote   = null;

    /**
     * Customer mofrl object
     *
     * @var Mage_Customer_Model_Customer|null
     */
    protected $_customer = null;

    /**
     * Store model object
     *
     * @var Mage_Core_Model_Store|null
     */
    protected $_store   = null;

    /**
     * Order model object
     *
     * @var Mage_Sales_Model_Order|null
     */
    protected $_order   = null;

    public function __construct()
    {
        $this->init('adminhtml_quote');
        if (Mage::app()->isSingleStoreMode()) {
            $this->setStoreId(Mage::app()->getStore(true)->getId());
        }
    }

    /**
     * Retrieve quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (is_null($this->_quote)) {
            $this->_quote = Mage::getModel('sales/quote');
            if ($this->getStoreId() && $this->getQuoteId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->load($this->getQuoteId());
            } elseif ($this->getStoreId() && $this->getCustomerIsGuest()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
                    ->setCustomerIsGuest(true)
                    ->setIsActive(false)
                    ->save();
                $this->setQuoteId($this->_quote->getId());
            } elseif ($this->getStoreId() && $this->hasCustomerId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->setCustomerGroupId(Mage::getStoreConfig(self::XML_PATH_DEFAULT_CREATEACCOUNT_GROUP))
                    ->assignCustomer($this->getCustomer())
                    ->setIsActive(false)
                    ->save();
                $this->setQuoteId($this->_quote->getId());
            }

            $this->_quote->setIgnoreOldQty(true);
            $this->_quote->setIsSuperMode(true);
        }

        return $this->_quote;
    }

    /**
     * Set customer model object
     * To enable quick switch of preconfigured customer
     * @return $this
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Retrieve customer model object
     * @param bool $forceReload
     * @param bool $useSetStore
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer($forceReload = false, $useSetStore = false)
    {
        if (is_null($this->_customer) || $forceReload) {
            $this->_customer = Mage::getModel('customer/customer');
            if ($useSetStore && $this->getStore()->getId()) {
                $this->_customer->setStore($this->getStore());
            }

            if ($customerId = $this->getCustomerId()) {
                $this->_customer->load($customerId);
            }

            if ($this->getCustomerIsGuest()) {
                $this->_customer->setGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
            }
        }

        return $this->_customer;
    }

    /**
     * Retrieve store model object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore($this->getStoreId());
            if ($currencyId = $this->getCurrencyId()) {
                $this->_store->setCurrentCurrencyCode($currencyId);
            }
        }

        return $this->_store;
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('sales/order');
            if ($this->getOrderId()) {
                $this->_order->load($this->getOrderId());
            }
        }

        return $this->_order;
    }
}
