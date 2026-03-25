<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Base;

use Generator;

trait UrlTrait
{
    public function provideUrl(): Generator
    {
        yield 'invalid empty' => [
            false,
            '',
        ];

        yield 'invalid wrong' => [
            false,
            'no-url',
        ];

        yield 'valid' => [
            true,
            'https://example.com',
        ];
    }
}
