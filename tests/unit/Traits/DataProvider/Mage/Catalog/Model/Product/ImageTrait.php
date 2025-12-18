<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Product;

use Generator;

trait ImageTrait
{
    public function provideSetSizeData(): Generator
    {
        yield 'size height and width' => [
            [
                'width' => 200,
                'height' => 100,
            ],
            '200x100',
        ];

        yield 'size width' => [
            [
                'width' => 200,
                'height' => null,
            ],
            '200x',
        ];

        yield 'size height' => [
            [
                'width' => null,
                'height' => 100,
            ],
            'x100',
        ];

        yield 'size value "x' => [
            [
                'width' => null,
                'height' => null,
            ],
            'x',
        ];

        yield 'size value "0x' => [
            [
                'width' => null,
                'height' => null,
            ],
            '0x',
        ];

        yield 'size value "x0' => [
            [
                'width' => null,
                'height' => null,
            ],
            'x0',
        ];

        yield 'size value empty' => [
            [
                'width' => null,
                'height' => null,
            ],
            '',
        ];

        yield 'size value numeric' => [
            [
                'width' => 300,
                'height' => 300,
            ],
            '300',
        ];

        yield 'size value non-numeric' => [
            [
                'width' => null,
                'height' => null,
            ],
            'abc',
        ];
    }
}
