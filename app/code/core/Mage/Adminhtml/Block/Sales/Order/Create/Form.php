<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create sidebar
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_form');
    }

    /**
     * Retrieve url for loading blocks
     * @return string
     */
    public function getLoadBlockUrl()
    {
        return $this->getUrl('*/*/loadBlock');
    }

    /**
     * Retrieve url for form submiting
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * @return string
     */
    public function getCustomerSelectorDisplay()
    {
        $customerId = $this->getCustomerId();
        if (is_null($customerId)) {
            return 'block';
        }
        return 'none';
    }

    /**
     * @return string
     */
    public function getStoreSelectorDisplay()
    {
        $storeId    = $this->getStoreId();
        $customerId = $this->getCustomerId();
        if (!is_null($customerId) && !$storeId) {
            return 'block';
        }
        return 'none';
    }

    /**
     * @return string
     */
    public function getDataSelectorDisplay()
    {
        $storeId    = $this->getStoreId();
        $customerId = $this->getCustomerId();
        if (!is_null($customerId) && $storeId) {
            return 'block';
        }
        return 'none';
    }

    public function getOrderDataJson()
    {
        $data = [];
        if (!is_null($this->getCustomerId())) {
            $data['customer_id'] = $this->getCustomerId();
            $data['addresses'] = [];

            /** @var Mage_Customer_Model_Form $addressForm */
            $addressForm = Mage::getModel('customer/form')
                ->setFormCode('adminhtml_customer_address')
                ->setStore($this->getStore());
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $data['addresses'][$address->getId()] = $addressForm->setEntity($address)
                    ->outputData(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_JSON);
            }
        }
        if (!is_null($this->getStoreId())) {
            $data['store_id'] = $this->getStoreId();
            $currency = Mage::app()->getLocale()->currency($this->getStore()->getCurrentCurrencyCode());
            $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
            $data['currency_symbol'] = $symbol;
            $data['shipping_method_reseted'] = !(bool) $this->getQuote()->getShippingAddress()->getShippingMethod();
            $data['payment_method'] = $this->getQuote()->getPayment()->getMethod();
        }
        return Mage::helper('core')->jsonEncode($data);
    }
}
