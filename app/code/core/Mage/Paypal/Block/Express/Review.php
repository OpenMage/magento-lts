<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal Express review page block.
 */
class Mage_Paypal_Block_Express_Review extends Mage_Core_Block_Template
{
    /**
     * Set the default review template.
     */
    #[Override]
    protected function _construct(): void
    {
        parent::_construct();
        $this->setTemplate('paypal/express/review.phtml');
    }

    /**
     * Retrieve the active checkout quote.
     */
    public function getQuote(): Mage_Sales_Model_Quote
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Retrieve visible quote items.
     *
     * @return Mage_Sales_Model_Quote_Item[]
     */
    public function getItems(): array
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Checks whether the quote is virtual.
     */
    public function isVirtual(): bool
    {
        return $this->getQuote()->isVirtual();
    }

    /**
     * Retrieve the quote billing address.
     */
    public function getBillingAddress(): Mage_Sales_Model_Quote_Address
    {
        return $this->getQuote()->getBillingAddress();
    }

    /**
     * Retrieve the quote shipping address.
     */
    public function getShippingAddress(): Mage_Sales_Model_Quote_Address
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Format a quote address as HTML.
     */
    public function getAddressHtml(Mage_Sales_Model_Quote_Address $address): string
    {
        return (string) $address->format('html');
    }

    /**
     * Retrieve grouped shipping rates.
     *
     * @return array<string, Mage_Sales_Model_Quote_Address_Rate[]>
     */
    public function getShippingRates(): array
    {
        if ($this->isVirtual()) {
            return [];
        }

        $address = $this->getShippingAddress();
        $address->setCollectShippingRates(true)->collectShippingRates();

        return $address->getGroupedAllShippingRates();
    }

    /**
     * Checks whether at least one non-error shipping rate is available.
     */
    public function hasShippingRates(): bool
    {
        foreach ($this->getShippingRates() as $rates) {
            foreach ($rates as $rate) {
                if (!$rate->getErrorMessage()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Resolve carrier display name.
     */
    public function getCarrierName(string $carrierCode): string
    {
        $name = Mage::getStoreConfig('carriers/' . $carrierCode . '/title');
        if ($name !== null) {
            return (string) $name;
        }

        return $carrierCode;
    }

    /**
     * Retrieve the selected shipping method.
     */
    public function getAddressShippingMethod(): string
    {
        return (string) $this->getShippingAddress()->getShippingMethod();
    }

    /**
     * Format a shipping price.
     */
    public function getShippingPrice(float $price, bool $includingTax): string
    {
        return (string) $this->getQuote()->getStore()->convertPrice(
            Mage::helper('tax')->getShippingPrice($price, $includingTax, $this->getShippingAddress()),
            true,
        );
    }

    /**
     * Format a quote item row total.
     */
    public function formatPrice(float $price): string
    {
        return (string) $this->getQuote()->getStore()->formatPrice($price);
    }

    /**
     * Render child totals against the active quote.
     */
    public function getTotalsHtml(): string
    {
        $totals = $this->getChild('totals');
        if ($totals instanceof Mage_Core_Block_Abstract) {
            $totals->setCustomQuote($this->getQuote());
            return $totals->toHtml();
        }

        return '';
    }

    /**
     * Retrieve the PayPal order id for the active attempt.
     */
    public function getPaypalOrderId(): string
    {
        return (string) $this->getQuote()->getPayment()
            ->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID);
    }

    /**
     * Format the current grand total for server-side drift checking.
     */
    public function getGrandTotalForPost(): string
    {
        $currency = $this->getQuote()->getOrderCurrencyCode();
        if ($currency === null || $currency === '') {
            $currency = $this->getQuote()->getQuoteCurrencyCode();
        }

        return Mage::helper('paypal')->formatPrice((float) $this->getQuote()->getGrandTotal(), (string) $currency);
    }

    /**
     * Place order URL.
     */
    public function getPlaceOrderUrl(): string
    {
        return $this->getUrl('paypal/express/placeOrder');
    }

    /**
     * Save shipping method URL.
     */
    public function getSaveShippingMethodUrl(): string
    {
        return $this->getUrl('paypal/express/saveShippingMethod');
    }

    /**
     * JavaScript configuration for the review page.
     *
     * @return array<string, string>
     */
    public function getReviewConfig(): array
    {
        return [
            'formId' => 'paypal-express-review-form',
            'token' => $this->getPaypalOrderId(),
            'formKey' => $this->getFormKey(),
            'saveShippingMethodUrl' => $this->getSaveShippingMethodUrl(),
            'totalsContainerId' => 'paypal-express-review-totals',
            'grandTotalInputId' => 'paypal-express-grand-total',
            'errorContainerId' => 'paypal-express-review-error',
        ];
    }
}
