<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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

    public function provideGetDataMaxSizeData(): Generator
    {
        yield 'larger post max size' => [
            '1M',
            [
                'getPostMaxSize' => '1G',
                'getUploadMaxSize' => '1M',
            ],
        ];
        yield 'larger upload max size' => [
            '1M',
            [
                'getPostMaxSize' => '1M',
                'getUploadMaxSize' => '1G',
            ],
        ];
    }

    public function provideGetDataMaxSizeInBytesData(): Generator
    {
        yield 'no unit' => [
            1024,
            [
                'getDataMaxSize' => '1024',
            ],
        ];
        yield 'kilobyte' => [
            1024,
            [
                'getDataMaxSize' => '1K',
            ],
        ];
        yield 'megabyte' => [
            1048576,
            [
                'getDataMaxSize' => '1M',
            ],
        ];
        yield 'gigabyte' => [
            1073741824,
            [
                'getDataMaxSize' => '1G',
            ],
        ];
    }
}
