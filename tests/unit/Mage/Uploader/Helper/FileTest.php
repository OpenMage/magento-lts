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

namespace OpenMage\Tests\Unit\Mage\Uploader\Helper;

use Mage;
use Mage_Core_Model_Config;
use Mage_Uploader_Helper_File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @var Mage_Uploader_Helper_File
     */
    public Mage_Uploader_Helper_File $subject;

    public function setUp(): void
    {
        Mage::app();

        /** @var Mage_Core_Model_Config $config */
        $config = Mage::getConfig();
        $config->setNode('global/mime/types/test-new-node', 'application/octet-stream');
        $this->subject = Mage::helper('uploader/file');
    }

    /**
     * @dataProvider provideGetMimeTypeFromExtensionListData
     * @param array<int, string> $expectedResult
     * @param string|array<int, string> $extensionsList
     * @return void
     *
     * @group Mage_Uploader
     */
    public function testGetMimeTypeFromExtensionList(array $expectedResult, $extensionsList): void
    {
        $this->assertEquals($expectedResult, $this->subject->getMimeTypeFromExtensionList($extensionsList));
    }

    /**
     * @return array<string, array<int, array<int, string>|string>>
     */
    public function provideGetMimeTypeFromExtensionListData(): array
    {
        return [
            'string exists' => [
                [
                    0 => 'application/vnd.lotus-1-2-3'
                ],
                '123'
            ],
            'string not exists' => [
                [
                    0 => 'application/octet-stream'
                ],
                'not-exists'
            ],
            'array' => [
                [
                    0 => 'application/vnd.lotus-1-2-3',
                    1 => 'application/octet-stream',
                    2 => 'application/octet-stream',
                ],
                [
                    '123',
                    'not-exists',
                    'test-new-node',
                ]
            ],
        ];
    }

    /**
     * @group Mage_Uploader
     */
    public function testGetPostMaxSize(): void
    {
        $this->assertIsString($this->subject->getPostMaxSize());
    }

    /**
     * @group Mage_Uploader
     */
    public function testGetUploadMaxSize(): void
    {
        $this->assertIsString($this->subject->getUploadMaxSize());
    }

    /**
     * @group Mage_Uploader
     */
    public function testGetDataMaxSize(): void
    {
        $mock = $this->getMockBuilder(Mage_Uploader_Helper_File::class)
            ->setMethods(['getPostMaxSize', 'getUploadMaxSize'])
            ->getMock();

        $mock->expects($this->once())->method('getPostMaxSize')->willReturn('1G');
        $mock->expects($this->once())->method('getUploadMaxSize')->willReturn('1M');
        $this->assertEquals('1M', $mock->getDataMaxSize());
    }

    /**
     * @dataProvider provideGetDataMaxSizeInBytesData
     * @param int $expectedResult
     * @param string $maxSize
     * @return void
     *
     * @group Mage_Uploader
     */
    public function testGetDataMaxSizeInBytes(int $expectedResult, string $maxSize): void
    {
        $mock = $this->getMockBuilder(Mage_Uploader_Helper_File::class)
            ->setMethods(['getDataMaxSize'])
            ->getMock();

        $mock->expects($this->once())->method('getDataMaxSize')->willReturn($maxSize);
        $this->assertEquals($expectedResult, $mock->getDataMaxSizeInBytes());
    }

    /**
     * @return array<string, array<int, int|string>>
     */
    public function provideGetDataMaxSizeInBytesData(): array
    {
        return [
            'no unit' => [
                1024,
                '1024'
            ],
            'kilobyte' => [
                1024,
                '1K'
            ],
            'megabyte' => [
                1048576,
                '1M'
            ],
            'gigabyte' => [
                1073741824,
                '1G'
            ]
        ];
    }
}
