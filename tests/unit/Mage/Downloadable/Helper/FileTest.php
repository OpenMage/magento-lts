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
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Helper;

use Mage;
use Mage_Downloadable_Helper_File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @var Mage_Downloadable_Helper_File
     */
    public Mage_Downloadable_Helper_File $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('downloadable/file');
    }

    /**
     * @dataProvider provideGetFilePathData
     * @param string $expectedResult
     * @param string $path
     * @param string|null $file
     * @return void
     *
     * @group Mage_Downloadable
     */
    public function testGetFilePath(string $expectedResult, string $path, ?string $file): void
    {
        $result = $this->subject->getFilePath($path, $file);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array<string, array<int, string|null>>
     */
    public function provideGetFilePathData(): array
    {
        return [
            'strings path and strings file' => [
                'path' . DS . 'file',
                'path',
                'file'
            ],
            'strings path and strings file with slash' => [
                'path' . DS . 'file',
                'path',
                '/file'
            ],
            'string path and null file' => [
                'path' . DS,
                'path',
                null
            ],
            'string path and empty file' => [
                'path' . DS,
                'path',
                ''
            ],
            'strings path and strings file named 0' => [
                'path' . DS . '0',
                'path',
                '0'
            ],
        ];
    }
}
