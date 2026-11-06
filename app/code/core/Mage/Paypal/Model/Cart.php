<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\ItemCategory;

class Mage_Paypal_Model_Cart
{
    public const TOTAL_SUBTOTAL = 'subtotal';

    public const TOTAL_DISCOUNT = 'discount';

    public const TOTAL_TAX = 'tax';

    public const TOTAL_SHIPPING = 'shipping';

    public const TOTAL_HANDLING = 'handling';

    protected null|Mage_Sales_Model_Order|Mage_Sales_Model_Quote $_quote = null;

    /**
     * @var array<string, object>
     */
    protected array $_totals = [];

    /**
     * @var array<int, object>
     */
    protected array $_items = [];

    protected ?string $_currency = null;

    /**
     * Initializes the cart model with a sales entity (order or quote).
     *
     * @throws Mage_Paypal_Model_Exception
     */
    public function __construct(array $params = [])
    {
        $salesEntity = array_shift($params);
        if (
            $salesEntity instanceof Mage_Sales_Model_Order
            || $salesEntity instanceof Mage_Sales_Model_Quote
        ) {
            $this->_quote = $salesEntity;
        } else {
            throw new Mage_Paypal_Model_Exception(
                Mage::helper('paypal')->__('Invalid sales entity provided.'),
            );
        }

        $this->_validateCurrency();
    }

    /**
     * Validates that the quote's currency is supported by PayPal.
     */
    protected function _validateCurrency(): self
    {
        $allowedCurrencies = Mage::getModel('paypal/config')->getAllowedCurrencyCodes();
        $currentCurrency = $this->_quote->getOrderCurrencyCode()
            ?: $this->_quote->getQuoteCurrencyCode();

        if (!in_array($currentCurrency, $allowedCurrencies)) {
            throw new Mage_Paypal_Model_Exception(
                Mage::helper('paypal')->__('Selected currency code (%s) is not supported by PayPal', $currentCurrency),
            );
        }

        $this->_currency = $currentCurrency;
        return $this;
    }

    /**
     * Retrieves all prepared items for the PayPal request. Items are prepared if not already.
     */
    public function getAllItems(): array
    {
        if ($this->_items === []) {
            $this->_prepareItems();
        }

        return $this->_items;
    }

    /**
     * Retrieves all prepared totals (subtotal, tax, shipping, discount). Totals are prepared if not already.
     */
    public function getAmounts(): array
    {
        if ($this->_totals === []) {
            $this->_prepareTotals();
        }

        return $this->_totals;
    }

    /**
     * Prepares the line items from the quote for the PayPal API request.
     */
    protected function _prepareItems(): self
    {
        $helper = Mage::helper('paypal');
        foreach ($this->_quote->getAllVisibleItems() as $item) {
            $taxAmount = $item->getData('tax_amount');
            $qty = $item->getQty();
            $moneyBuilder = MoneyBuilder::init(
                $this->_currency,
                $helper->formatPrice((float) $item->getCalculationPrice(), $this->_currency),
            );

            $taxMoneyBuilder = ($taxAmount > 0)
                ? MoneyBuilder::init($this->_currency, $helper->formatPrice((float) $taxAmount / $qty, $this->_currency))
                : null;

            $itemBuilder = ItemBuilder::init($item->getName(), $moneyBuilder->build(), (string) $qty)
                ->sku($item->getSku())
                ->category($item->getIsVirtual() ? ItemCategory::DIGITAL_GOODS : ItemCategory::PHYSICAL_GOODS);

            if ($taxMoneyBuilder instanceof MoneyBuilder) {
                $itemBuilder->tax($taxMoneyBuilder->build());
            }

            $description = $item->getDescription();
            if ($description) {
                $itemBuilder->description(substr((string) $description, 0, 127));
            }

            $this->_items[] = $itemBuilder->build();
        }

        return $this;
    }

    /**
     * Prepares the cart totals from the quote for the PayPal API request.
     */
    protected function _prepareTotals(): self
    {
        $this->_quote->collectTotals()->save();
        $shippingAddress = $this->_quote->getShippingAddress();
        $totalDiscount = abs((float) ($shippingAddress->getDiscountAmount() ?? 0));
        $helper = Mage::helper('paypal');
        $this->_totals = [
            self::TOTAL_SUBTOTAL => MoneyBuilder::init(
                $this->_currency,
                $helper->formatPrice((float) $this->_quote->getSubtotal(), $this->_currency),
            )->build(),
            self::TOTAL_TAX => MoneyBuilder::init(
                $this->_currency,
                $helper->formatPrice((float) ($shippingAddress->getTaxAmount() ?? 0), $this->_currency),
            )->build(),
            self::TOTAL_SHIPPING => MoneyBuilder::init(
                $this->_currency,
                $helper->formatPrice((float) ($shippingAddress->getShippingAmount() ?? 0), $this->_currency),
            )->build(),
            self::TOTAL_DISCOUNT => MoneyBuilder::init(
                $this->_currency,
                $helper->formatPrice($totalDiscount, $this->_currency),
            )->build(),
        ];

        return $this;
    }

    /**
     * Get the sales entity (quote or order) object associated with this cart.
     */
    public function getQuote(): Mage_Sales_Model_Order|Mage_Sales_Model_Quote
    {
        return $this->_quote;
    }
}
