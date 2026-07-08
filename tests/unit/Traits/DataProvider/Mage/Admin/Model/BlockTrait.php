<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model;

use Generator;

trait BlockTrait
{
    public static function provideValidateAdminBlockData(): Generator
    {
        $errorIncorrectBlockName = 'Block Name is incorrect.';

        yield 'valid' => [
            true,
            [
                'block_name' => 'test/block',
                'is_allowed' => '1',
            ],
        ];
        yield 'invalid' => [
            [$errorIncorrectBlockName],
            [
                'block_name' => 'Test_Block',
                'is_allowed' => '1',
            ],
        ];
        yield 'errors: blank blockname' => [
            [
                'Block Name is required field.',
                'Is Allowed is required field.',
            ],
            [
                'block_name' => '',
                'is_allowed' => '',
            ],
        ];
        yield 'errors: invalid char blockname' => [
            [$errorIncorrectBlockName],
            [
                'block_name' => '~',
                'is_allowed' => '1',
            ],
        ];
        yield 'errors: invalid blockname' => [
            [$errorIncorrectBlockName],
            [
                'block_name' => 'test',
                'is_allowed' => '0',
            ],
        ];
        yield 'errors: null blockname' => [
            [
                'Block Name is required field.',
            ],
            [
                'block_name' => null,
                'is_allowed' => '1',
            ],
        ];
        yield 'errors: special chars in blockname' => [
            [$errorIncorrectBlockName],
            [
                'block_name' => '!@#$%^&*()',
                'is_allowed' => '1',
            ],
        ];
        yield 'errors: numeric blockname' => [
            [$errorIncorrectBlockName],
            [
                'block_name' => '12345',
                'is_allowed' => '1',
            ],
        ];
        yield 'valid: mixed case blockname' => [
            true,
            [
                'block_name' => 'Test/Block',
                'is_allowed' => '1',
            ],
        ];
    }
}
