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
