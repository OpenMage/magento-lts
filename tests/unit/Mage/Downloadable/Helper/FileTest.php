<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
