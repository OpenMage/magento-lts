<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Override;
use Mage;
use Mage_Core_Model_Config;
use Mage_Core_Helper_Log as Subject;
use InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Level;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\LogTrait;
use SimpleXMLElement;

final class LogTest extends OpenMageTest
{
    use LogTrait;

    // @phpstan-ignore property.onlyWritten
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/log');
    }

    /**
     * @dataProvider provideGetLogLevelData
     * @covers Mage_Core_Helper_Log::getLogLevelValue()
     * @group Helper
     */
    public function testGetLogLevelValue(int $expectedResult, null|int|Level|string $level): void
    {
        self::assertSame($expectedResult, Subject::getLogLevelValue($level));
    }

    /**
     * @covers Mage_Core_Helper_Log::getConfigAllowedFileExtensions()
     * @group Helper
     */
    public function testGetConfigAllowedFileExtensions(): void
    {
        self::assertIsArray(Subject::getConfigAllowedFileExtensions());
    }

    /**
     * @covers Mage_Core_Helper_Log::getConfigLogFile()
     * @group Helper
     */
    public function testGetConfigLogFile(): void
    {
        self::assertIsString(Subject::getConfigLogFile());
    }

    /**
     * @covers Mage_Core_Helper_Log::getHandler()
     * @group Helper
     */
    public function testGetHandler(): void
    {
        self::assertInstanceOf(StreamHandler::class, Subject::getHandler('somefile.log'));
    }

    /**
     * @covers Mage_Core_Helper_Log::getConfigHandler()
     * @group Helper
     */
    public function testGetConfigHandler(): void
    {
        $config = Mage::getConfig();
        self::assertInstanceOf(Mage_Core_Model_Config::class, $config);

        $path = 'default/' . Subject::XML_PATH_DEV_LOG_HANDLER;
        $original = (string) $config->getNode($path);

        try {
            self::assertSame(Subject::HANDLER_FILE, $original, 'File must stay the default log handler.');

            $config->setNode($path, Subject::HANDLER_SYSLOG_UDP);
            self::assertSame(Subject::HANDLER_SYSLOG_UDP, Subject::getConfigHandler());

            $config->setNode($path, 'unknown');
            self::assertSame(Subject::HANDLER_FILE, Subject::getConfigHandler());
        } finally {
            $config->setNode($path, $original);
        }
    }

    /**
     * @covers Mage_Core_Helper_Log::getSyslogHandler()
     * @group Helper
     */
    public function testGetSyslogHandler(): void
    {
        self::assertInstanceOf(SyslogHandler::class, Subject::getSyslogHandler(Subject::HANDLER_SYSLOG));

        if (extension_loaded('sockets')) {
            self::assertInstanceOf(SyslogUdpHandler::class, Subject::getSyslogHandler(Subject::HANDLER_SYSLOG_UDP));
        }

        $this->expectException(InvalidArgumentException::class);
        // @phpstan-ignore argument.type (an unsupported handler type is the case under test)
        Subject::getSyslogHandler(Subject::HANDLER_FILE);
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
     * @covers Mage_Core_Helper_Log::getLogLevelMaxValue()
     * @group Helper
     */
    public function testGetLogLevelMaxValue(): void
    {
        self::assertIsInt(Subject::getLogLevelMaxValue());
    }

    /**
     * The default from Mage_Core's config.xml is compared on the RFC 5424 scale,
     * so it must be one of the back-end options and must not filter any level.
     *
     * @covers Mage_Core_Helper_Log::getLogLevelValue()
     * @group Helper
     */
    public function testDefaultMaxLogLevelLogsAllLevels(): void
    {
        $config = simplexml_load_file(Mage::getModuleDir('etc', 'Mage_Core') . DS . 'config.xml');
        self::assertInstanceOf(SimpleXMLElement::class, $config);
        $default = (string) $config->default->dev->log->max_level;

        $options = Mage::getModel('adminhtml/system_config_source_log_level')->toOptionArray();
        self::assertArrayHasKey((int) $default, $options);

        self::assertSame(Subject::getLogLevelValue(Level::Debug), Subject::getLogLevelValue($default));
    }

    /**
     * @covers Mage_Core_Helper_Log::getLogFilePath()
     * @dataProvider provideGetLogFilePathData
     * @group Helper
     */
    public function testGetLogFilePath(?string $expectedResult, string $file): void
    {
        if ($expectedResult !== null) {
            $logDir = Mage::getBaseDir('var') . DS . 'log' . DS;
            $expectedResult = $logDir . $expectedResult;
        }

        self::assertSame($expectedResult, Subject::getLogFilePath($file));
    }
}
