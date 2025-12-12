<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Log as Subject;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\LogTrait;

final class LogTest extends OpenMageTest
{
    use LogTrait;

    // @phpstan-ignore property.onlyWritten
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/log');
    }

    /**
     * @dataProvider provideGetLogLevelData
     * @covers Mage_Core_Helper_Log::getLogLevel()
     * @covers Mage_Core_Helper_Log::getLogLevelMax()
     * @group Helper
     */
    public function testGetLogLevel(int $expectedResult, null|int|Level|string $level): void
    {
        self::assertSame($expectedResult, Subject::getLogLevel($level));
    }

    /**
     * @covers Mage_Core_Helper_Log::getAllowedFileExtensions()
     * @group Helper
     */
    public function testGetAlowedFileExtensions(): void
    {
        self::assertIsArray(Subject::getAllowedFileExtensions());
    }

    /**
     * @covers Mage_Core_Helper_Log::getLogFile()
     * @group Helper
     */
    public function testGetLogFile(): void
    {
        self::assertIsString(Subject::getLogFile());
    }

    /**
     * @covers Mage_Core_Helper_Log::getHandler()
     * @group Helper
     */
    public function testGetHandler(): void
    {
        self::assertInstanceOf(StreamHandler::class, Subject::getHandler(null, 'somefile.log'));
    }

    /**
     * @covers Mage_Core_Helper_Log::getLineFormatter()
     * @group Helper
     */
    public function testGetLineFormatter(): void
    {
        self::assertInstanceOf(LineFormatter::class, Subject::getLineFormatter());
    }

    /**
     * @covers Mage_Core_Helper_Log::getLogLevelMax()
     * @group Helper
     */
    public function testGetLogLevelMax(): void
    {
        self::assertIsInt(Subject::getLogLevelMax());
    }
}
