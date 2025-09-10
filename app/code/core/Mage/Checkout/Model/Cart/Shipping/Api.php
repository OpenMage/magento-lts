<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Shopping cart api
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Cart_Shipping_Api extends Mage_Checkout_Model_Api_Resource
{
    public function __construct()
    {
        $this->_ignoredAttributeCodes['quote_shipping_rate'] = ['address_id', 'created_at', 'updated_at', 'rate_id', 'carrier_sort_order'];
    }

    /**
     * Set an Shipping Method for Shopping Cart
     *
     * @param  int $quoteId
     * @param  string $shippingMethod
     * @param  string|int $store
     * @return bool
     */
    public function setShippingMethod($quoteId, $shippingMethod, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $quoteShippingAddress = $quote->getShippingAddress();
        if (is_null($quoteShippingAddress->getId())) {
            $this->_fault('shipping_address_is_not_set');
        }

        $rate = $quote->getShippingAddress()->collectShippingRates()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            $this->_fault('shipping_method_is_not_available');
        }

        try {
            $quote->getShippingAddress()->setShippingMethod($shippingMethod);
            $quote->collectTotals()->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('shipping_method_is_not_set', $e->getMessage());
        }

        return true;
    }

    /**
     * Get list of available shipping methods
     *
     * @param  int $quoteId
     * @param  int|string $store
     * @return array
     */
    public function getShippingMethodsList($quoteId, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $quoteShippingAddress = $quote->getShippingAddress();
        if (is_null($quoteShippingAddress->getId())) {
            $this->_fault('shipping_address_is_not_set');
        }

        try {
            $quoteShippingAddress->collectShippingRates()->save();
            $groupedRates = $quoteShippingAddress->getGroupedAllShippingRates();

            $ratesResult = [];
            foreach ($groupedRates as $carrierCode => $rates) {
                $carrierName = $carrierCode;
                if (!is_null(Mage::getStoreConfig('carriers/' . $carrierCode . '/title'))) {
                    $carrierName = Mage::getStoreConfig('carriers/' . $carrierCode . '/title');
                }

                foreach ($rates as $rate) {
                    $rateItem = $this->_getAttributes($rate, 'quote_shipping_rate');
                    $rateItem['carrierName'] = $carrierName;
                    $ratesResult[] = $rateItem;
                    unset($rateItem);
                }
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('shipping_methods_list_could_not_be_retrieved', $e->getMessage());
        }

        return $ratesResult;
    }
}
