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
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    use DownloadableTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('downloadable/file');
    }

    /**
     * @dataProvider provideGetFilePathData
     *
     * @group Mage_Downloadable
     * @group Mage_Downloadable_Helper
     */
    public function testGetFilePath(string $expectedResult, string $path, ?string $file): void
    {
        $result = $this->subject->getFilePath($path, $file);
        $this->assertSame($expectedResult, $result);
    }
}
