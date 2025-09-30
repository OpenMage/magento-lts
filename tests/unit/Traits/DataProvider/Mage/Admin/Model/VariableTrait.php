<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model;

use Generator;

trait VariableTrait
{
    public function provideValidateAdminVariableData(): Generator
    {
        yield 'test passes' => [
            true,
            [
                'getVariableName' => 'test',
                'getIsAllowed' => '1',
            ],
        ];
        yield 'test error empty' => [
            ['Variable Name is required field.'],
            [
                'getVariableName' => '',
                'getIsAllowed' => '1',
            ],
        ];
        yield 'test error regex' => [
            ['Variable Name is incorrect.'],
            [
                'getVariableName' => '#invalid-name#',
                'getIsAllowed' => '1',
            ],
        ];
        yield 'test error allowed' => [
            ['Is Allowed is required field.'],
            [
                'getVariableName' => 'test',
                'getIsAllowed' => '',
            ],
        ];
    }
}
