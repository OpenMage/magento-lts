<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model;

use Mage;
use Mage_Core_Model_Session;
use Mage_Paypal_Model_Cart;
use Mage_Paypal_Model_Helper;
use Mage_Paypal_Model_Order as Subject;
use Mage_Sales_Model_Quote;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\OrderTrait;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;

final class OrderTest extends OpenMageTest
{
    use OrderTrait;

    private const HELPER_SINGLETON = '_singleton/paypal/helper';

    private const CORE_SESSION_SINGLETON = '_singleton/core/session';

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('paypal/order');
    }

    /**
     * The reconciled purchase unit must satisfy both PayPal amount invariants:
     *  - breakdown.item_total == sum(item.unit_amount * qty)
     *  - item_total + tax_total + shipping + handling - discount == grand_total
     *
     * No synthetic "Rounding Adjustment" line items may be appended.
     *
     * @param list<array{float, float, int}> $items
     * @dataProvider provideRoundingScenarios
     * @group Model
     */
    public function testReconcileCartForPaypalBalances(array $items, ?float $shipping, float $grandTotal, string $currency): void
    {
        $helper = Mage::helper('paypal');
        $itemObjects = [];
        $sumItems = 0.0;
        $sumTax = 0.0;

        foreach ($items as [$price, $tax, $qty]) {
            $builder = ItemBuilder::init(
                'Test item',
                MoneyBuilder::init($currency, $helper->formatPrice($price, $currency))->build(),
                (string) $qty,
            );
            if ($tax > 0) {
                $builder->tax(MoneyBuilder::init($currency, $helper->formatPrice($tax, $currency))->build());
            }

            $itemObjects[] = $builder->build();
            $sumItems += $price * $qty;
            $sumTax   += $tax * $qty;
        }

        $totals = [];
        if ($shipping !== null) {
            $totals[Mage_Paypal_Model_Cart::TOTAL_SHIPPING] = MoneyBuilder::init(
                $currency,
                $helper->formatPrice($shipping, $currency),
            )->build();
        }

        $quote = Mage::getModel('sales/quote');
        $quote->setGrandTotal($grandTotal);

        $cart = $this->createMock(Mage_Paypal_Model_Cart::class);
        $cart->method('getAmounts')->willReturn($totals);
        $cart->method('getAllItems')->willReturn($itemObjects);
        $cart->method('getQuote')->willReturn($quote);

        $result = self::$subject->reconcileCartForPaypal($cart, $currency);
        $totals = $result['totals'];

        // No synthetic line items were appended.
        self::assertCount(count($items), $result['items']);

        // Invariant 1: item_total / tax_total are derived from the line items.
        self::assertSame(
            $helper->formatPrice($sumItems, $currency),
            $totals[Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL]->getValue(),
        );
        self::assertSame(
            $helper->formatPrice($sumTax, $currency),
            $totals[Mage_Paypal_Model_Cart::TOTAL_TAX]->getValue(),
        );

        // discount and handling are mutually exclusive.
        self::assertFalse(
            isset($totals[Mage_Paypal_Model_Cart::TOTAL_DISCOUNT])
            && isset($totals[Mage_Paypal_Model_Cart::TOTAL_HANDLING]),
            'Discount and handling must not both be set.',
        );

        // Invariant 2: the breakdown balances against the grand total.
        $value = static fn(string $key): float => isset($totals[$key])
            ? (float) $totals[$key]->getValue()
            : 0.0;
        $balance = $value(Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL)
            + $value(Mage_Paypal_Model_Cart::TOTAL_TAX)
            + $value(Mage_Paypal_Model_Cart::TOTAL_SHIPPING)
            + $value(Mage_Paypal_Model_Cart::TOTAL_HANDLING)
            - $value(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT);

        self::assertEqualsWithDelta($grandTotal, $balance, 0.001);
    }

    /**
     * @param array<string, string> $addressData
     * @dataProvider provideShippingPreferenceData
     * @group Model
     */
    public function testGetShippingPreference(bool $useQuoteShippingAddress, bool $isVirtual, array $addressData, ?string $expected): void
    {
        $quote = $this->createMock(Mage_Sales_Model_Quote::class);
        $quote->method('isVirtual')->willReturn($isVirtual);
        $quote->method('getShippingAddress')->willReturn(Mage::getModel('sales/quote_address')->setData($addressData));

        self::assertSame($expected, self::$subject->getShippingPreference($quote, $useQuoteShippingAddress));
    }

    /**
     * @group Model
     */
    public function testBuildShippingDetails(): void
    {
        $address = Mage::getModel('sales/quote_address')->setData([
            'firstname' => 'Sandbox',
            'lastname' => 'Tester',
            'street' => "1 Main St\nApt 2",
            'city' => str_repeat('c', 130),
            'region' => 'CA',
            'postcode' => '',
            'country_id' => 'US',
        ]);

        $shipping = json_decode((string) json_encode(self::$subject->buildShippingDetails($address)), true);

        self::assertSame(['full_name' => 'Sandbox Tester'], $shipping['name']);
        self::assertSame([
            'address_line_1' => '1 Main St',
            'address_line_2' => 'Apt 2',
            'admin_area_2' => str_repeat('c', 120),
            'admin_area_1' => 'CA',
            'country_code' => 'US',
        ], $shipping['address']);
    }

    /**
     * The payer address once read street lines through a getter the address does not have, so PayPal got none.
     *
     * @group Model
     */
    public function testBuildPayerFromBillingAddressSendsStreet(): void
    {
        $payer = self::$subject->buildPayerFromBillingAddress($this->createQuoteWithBillingAddress());
        self::assertNotNull($payer);

        $address = json_decode((string) json_encode($payer), true)['address'];
        self::assertSame('1 Main St', $address['address_line_1'] ?? null);
        self::assertSame('Apt 2', $address['address_line_2'] ?? null);
        self::assertSame('CA', $address['admin_area_1'] ?? null);
    }

    /**
     * The payer email was once read from a quote field that does not exist, so PayPal never got one.
     *
     * @dataProvider providePayerEmailData
     * @group Model
     */
    public function testBuildPayerFromBillingAddressEmail(?string $customerEmail, string $billingEmail, ?string $expected): void
    {
        $quote = $this->createQuoteWithBillingAddress(['email' => $billingEmail]);
        if ($customerEmail !== null) {
            $quote->setCustomerEmail($customerEmail);
        }

        $payer = self::$subject->buildPayerFromBillingAddress($quote);
        self::assertNotNull($payer);

        self::assertSame($expected, json_decode((string) json_encode($payer), true)['email_address'] ?? null);
    }

    /**
     * @param array<string, string> $billingData
     */
    private function createQuoteWithBillingAddress(array $billingData = []): Mage_Sales_Model_Quote
    {
        $quote = Mage::getModel('sales/quote');
        $quote->setBillingAddress(Mage::getModel('sales/quote_address')->setData($billingData + [
            'firstname' => 'Sandbox',
            'lastname' => 'Tester',
            'street' => "1 Main St\nApt 2",
            'city' => 'San Jose',
            'region_id' => Mage::getModel('directory/region')->loadByCode('CA', 'US')->getId(),
            'postcode' => '95131',
            'country_id' => 'US',
            'telephone' => '4085551234',
        ]));

        return $quote;
    }

    /**
     * @group Model
     */
    public function testBuildOrderRequestPassesShippingPreferenceToPaypalWallet(): void
    {
        $subject = $this->createPartialMock(Subject::class, ['buildPurchaseUnit', 'buildPayerFromBillingAddress', 'getShippingPreference']);
        $subject->method('buildPurchaseUnit')->willReturn(
            PurchaseUnitRequestBuilder::init(AmountWithBreakdownBuilder::init('USD', '1.00')->build())->build(),
        );
        $subject->method('buildPayerFromBillingAddress')->willReturn(null);
        $subject->method('getShippingPreference')->willReturn('SET_PROVIDED_ADDRESS');

        // The real helper and the return URLs would start a session after PHPUnit has sent output.
        $helper = $this->createMock(Mage_Paypal_Model_Helper::class);
        $helper->method('getPaymentIntent')->willReturn('CAPTURE');
        $session = $this->createMock(Mage_Core_Model_Session::class);
        $session->method('getSessionIdForHost')->willReturn('');
        $singletons = [self::HELPER_SINGLETON => $helper, self::CORE_SESSION_SINGLETON => $session];
        foreach ($singletons as $key => $singleton) {
            Mage::unregister($key);
            Mage::register($key, $singleton);
        }

        try {
            $request = json_decode((string) json_encode($subject->buildOrderRequest(Mage::getModel('sales/quote'), '100000001', 'paypal', true)), true);
        } finally {
            foreach (array_keys($singletons) as $key) {
                Mage::unregister($key);
            }
        }

        self::assertSame('SET_PROVIDED_ADDRESS', $request['payment_source']['paypal']['experience_context']['shipping_preference']);
    }
}
