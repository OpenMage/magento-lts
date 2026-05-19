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
use Mage_Paypal_Model_Cart;
use Mage_Paypal_Model_Order as Subject;
use Mage_Sales_Model_Quote;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\OrderTrait;
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;

final class OrderTest extends OpenMageTest
{
    use OrderTrait;

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
}
