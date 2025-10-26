<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Helper;

use Mage;
use Mage_Downloadable_Helper_File as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Downloadable\DownloadableTrait;
use OpenMage\Tests\Unit\OpenMageTest;

final class FileTest extends OpenMageTest
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
        self::assertSame($expectedResult, $result);
    }
}
