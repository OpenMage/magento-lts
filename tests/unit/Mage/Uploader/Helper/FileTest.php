<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Config $config
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Helper;

use Mage;
use Mage_Core_Model_Config;
use Mage_Uploader_Helper_File as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Uploader\UploaderTrait;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    use UploaderTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();

        
        $config = Mage::getConfig();
        $config->setNode('global/mime/types/test-new-node', 'application/octet-stream');
        $this->subject = Mage::helper('uploader/file');
    }

    /**
     * @dataProvider provideGetMimeTypeFromExtensionListData
     * @param array<int, string> $expectedResult
     * @param string|array<int, string> $extensionsList
     *
     * @group Mage_Uploader
     * @group Mage_Uploader_Helper
     */
    public function testGetMimeTypeFromExtensionList(array $expectedResult, $extensionsList): void
    {
        $this->assertSame($expectedResult, $this->subject->getMimeTypeFromExtensionList($extensionsList));
    }

    /**
     * @group Mage_Uploader
     * @group Mage_Uploader_Helper
     */
    public function testGetPostMaxSize(): void
    {
        $this->assertIsString($this->subject->getPostMaxSize());
    }

    /**
     * @group Mage_Uploader
     * @group Mage_Uploader_Helper
     */
    public function testGetUploadMaxSize(): void
    {
        $this->assertIsString($this->subject->getUploadMaxSize());
    }

    /**
     * @group Mage_Uploader
     * @group Mage_Uploader_Helper
     */
    public function testGetDataMaxSize(): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getPostMaxSize', 'getUploadMaxSize'])
            ->getMock();

        $mock->expects($this->once())->method('getPostMaxSize')->willReturn('1G');
        $mock->expects($this->once())->method('getUploadMaxSize')->willReturn('1M');
        $this->assertSame('1M', $mock->getDataMaxSize());
    }

    /**
     * @dataProvider provideGetDataMaxSizeInBytesData
     * @group Mage_Uploader
     * @group Mage_Uploader_Helper
     */
    public function testGetDataMaxSizeInBytes(int $expectedResult, string $maxSize): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getDataMaxSize'])
            ->getMock();

        $mock->expects($this->once())->method('getDataMaxSize')->willReturn($maxSize);
        $this->assertSame($expectedResult, $mock->getDataMaxSizeInBytes());
    }
}
