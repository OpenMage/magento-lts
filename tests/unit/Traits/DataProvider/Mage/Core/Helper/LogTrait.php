<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper;

use Generator;
use Monolog\Level;

trait LogTrait
{
    public function provideGetLogLevelData(): Generator
    {
        yield 'null' => [
            100,
            null,
        ];
        yield 'empty string' => [
            100,
            '',
        ];
        yield 'string' => [
            100,
            'abc',
        ];
        yield 'string psr3' => [
            200,
            'info',
        ];
        yield 'numeric string' => [
            600,
            '0',
        ];
        yield 'numeric invalid string' => [
            100,
            '999',
        ];
        yield 'int to 7' => [
            600,
            0,
        ];
        yield 'int to 100' => [
            100,
            66,
        ];
        yield 'int invalid' => [
            100,
            999,
        ];
        yield 'monolog level' => [
            100,
            Level::Debug,
        ];
    }
}
