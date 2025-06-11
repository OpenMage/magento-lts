<?php

use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\ItemCategory;

class Mage_Paypal_Model_Cart
{
    const TOTAL_SUBTOTAL = 'subtotal';
    const TOTAL_DISCOUNT = 'discount';
    const TOTAL_TAX = 'tax';
    const TOTAL_SHIPPING = 'shipping';

    protected $_quote = null;
    protected $_totals = [];
    protected $_items = [];
    protected $_currency = null;

    public function __construct($params = [])
    {
        $salesEntity = array_shift($params);
        if (
            is_object($salesEntity)
            && (($salesEntity instanceof Mage_Sales_Model_Order)
                || ($salesEntity instanceof Mage_Sales_Model_Quote))
        ) {
            $this->_quote = $salesEntity;
        } else {
            throw new Exception('Invalid sales entity provided.');
        }
        $this->_validateCurrency();
    }

    protected function _validateCurrency()
    {
        $allowedCurrencies = Mage::getModel('paypal/config')->getAllowedCurrencyCodes();
        $currentCurrency = $this->_quote->getOrderCurrencyCode()
            ?: $this->_quote->getQuoteCurrencyCode();

        if (!in_array($currentCurrency, $allowedCurrencies)) {
            throw new Mage_Core_Exception(
                Mage::helper('paypal')->__('Selected currency code (%s) is not supported by PayPal', $currentCurrency)
            );
        }
        $this->_currency = $currentCurrency;
        return $this;
    }

    public function getAllItems()
    {
        if (empty($this->_items)) {
            $this->_prepareItems();
        }
        return $this->_items;
    }

    public function getAmounts()
    {
        if (empty($this->_totals)) {
            $this->_prepareTotals();
        }
        return $this->_totals;
    }

    protected function _prepareItems()
    {
        foreach ($this->_quote->getAllVisibleItems() as $item) {
            $taxAmount = $item->getData('tax_amount');
            $qty = $item->getQty();
            $moneyBuilder = MoneyBuilder::init(
                $this->_currency,
                number_format($item->getCalculationPrice(), 2, '.', '')
            );

            $taxMoneyBuilder = ($taxAmount > 0)
                ? MoneyBuilder::init($this->_currency, number_format($taxAmount / $qty, 2, '.', ''))
                : null;

            $itemBuilder = ItemBuilder::init($item->getName(), $moneyBuilder->build(), (string) $qty)
                ->sku($item->getSku())
                ->category($item->getIsVirtual() ? ItemCategory::DIGITAL_GOODS : ItemCategory::PHYSICAL_GOODS);

            if ($taxMoneyBuilder) {
                $itemBuilder->tax($taxMoneyBuilder->build());
            }
            $description = $item->getDescription();
            if ($description) {
                $itemBuilder->description(substr($description, 0, 127));
            }
            $this->_items[] = $itemBuilder->build();
        }

        return $this;
    }
    protected function _prepareTotals()
    {
        $this->_quote->collectTotals()->save();
        $shippingAddress = $this->_quote->getShippingAddress();
        $totalDiscount = abs($shippingAddress->getDiscountAmount() ?? 0);
        $this->_totals = [
            self::TOTAL_SUBTOTAL => MoneyBuilder::init(
                $this->_currency,
                number_format($this->_quote->getSubtotal(), 2, '.', '')
            )->build(),
            self::TOTAL_TAX => MoneyBuilder::init(
                $this->_currency,
                number_format($shippingAddress->getTaxAmount() ?? 0, 2, '.', '')
            )->build(),
            self::TOTAL_SHIPPING => MoneyBuilder::init(
                $this->_currency,
                number_format($shippingAddress->getShippingAmount() ?? 0, 2, '.', '')
            )->build(),
            self::TOTAL_DISCOUNT => MoneyBuilder::init(
                $this->_currency,
                number_format($totalDiscount, 2, '.', '')
            )->build()
        ];

        return $this;
    }

    /**
     * Get the quote object associated with this cart.
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }
}
