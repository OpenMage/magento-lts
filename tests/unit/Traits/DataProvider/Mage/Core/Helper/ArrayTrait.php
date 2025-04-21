<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper;

use Generator;

trait ArrayTrait
{
    public function provideMergeRecursiveWithoutOverwriteNumKeysData(): Generator
    {
        yield 'empty' => [
            [],
            [],
            [],
        ];
        yield 'with base array' => [
            ['a' => 1],
            ['a' => 1],
            [],
        ];
        yield 'with merge array' => [
            ['b' => 1],
            [],
            ['b' => 1],
        ];
        yield 'with base & merge array' => [
            [
                'a' => 1,
                'b' => 1,
            ],
            ['a' => 1],
            ['b' => 1],
        ];
        yield 'with nested merge array' => [
            [
                'a' => 1,
                'b' => [
                    'c' => 1,
                ],
            ],
            [],
            [
                'a' => 1,
                'b' => [
                    'c' => 1,
                ],
            ],
        ];

        yield 'with nested base & nested merge array' => [
            [
                'a' => 1,
                'b' => [
                    'c' => 1,
                ],
            ],
            [
                'a' => [
                    'b' => 1,
                ],
            ],
            [
                'a' => 1,
                'b' => [
                    'c' => 1,
                ],
            ],
        ];
    }
}
