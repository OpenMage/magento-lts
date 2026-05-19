<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model;

trait OrderTrait
{
    /**
     * Scenarios for reconcileCartForPaypal().
     *
     * Each case: a list of line items ([price, tax, qty]), the shipping total
     * (null means no shipping line is present at all), the order grand total
     * and the currency code.
     *
     * @return array<string, array{list<array{float, float, int}>, ?float, float, string}>
     */
    public static function provideRoundingScenarios(): array
    {
        return [
            'exact match, no adjustment' => [
                [[10.00, 0.00, 1]], 0.00, 10.00, 'USD',
            ],
            'no shipping total present' => [
                [[10.00, 0.00, 1]], null, 10.00, 'USD',
            ],
            'item rounding leaves one cent over' => [
                [[84.81, 0.00, 3]], 0.00, 254.42, 'USD',
            ],
            'grand total exceeds parts (fee) -> handling' => [
                [[100.00, 0.00, 1]], 0.00, 100.50, 'USD',
            ],
            'tax and shipping balance exactly' => [
                [[84.80, 7.00, 1]], 3.53, 95.33, 'USD',
            ],
            'coupon discount folded into discount field' => [
                [[100.00, 0.00, 1]], 0.00, 90.00, 'USD',
            ],
            'multiple items with tax' => [
                [[20.00, 1.65, 2], [9.99, 0.82, 1]], 5.00, 60.93, 'EUR',
            ],
            'zero-decimal currency (JPY)' => [
                [[1200.00, 0.00, 1]], 0.00, 1200.00, 'JPY',
            ],
        ];
    }
}
