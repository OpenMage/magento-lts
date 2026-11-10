<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Override;
use Mage;
use Mage_Core_Helper_Log as HelperLog;
use Mage_Core_Model_Config;
use Mage_Core_Model_Logger as Subject;
use Monolog\Level;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\LoggerTrait;

final class LoggerTest extends OpenMageTest
{
    use LoggerTrait;

    private const SYSLOG_CONFIG_PATHS = [
        HelperLog::XML_PATH_DEV_LOG_HANDLER,
        HelperLog::XML_PATH_DEV_LOG_SYSLOG_FACILITY,
        HelperLog::XML_PATH_DEV_LOG_SYSLOG_HOST,
        HelperLog::XML_PATH_DEV_LOG_SYSLOG_PORT,
    ];

    private static Subject $subject;

    /**
     * @var array<string, string>
     */
    private array $originalConfig = [];

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/logger');
    }

    protected function setUp(): void
    {
        parent::setUp();
        foreach (self::SYSLOG_CONFIG_PATHS as $path) {
            $this->originalConfig[$path] = (string) $this->getConfig()->getNode('default/' . $path);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->originalConfig as $path => $value) {
            $this->setConfig($path, $value);
        }

        parent::tearDown();
    }

    /**
     * @dataProvider provideLogData
     * @group Model
     */
    public function testLog($message, $level, $file, $forceLog, $context)
    {
        if (!in_array($file, ['php://stdout', 'php://stderr'], true)) {
            self::$subject->log($message, $level, $file, $forceLog, $context);

            $logDir = Mage::getBaseDir('var') . DS . 'log' . DS;
            $file = $logDir . $file;

            self::assertFileExists($file, 'Log file does not exist.');

            if (file_exists($file)) {
                unlink($file);
            }

            self::assertFileDoesNotExist($file, 'Log file was not deleted.');
        } else {
            self::markTestSkipped('Testing logging to stdout and stderr is not supported at the moment.');
        }
    }

    /**
     * @group Model
     */
    public function testLogToSyslogUdp(): void
    {
        if (!extension_loaded('sockets')) {
            self::markTestSkipped('The sockets extension is required to test the UDP syslog handler.');
        }

        $server = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        self::assertNotFalse($server);
        socket_bind($server, '127.0.0.1', 0);
        socket_getsockname($server, $address, $port);
        socket_set_option($server, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 2, 'usec' => 0]);

        $this->setConfig(HelperLog::XML_PATH_DEV_LOG_HANDLER, HelperLog::HANDLER_SYSLOG_UDP);
        $this->setConfig(HelperLog::XML_PATH_DEV_LOG_SYSLOG_HOST, '127.0.0.1');
        $this->setConfig(HelperLog::XML_PATH_DEV_LOG_SYSLOG_PORT, (string) $port);

        // loggers are cached per file name for the whole process
        $name = uniqid('test-phpunit-udp-');
        self::$subject->log("Test syslog\nmessage", Level::Error, $name . '.log', true);

        $received = socket_recvfrom($server, $datagram, 2048, 0, $address, $port);
        socket_close($server);

        self::assertNotFalse($received, 'No syslog datagram received.');
        self::assertStringStartsWith('<11>1 ', $datagram, 'Expected RFC 5424 header with user.err priority.');
        self::assertStringContainsString(' openmage ', (string) $datagram);
        self::assertStringContainsString($name . '.ERROR: Test syslog message', (string) $datagram);
        self::assertFileDoesNotExist(Mage::getBaseDir('var') . DS . 'log' . DS . $name . '.log');
    }

    /**
     * @group Model
     */
    public function testLogFallsBackToFileWhenSyslogIsMisconfigured(): void
    {
        $this->setConfig(HelperLog::XML_PATH_DEV_LOG_HANDLER, HelperLog::HANDLER_SYSLOG);
        $this->setConfig(HelperLog::XML_PATH_DEV_LOG_SYSLOG_FACILITY, 'invalid');

        // loggers are cached per file name for the whole process
        $name = uniqid('test-phpunit-fallback-') . '.log';
        self::$subject->log('Test fallback message', null, $name, true);

        $file = Mage::getBaseDir('var') . DS . 'log' . DS . $name;
        self::assertFileExists($file);
        $content = (string) file_get_contents($file);
        unlink($file);

        self::assertStringContainsString('Syslog handler unavailable, logging to file instead: Unknown facility value "invalid" given', $content);
        self::assertStringContainsString('Test fallback message', $content);
    }

    private function setConfig(string $path, string $value): void
    {
        $this->getConfig()->setNode('default/' . $path, $value);
    }

    private function getConfig(): Mage_Core_Model_Config
    {
        $config = Mage::getConfig();
        self::assertInstanceOf(Mage_Core_Model_Config::class, $config);

        return $config;
    }
}
