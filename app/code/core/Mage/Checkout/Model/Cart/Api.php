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
class Mage_Checkout_Model_Cart_Api extends Mage_Checkout_Model_Api_Resource
{
    public function __construct()
    {
        $this->_storeIdSessionField = 'cart_store_id';
        $this->_attributesMap['quote'] = ['quote_id' => 'entity_id'];
        $this->_attributesMap['quote_customer'] = ['customer_id' => 'entity_id'];
        $this->_attributesMap['quote_address'] = ['address_id' => 'entity_id'];
        $this->_attributesMap['quote_payment'] = ['payment_id' => 'entity_id'];
    }

    /**
     * Create new quote for shopping cart
     *
     * @param int|string $store
     * @return int
     */
    public function create($store = null)
    {
        $storeId = $this->_getStoreId($store);

        try {
            /*@var $quote Mage_Sales_Model_Quote*/
            $quote = Mage::getModel('sales/quote');
            $quote->setStoreId($storeId)
                    ->setIsActive(false)
                    ->setIsMultiShipping(false)
                    ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('create_quote_fault', $e->getMessage());
        }

        return (int) $quote->getId();
    }

    /**
     * Retrieve full information about quote
     *
     * @param  int $quoteId
     * @param  int $store
     * @return array
     */
    public function info($quoteId, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        if ($quote->getGiftMessageId() > 0) {
            $quote->setGiftMessage(
                Mage::getSingleton('giftmessage/message')->load($quote->getGiftMessageId())->getMessage(),
            );
        }

        $result = $this->_getAttributes($quote, 'quote');
        $result['shipping_address'] = $this->_getAttributes($quote->getShippingAddress(), 'quote_address');
        $result['billing_address'] = $this->_getAttributes($quote->getBillingAddress(), 'quote_address');
        $result['items'] = [];

        foreach ($quote->getAllItems() as $item) {
            if ($item->getGiftMessageId() > 0) {
                $item->setGiftMessage(
                    Mage::getSingleton('giftmessage/message')->load($item->getGiftMessageId())->getMessage(),
                );
            }

            $result['items'][] = $this->_getAttributes($item, 'quote_item');
        }

        $result['payment'] = $this->_getAttributes($quote->getPayment(), 'quote_payment');

        return $result;
    }

    /**
     * @param int $quoteId
     * @param string|int $store
     * @return array
     */
    public function totals($quoteId, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $totals = $quote->getTotals();

        $totalsResult = [];
        foreach ($totals as $total) {
            $totalsResult[] = [
                'title' => $total->getTitle(),
                'amount' => $total->getValue(),
            ];
        }

        return $totalsResult;
    }

    /**
     * Create an order from the shopping cart (quote)
     *
     * @param  int $quoteId
     * @param  int|string $store
     * @param  array $agreements
     * @return string
     */
    public function createOrder($quoteId, $store = null, $agreements = null)
    {
        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        if (!empty($requiredAgreements)) {
            $diff = array_diff($agreements, $requiredAgreements);
            if (!empty($diff)) {
                $this->_fault('required_agreements_are_not_all');
            }
        }

        $quote = $this->_getQuote($quoteId, $store);
        if ($quote->getIsMultiShipping()) {
            $this->_fault('invalid_checkout_type');
        }

        if ($quote->getCheckoutMethod() == Mage_Checkout_Model_Api_Resource_Customer::MODE_GUEST
                && !Mage::helper('checkout')->isAllowedGuestCheckout($quote, $quote->getStoreId())
        ) {
            $this->_fault('guest_checkout_is_not_enabled');
        }

        /** @var Mage_Checkout_Model_Api_Resource_Customer $customerResource */
        $customerResource = Mage::getModel('checkout/api_resource_customer');
        $isNewCustomer = $customerResource->prepareCustomerForQuote($quote);

        try {
            $quote->collectTotals();
            /** @var Mage_Sales_Model_Service_Quote $service */
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();

            if ($isNewCustomer) {
                try {
                    $customerResource->involveNewCustomer($quote);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            $order = $service->getOrder();
            if ($order) {
                Mage::dispatchEvent(
                    'checkout_type_onepage_save_order_after',
                    ['order' => $order, 'quote' => $quote],
                );

                try {
                    $order->queueNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            Mage::dispatchEvent(
                'checkout_submit_all_after',
                ['order' => $order, 'quote' => $quote],
            );
        } catch (Mage_Core_Exception $e) {
            $this->_fault('create_order_fault', $e->getMessage());
        }

        return $order->getIncrementId();
    }

    /**
     * @param  int $quoteId
     * @param  int|string $store
     * @return array
     */
    public function licenseAgreement($quoteId, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        $storeId = $quote->getStoreId();

        $agreements = [];
        if (Mage::getStoreConfigFlag('checkout/options/enable_agreements')) {
            $agreementsCollection = Mage::getModel('checkout/agreement')->getCollection()
                    ->addStoreFilter($storeId)
                    ->addFieldToFilter('is_active', 1);

            foreach ($agreementsCollection as $_a) {
                /** @var Mage_Checkout_Model_Agreement $_a */
                $agreements[] = $this->_getAttributes($_a, 'quote_agreement');
            }
        }

        return $agreements;
    }
}
