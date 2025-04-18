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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model;

use Generator;

trait BlockTrait
{
    public function provideValidateAdminBlockData(): Generator
    {
        $errorIncorrectBlockName = 'Block Name is incorrect.';

        yield 'valid' => [
            true,
            [
                'getBlockName' => 'test/block',
                'getIsAllowed' => '1',
            ],
        ];
        yield 'invalid' => [
            [$errorIncorrectBlockName],
            [
                'getBlockName' => 'Test_Block',
                'getIsAllowed' => '1',
            ],
        ];
        yield 'errors: blank blockname' => [
            [
                0 => 'Block Name is required field.',
                1 => 'Is Allowed is required field.',
            ],
            [
                'getBlockName' => '',
                'getIsAllowed' => '',
            ],
        ];
        yield 'errors: invalid char blockname' => [
            [$errorIncorrectBlockName],
            [
                'getBlockName' => '~',
                'getIsAllowed' => '1',
            ],
        ];
        yield 'errors: invalid blockname' => [
            [$errorIncorrectBlockName],
            [
                'getBlockName' => 'test',
                'getIsAllowed' => '0',
            ],
        ];
        yield 'errors: null blockname' => [
            ['Block Name is required field.'],
            [
                'getBlockName' => null,
                'getIsAllowed' => '1',
            ],
        ];
        yield 'errors: special chars in blockname' => [
            [$errorIncorrectBlockName],
            [
                'getBlockName' => '!@#$%^&*()',
                'getIsAllowed' => '1',
            ],
        ];
        yield 'errors: numeric blockname' => [
            [$errorIncorrectBlockName],
            [
                'getBlockName' => '12345',
                'getIsAllowed' => '1',
            ],
        ];
        yield 'valid: mixed case blockname' => [
            true,
            [
                'getBlockName' => 'Test/Block',
                'getIsAllowed' => '1',
            ],
        ];
    }
}
