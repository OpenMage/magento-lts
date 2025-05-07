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

namespace OpenMage\Tests\Unit\Mage\Downloadable\Helper;

use Mage;
use Mage_Downloadable_Helper_File as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Downloadable\DownloadableTrait;
use OpenMage\Tests\Unit\OpenMageTest;

class FileTest extends OpenMageTest
{
    use DownloadableTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('downloadable/file');
    }

    /**
     * @dataProvider provideGetFilePathData
     *
     * @group Helper
     */
    public function testGetFilePath(string $expectedResult, string $path, ?string $file): void
    {
        $result = self::$subject->getFilePath($path, $file);
        static::assertSame($expectedResult, $result);
    }
}
