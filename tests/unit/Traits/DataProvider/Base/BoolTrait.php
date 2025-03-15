<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Base;

use Generator;

trait BoolTrait
{
    public function provideBool(): Generator
    {
        yield 'true' => [
            true,
        ];
        yield 'false' => [
            false,
        ];
    }
}
