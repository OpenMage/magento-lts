<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
