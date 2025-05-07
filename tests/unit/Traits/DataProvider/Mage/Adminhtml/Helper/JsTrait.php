<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Helper;

use Generator;

trait JsTrait
{
    public function provideDecodeGridSerializedInput(): Generator
    {
        yield 'w/o keys' => [
            [
                0 => 1,
                1 => 2,
                2 => 3,
                3 => 4,
            ],
            '1&2&3&4',
        ];
        yield 'w/ keys' => [
            [
                1 => [],
                2 => [],
            ],
            '1=1&2=2',
        ];
    }
}
