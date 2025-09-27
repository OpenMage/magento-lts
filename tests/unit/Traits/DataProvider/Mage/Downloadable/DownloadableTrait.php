<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Downloadable;

use Generator;

trait DownloadableTrait
{
    public function provideGetFilePathData(): Generator
    {
        yield 'strings path and strings file' => [
            'path' . DS . 'file',
            'path',
            'file',
        ];
        yield 'strings path and strings file with slash' => [
            'path' . DS . 'file',
            'path',
            '/file',
        ];
        yield 'string path and null file' => [
            'path' . DS,
            'path',
            null,
        ];
        yield 'string path and empty file' => [
            'path' . DS,
            'path',
            '',
        ];
        yield 'strings path and strings file named 0' => [
            'path' . DS . '0',
            'path',
            '0',
        ];
    }
}
