<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * Manages the PayPal Express currency lock — pins the quote's currency fields, the store's current currency
 * for the lifetime of one PayPal attempt, and a session snapshot of the user's prior browsing currency that
 * cancel/success uses to restore.
 */
class Mage_Paypal_Model_Express_CurrencyLock extends Mage_Core_Model_Abstract
{
    /**
     * Lock the current store currency onto the quote and pin the store-current-currency to match. Snapshots
     * the user's prior browsing currency on the first call so cancel/success can restore it.
     */
    public function lock(Mage_Sales_Model_Quote $quote): void
    {
        $store = Mage::app()->getStore();
        $currency = $store->getCurrentCurrency();
        $baseCurrency = $store->getBaseCurrency();
        $currencyCode = $currency->getCode();
        $rate = $baseCurrency->getRate($currency);

        $quote->setStoreId($store->getId())
            ->setBaseCurrencyCode($baseCurrency->getCode())
            ->setStoreCurrencyCode($baseCurrency->getCode())
            ->setQuoteCurrencyCode($currencyCode)
            ->setOrderCurrencyCode($currencyCode)
            ->setBaseToQuoteRate($rate)
            ->setStoreToQuoteRate($rate);

        $session = $this->_getCheckoutSession();
        if ((string) $session->getData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_PRIOR_CURRENCY) === '') {
            $session->setData(
                Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_PRIOR_CURRENCY,
                $store->getCurrentCurrencyCode(),
            );
        }

        // Pin the store's current currency so subsequent collectors (e.g., Shipping_Total::collect, which
        // calls $store->convertPrice()) produce shipping_amount in the quote currency instead of leaking
        // the store's session currency.
        $this->_pinStoreCurrency($store, $currencyCode);
    }

    /**
     * Re-pin the store's current currency to the quote's locked code for the current request — between
     * requests the session currency can drift away from the quote's locked value.
     */
    public function applyTo(Mage_Sales_Model_Quote $quote): void
    {
        $lockedCurrency = $this->_getShortcutState()->getLockedCurrency($quote);
        if ($lockedCurrency === '') {
            return;
        }

        $this->_pinStoreCurrency(Mage::app()->getStore($quote->getStoreId()), $lockedCurrency);
    }

    /**
     * Verify the lock still holds — the quote currency and the store's current currency must both match
     * the value originally locked, otherwise we'd be charging the buyer a different currency than PayPal
     * has on file.
     */
    public function assertHeld(Mage_Sales_Model_Quote $quote): void
    {
        $lockedCurrency = $this->_getShortcutState()->getLockedCurrency($quote);
        $quoteCurrency = $this->_getQuoteCurrencyCode($quote);
        $storeCurrency = (string) Mage::app()->getStore($quote->getStoreId())->getCurrentCurrencyCode();
        if (
            $lockedCurrency === ''
            || $quoteCurrency === ''
            || !hash_equals($lockedCurrency, $quoteCurrency)
            || !hash_equals($lockedCurrency, $storeCurrency)
        ) {
            Mage::throwException(
                Mage::helper('paypal')->__('The store currency changed after PayPal checkout started. Please restart PayPal checkout.'),
            );
        }
    }

    /**
     * Restore the user's pre-express session currency. The lock cookie/session set by
     * setCurrentCurrencyCode() would otherwise persist into general browsing after the express flow ends.
     */
    public function restore(Mage_Sales_Model_Quote $quote): void
    {
        $session = $this->_getCheckoutSession();
        $prior = trim((string) $session->getData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_PRIOR_CURRENCY));
        if ($prior === '') {
            return;
        }

        $this->_pinStoreCurrency(Mage::app()->getStore($quote->getStoreId()), $prior);
        $session->unsetData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_PRIOR_CURRENCY);
    }

    /**
     * Drop the prior-currency snapshot without rewriting the store currency. Used when a stale Buy-Now
     * quote is cleared so the next attempt's lock() takes a fresh snapshot.
     */
    public function forgetPriorSnapshot(): void
    {
        $this->_getCheckoutSession()->unsetData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_PRIOR_CURRENCY);
    }

    private function _pinStoreCurrency(?Mage_Core_Model_Store $store, string $currencyCode): void
    {
        if ($store === null) {
            return;
        }

        $store->setCurrentCurrencyCode($currencyCode);
        // Mage_Core_Model_Store::getCurrentCurrency() caches the Currency model on first read; clear it so
        // the next call re-resolves through the freshly-set code.
        $store->unsetData('current_currency');
    }

    private function _getQuoteCurrencyCode(Mage_Sales_Model_Quote $quote): string
    {
        $currency = $quote->getOrderCurrencyCode();
        if ($currency === null || $currency === '') {
            $currency = $quote->getQuoteCurrencyCode();
        }
        return (string) $currency;
    }

    private function _getShortcutState(): Mage_Paypal_Model_Express_ShortcutState
    {
        /** @var Mage_Paypal_Model_Express_ShortcutState $state */
        $state = Mage::getSingleton('paypal/express_shortcutState');
        return $state;
    }

    private function _getCheckoutSession(): Mage_Checkout_Model_Session
    {
        return Mage::getSingleton('checkout/session');
    }
}
