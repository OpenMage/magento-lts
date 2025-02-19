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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Uploader;

use Generator;

trait UploaderTrait
{
    public static string $default = 'application/octet-stream';

    public function provideGetMimeTypeFromExtensionListData(): Generator
    {
        yield 'string exists' => [
            [
                0 => 'application/vnd.lotus-1-2-3',
            ],
            '123',
        ];
        yield 'string not exists' => [
            [
                0 => self::$default,
            ],
            'not-exists',
        ];
        yield 'array' => [
            [
                0 => 'application/vnd.lotus-1-2-3',
                1 => self::$default,
                2 => self::$default,
            ],
            [
                '123',
                'not-exists',
                'test-new-node',
            ],
        ];
    }

    public function provideGetDataMaxSizeInBytesData(): Generator
    {
        yield 'no unit' => [
            1024,
            '1024',
        ];
        yield 'kilobyte' => [
            1024,
            '1K',
        ];
        yield 'megabyte' => [
            1048576,
            '1M',
        ];
        yield 'gigabyte' => [
            1073741824,
            '1G',
        ];
    }
}
