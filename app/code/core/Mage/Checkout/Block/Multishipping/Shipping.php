<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Mustishipping checkout shipping
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Multishipping_Shipping extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Get multishipping checkout model
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/type_multishipping');
    }

    /**
     * @return Mage_Sales_Block_Items_Abstract
     */
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('checkout')->__('Shipping Methods') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getAddresses()
    {
        return $this->getCheckout()->getQuote()->getAllShippingAddresses();
    }

    /**
     * @return int|mixed
     */
    public function getAddressCount()
    {
        $count = $this->getData('address_count');
        if (is_null($count)) {
            $count = count($this->getAddresses());
            $this->setData('address_count', $count);
        }
        return $count;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return array|mixed
     * @throws Exception
     */
    public function getAddressItems($address)
    {
        $items = [];
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            $item->setQuoteItem($this->getCheckout()->getQuote()->getItemById($item->getQuoteItemId()));
            $items[] = $item;
        }
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    public function getAddressShippingMethod($address)
    {
        return $address->getShippingMethod();
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return array
     */
    public function getShippingRates($address)
    {
        return $address->getGroupedAllShippingRates();
    }

    /**
     * @param string $carrierCode
     * @return string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/' . $carrierCode . '/title')) {
            return $name;
        }
        return $carrierCode;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    public function getAddressEditUrl($address)
    {
        return $this->getUrl('*/multishipping_address/editShipping', ['id' => $address->getCustomerAddressId()]);
    }

    /**
     * @return string
     */
    public function getItemsEditUrl()
    {
        return $this->getUrl('*/*/backToAddresses');
    }

    /**
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/shippingPost');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/backtoaddresses');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param float $price
     * @param bool $flag
     * @return float
     */
    public function getShippingPrice($address, $price, $flag)
    {
        /** @var Mage_Tax_Helper_Data $helper */
        $helper = $this->helper('tax');
        return $address->getQuote()->getStore()->convertPrice($helper->getShippingPrice($price, $flag, $address), true);
    }
}
