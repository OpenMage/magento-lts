<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Base;

use Generator;

trait NumericStringTrait
{
    public function provideNumericString(): Generator
    {
        yield 'zero' => [
            '0',
        ];
        yield 'non-zero' => [
            '1',
        ];
    }
}
