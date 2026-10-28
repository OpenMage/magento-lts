<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Order\Creditmemo\Total;

use Generator;

trait DiscountTrait
{
    /**
     * Expected discount, order shipping, order shipping discount, shipping to refund
     *
     * @return Generator<string, list{float, float, float, float}, void, void>
     */
    public static function provideCollectShippingDiscountData(): Generator
    {
        yield 'shipping with discount, half refunded' => [
            -1.0,
            10.0,
            2.0,
            5.0,
        ];
        yield 'shipping without discount' => [
            0.0,
            10.0,
            0.0,
            5.0,
        ];
        yield 'no shipping refunded' => [
            0.0,
            10.0,
            2.0,
            0.0,
        ];
        yield 'free shipping' => [
            0.0,
            0.0,
            0.0,
            5.0,
        ];
    }
}
