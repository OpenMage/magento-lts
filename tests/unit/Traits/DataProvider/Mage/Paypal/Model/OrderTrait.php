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

    /**
     * Each case: use the checkout address, the quote is virtual, the shipping address data, expected preference.
     *
     * @return array<string, array{bool, bool, array<string, string>, ?string}>
     */
    public static function provideShippingPreferenceData(): array
    {
        $address = ['firstname' => 'Sandbox', 'lastname' => 'Tester', 'country_id' => 'US'];

        return [
            'shortcut keeps the address picked on PayPal' => [false, false, $address, null],
            'checkout ships to its own address' => [true, false, $address, 'SET_PROVIDED_ADDRESS'],
            'checkout of a virtual quote ships nothing' => [true, true, [], 'NO_SHIPPING'],
            'checkout without a usable address leaves PayPal to ask' => [true, false, ['firstname' => 'Sandbox'], null],
        ];
    }

    /**
     * Each case: the quote's customer email, the billing address email, the email PayPal should get.
     * PayPal answers INVALID_PARAMETER_SYNTAX or INVALID_STRING_LENGTH for the addresses left out here.
     *
     * @return array<string, array{?string, string, ?string}>
     */
    public static function providePayerEmailData(): array
    {
        return [
            'customer email wins' => ['customer@example.com', 'billing@example.com', 'customer@example.com'],
            'guest quote before placement uses the billing email' => [null, 'Guest.Buyer+yes@mail.example.co.uk', 'Guest.Buyer+yes@mail.example.co.uk'],
            'internationalized domain is left out' => [null, 'buyer@bücher.de', null],
            'unicode local part is left out' => [null, 'büyer@example.com', null],
            'over 254 characters is left out' => [null, 'buyer@' . str_repeat(str_repeat('d', 62) . '.', 4) . 'com', null],
        ];
    }
}
