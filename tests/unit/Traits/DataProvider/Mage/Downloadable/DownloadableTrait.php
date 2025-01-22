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
