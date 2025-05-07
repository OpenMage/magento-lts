<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Helper\Product;

use Generator;

trait UrlTrait
{
    public function provideFormat(): Generator
    {
        yield 'null' => [
            '',
            null,
        ];
        yield 'string' => [
            'string',
            'string',
        ];
        yield 'umlauts' => [
            'string with aou',
            'string with ÄÖÜ',
        ];
        yield 'at' => [
            'at',
            '@',
        ];
    }
}
