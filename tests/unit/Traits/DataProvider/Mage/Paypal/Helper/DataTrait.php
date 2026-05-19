<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Helper;

trait DataTrait
{
    /**
     * @return array<string, array{int, string}>
     */
    public static function provideCurrencyDecimals(): array
    {
        return [
            'USD has two decimals'    => [2, 'USD'],
            'EUR has two decimals'    => [2, 'EUR'],
            'JPY has zero decimals'   => [0, 'JPY'],
            'HUF has zero decimals'   => [0, 'HUF'],
            'TWD has zero decimals'   => [0, 'TWD'],
            'lowercase is normalized' => [0, 'jpy'],
        ];
    }

    /**
     * @return array<string, array{string, float, string}>
     */
    public static function provideFormatPrice(): array
    {
        return [
            'two-decimal currency'      => ['84.80', 84.8, 'USD'],
            'rounds to two decimals'    => ['84.81', 84.805, 'USD'],
            'zero-decimal currency'     => ['85', 84.805, 'JPY'],
            'zero-decimal no fraction'  => ['1200', 1200.0, 'JPY'],
            'no currency defaults to 2' => ['10.00', 10.0, ''],
        ];
    }
}
