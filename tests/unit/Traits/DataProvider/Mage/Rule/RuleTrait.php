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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Rule;

use Generator;

trait RuleTrait
{
    public function provideValidateData(): Generator
    {
        yield 'empty object' => [
            true,
        ];

        yield 'object with equal date' => [
            true,
            [
                'from_date' => '1',
                'to_date'   => '1',
            ],
        ];
        yield 'object with valid date' => [
            true,
            [
                'from_date' => '1',
                'to_date'   => '2',
            ],
        ];
        yield 'object with invalid date' => [
            [
                0 => 'End Date must be greater than Start Date.',
            ],
            [
                'from_date' => '2',
                'to_date'   => '1',
            ],
        ];
        yield 'object with empty website ids' => [
            [
                0 => 'Websites must be specified.',
            ],
            [
                'website_ids' => '',
            ],
        ];
        yield 'object with not empty website ids' => [
            true,
            [
                'website_ids' => '1',
            ],
        ];
        yield 'object with empty customer group ids' => [
            [
                0 => 'Customer Groups must be specified.',
            ],
            [
                'customer_group_ids' => '',
            ],
        ];
        yield 'object with not empty customer group ids' => [
            true,
            [
                'customer_group_ids' => '1',
            ],
        ];
    }
}
