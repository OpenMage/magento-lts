<?php

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
     */
    public function testGetFilePath(string $expectedResult, string $path, ?string $file): void
    {
        $result = $this->subject->getFilePath($path, $file);
        $this->assertSame($expectedResult, $result);
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
                'file',
            ],
            'strings path and strings file with slash' => [
                'path' . DS . 'file',
                'path',
                '/file',
            ],
            'string path and null file' => [
                'path' . DS,
                'path',
                null,
            ],
            'string path and empty file' => [
                'path' . DS,
                'path',
                '',
            ],
            'strings path and strings file named 0' => [
                'path' . DS . '0',
                'path',
                '0',
            ],
        ];
    }
}
