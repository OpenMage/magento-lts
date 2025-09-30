<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Helper;

use Generator;

trait DataTrait
{
    public function provideSplitSku(): Generator
    {
        yield 'test #1' => [
            [
                '100',
            ],
            '100',
        ];
        yield 'test #2 w/ length' => [
            [
                '10',
                '0',
            ],
            '100',
            2,
        ];
    }
}
