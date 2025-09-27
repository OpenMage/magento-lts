<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Helper;

use Mage;
use Mage_Core_Model_Config;
use Mage_Uploader_Helper_File as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Uploader\UploaderTrait;

final class FileTest extends OpenMageTest
{
    use UploaderTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        /** @var Mage_Core_Model_Config $config */
        $config = Mage::getConfig();
        $config->setNode('global/mime/types/test-new-node', 'application/octet-stream');
        self::$subject = Mage::helper('uploader/file');
    }

    /**
     * @dataProvider provideGetMimeTypeFromExtensionListData
     * @param array<int, string> $expectedResult
     * @param string|array<int, string> $extensionsList
     *
     * @group Helper
     */
    public function testGetMimeTypeFromExtensionList(array $expectedResult, $extensionsList): void
    {
        static::assertSame($expectedResult, self::$subject->getMimeTypeFromExtensionList($extensionsList));
    }

    /**
     * @group Helper
     */
    public function testGetPostMaxSize(): void
    {
        static::assertIsString(self::$subject->getPostMaxSize());
    }

    /**
     * @group Helper
     */
    public function testGetUploadMaxSize(): void
    {
        static::assertIsString(self::$subject->getUploadMaxSize());
    }

    /**
     * @dataProvider provideGetDataMaxSizeData
     * @group Helper
     */
    public function testGetDataMaxSize(string $expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods, true);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertSame($expectedResult, $mock->getDataMaxSize());
    }

    /**
     * @dataProvider provideGetDataMaxSizeInBytesData
     * @group Helper
     */
    public function testGetDataMaxSizeInBytes(int $expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods, true);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertSame($expectedResult, $mock->getDataMaxSizeInBytes());
    }
}
