<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
                'Block Name is required field.',
                'Block Name is incorrect.',
                'Is Allowed is required field.',
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
            [
                'Block Name is required field.',
                $errorIncorrectBlockName,
            ],
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
