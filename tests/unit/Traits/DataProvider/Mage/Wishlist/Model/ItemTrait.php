<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Wishlist\Model;

use Generator;

trait ItemTrait
{
    public static function provideSetQtyData(): Generator
    {
        yield 'positive quantity' => [
            5,
            5,
        ];
        yield 'zero quantity' => [
            0,
            0,
        ];
        yield 'negative quantity' => [
            1,
            -1,
        ];
    }

    public static function provideValidateData(): Generator
    {
        yield 'valid data' => [
            null,
            1,
            1,
        ];
        yield 'missing wishlist ID' => [
            'Cannot specify wishlist.',
            null,
            1,
        ];
        yield 'missing product ID' => [
            'Cannot specify product.',
            1,
            null,
        ];
    }
}
