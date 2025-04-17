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
 * @copyright  Copyright (c) 2024-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Helper;

use Mage;
use Mage_Core_Model_Config;
use Mage_Uploader_Helper_File as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Uploader\UploaderTrait;

class FileTest extends OpenMageTest
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

        static::assertInstanceOf(self::$subject::class, $mock);
        static::assertSame($expectedResult, $mock->getDataMaxSize());
    }

    /**
     * @dataProvider provideGetDataMaxSizeInBytesData
     * @group Helper
     */
    public function testGetDataMaxSizeInBytes(int $expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods, true);

        static::assertInstanceOf(self::$subject::class, $mock);
        static::assertSame($expectedResult, $mock->getDataMaxSizeInBytes());
    }
}
