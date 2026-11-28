<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Command\Cache;

use Mage;
use Mage_Core_Model_Config;
use Mage_Core_Model_Layout_Update;
use OpenMage\Console\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class CleanCommandTest extends TestCase
{
    private const CONFIG_ENTRY = 'openmage_console_test_config';

    private const LAYOUT_ENTRY = 'openmage_console_test_layout';

    protected function setUp(): void
    {
        Mage::app()->saveCache('config', self::CONFIG_ENTRY, [Mage_Core_Model_Config::CACHE_TAG]);
        Mage::app()->saveCache('layout', self::LAYOUT_ENTRY, [Mage_Core_Model_Layout_Update::LAYOUT_GENERAL_CACHE_TAG]);
    }

    protected function tearDown(): void
    {
        Mage::app()->removeCache(self::CONFIG_ENTRY);
        Mage::app()->removeCache(self::LAYOUT_ENTRY);
    }

    public function testCleansOnlyTheGivenTypes(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute(['type' => ['config'], '--no-reinit' => true]));
        self::assertFalse(Mage::app()->loadCache(self::CONFIG_ENTRY));
        self::assertSame('layout', Mage::app()->loadCache(self::LAYOUT_ENTRY));
        self::assertStringContainsString('config', $tester->getDisplay());
        self::assertStringNotContainsString('layout', $tester->getDisplay());
    }

    public function testCleansAllTypesWithoutArgument(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute([]));
        self::assertFalse(Mage::app()->loadCache(self::CONFIG_ENTRY));
        self::assertFalse(Mage::app()->loadCache(self::LAYOUT_ENTRY));
    }

    public function testUnknownTypeFailsWithoutCleaning(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute(['type' => ['config', 'unknown_type'], '--no-reinit' => true], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString('unknown_type', $tester->getErrorOutput());
        self::assertSame('config', Mage::app()->loadCache(self::CONFIG_ENTRY));
    }

    private function createTester(): CommandTester
    {
        return new CommandTester(Application::create()->find('cache:clean'));
    }
}
