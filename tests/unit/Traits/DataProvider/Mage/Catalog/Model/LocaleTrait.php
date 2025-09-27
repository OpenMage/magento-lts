<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model;

use Generator;

trait LocaleTrait
{
    public function provideGetNumberData(): Generator
    {
        yield 'array' => [
            1.0,
            [1],
        ];
        yield 'int' => [
            1.0,
            1,
        ];
        yield 'string' => [
            1.0,
            '1',
        ];
        yield 'string comma' => [
            1.0,
            '1,0',
        ];
        yield 'string dot' => [
            1.0,
            '1.0',
        ];
        yield 'null' => [
            null,
            null,
        ];
    }
}
