<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model;

use Generator;

trait ObserverTrait
{
    /**
     * Expected result, applied rule ids, coupon code
     *
     * @return Generator<string, list{bool, null|string, null|string}, void, void>
     */
    public static function provideHasAppliedRulesData(): Generator
    {
        yield 'free shipping rule with coupon' => [true, '3', 'FREESHIP'];
        yield 'free shipping rule without coupon' => [true, '3', null];
        yield 'several rules' => [true, '0,4,7', ''];
        yield 'coupon only' => [true, null, 'FREESHIP'];
        yield 'no rule ids' => [false, '0,', ''];
        yield 'nothing applied' => [false, null, null];
    }
}
