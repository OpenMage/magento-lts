<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Helper;

use Mage;
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
        Mage::getConfig()->setNode('global/mime/types/test-new-node', 'application/octet-stream');
        $this->subject = Mage::helper('uploader/file');
    }

    /**
     * @dataProvider provideGetMimeTypeFromExtensionListData
     * @param array<int, string> $expectedResult
     * @param string|array<int, string> $extensionsList
     * @return void
     */
    public function testGetMimeTypeFromExtensionList(array $expectedResult, $extensionsList): void
    {
        $this->assertSame($expectedResult, $this->subject->getMimeTypeFromExtensionList($extensionsList));
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

    public function testGetPostMaxSize(): void
    {
        $this->assertIsString($this->subject->getPostMaxSize());
    }

    public function testGetUploadMaxSize(): void
    {
        $this->assertIsString($this->subject->getUploadMaxSize());
    }

    public function testGetDataMaxSize(): void
    {
        $mock = $this->getMockBuilder(Mage_Uploader_Helper_File::class)
            ->setMethods(['getPostMaxSize', 'getUploadMaxSize'])
            ->getMock();

        $mock->expects($this->once())->method('getPostMaxSize')->willReturn('1G');
        $mock->expects($this->once())->method('getUploadMaxSize')->willReturn('1M');
        $this->assertSame('1M', $mock->getDataMaxSize());
    }

    /**
     * @dataProvider provideGetDataMaxSizeInBytesData
     * @param int $expectedResult
     * @param string $maxSize
     * @return void
     */
    public function testGetDataMaxSizeInBytes(int $expectedResult, string $maxSize): void
    {
        $mock = $this->getMockBuilder(Mage_Uploader_Helper_File::class)
            ->setMethods(['getDataMaxSize'])
            ->getMock();

        $mock->expects($this->once())->method('getDataMaxSize')->willReturn($maxSize);
        $this->assertSame($expectedResult, $mock->getDataMaxSizeInBytes());
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
