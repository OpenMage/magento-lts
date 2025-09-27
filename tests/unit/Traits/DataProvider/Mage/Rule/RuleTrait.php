<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
                'End Date must be greater than Start Date.',
            ],
            [
                'from_date' => '2',
                'to_date'   => '1',
            ],
        ];
        yield 'object with empty website ids' => [
            [
                'Websites must be specified.',
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
                'Customer Groups must be specified.',
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
