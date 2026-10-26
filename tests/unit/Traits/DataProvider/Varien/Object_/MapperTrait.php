<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Varien\Object_;

use Generator;
use Varien_Object;

trait MapperTrait
{
    public static function provideAccumulateByMapData(): Generator
    {
        $map = ['key' => 'newKey'];

        yield 'array, array' => [
            [
                'key' => 'targetValue',
                'newKey' => 'sourceValue',
            ],
            ['key' => 'sourceValue'],
            ['key' => 'targetValue'],
            $map,
        ];

        yield 'empty array, array' => [
            [
                'key' => 'targetValue',
            ],
            [],
            ['key' => 'targetValue'],
            $map,
        ];

        yield 'array, empty array' => [
            [
                'newKey' => 'sourceValue',
            ],
            ['key' => 'sourceValue'],
            [],
            $map,
        ];

        yield 'varien, varien' => [
            [
                'key' => 'value',
                'newKey' => 'value',
            ],
            new Varien_Object(['key' => 'value']),
            new Varien_Object(['key' => 'value']),
            $map,
        ];

        yield 'varien, empty varien' => [
            [
                'newKey' => 'value',
            ],
            new Varien_Object(['key' => 'value']),
            new Varien_Object(),
            $map,
        ];

        yield 'empty varien, varien' => [
            [
                'key' => 'value',
            ],
            new Varien_Object(),
            new Varien_Object(['key' => 'value']),
            $map,
        ];
    }
}
