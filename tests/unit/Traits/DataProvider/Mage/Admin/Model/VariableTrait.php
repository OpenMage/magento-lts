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

/**
 * @phpstan-type ValidateData array{
 *     "variable_name": ?string,
 *     "is_allowed": string
 * }
 */
trait VariableTrait
{
    /**
     * @return Generator<string, list{bool|string[], ValidateData}, void, void>
     */
    public static function provideValidateAdminVariableData(): Generator
    {
        yield 'test passes' => [
            true,
            [
                'variable_name' => 'test',
                'is_allowed' => '1',
            ],
        ];
        yield 'test error empty' => [
            ['Variable Name is required field.'],
            [
                'variable_name' => '',
                'is_allowed' => '1',
            ],
        ];
        yield 'test error regex' => [
            ['Variable Name is incorrect.'],
            [
                'variable_name' => '#invalid-name#',
                'is_allowed' => '1',
            ],
        ];
        yield 'test error allowed' => [
            ['Is Allowed is required field.'],
            [
                'variable_name' => 'test',
                'is_allowed' => '',
            ],
        ];
    }
}
