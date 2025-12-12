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
            Level::Debug->toRFC5424Level(),
            null,
        ];
        yield 'empty string' => [
            Level::Debug->toRFC5424Level(),
            '',
        ];
        yield 'invalid string' => [
            Level::Debug->toRFC5424Level(),
            'abc',
        ];
        yield 'string psr3' => [
            Level::Info->toRFC5424Level(),
            'info',
        ];
        yield 'numeric string 0' => [
            Level::Emergency->toRFC5424Level(),
            '0',
        ];
        yield 'numeric string 200' => [
            Level::Info->toRFC5424Level(),
            '200',
        ];
        yield 'numeric string 999' => [
            Level::Debug->toRFC5424Level(),
            '999',
        ];
        yield 'int 0' => [
            Level::Emergency->toRFC5424Level(),
            0,
        ];
        yield 'int 66' => [
            Level::Debug->toRFC5424Level(),
            66,
        ];
        yield 'int 200' => [
            Level::Info->toRFC5424Level(),
            200,
        ];
        yield 'int 999' => [
            Level::Debug->toRFC5424Level(),
            999,
        ];
        yield 'monolog level' => [
            Level::Debug->toRFC5424Level(),
            Level::Debug,
        ];
    }
}
