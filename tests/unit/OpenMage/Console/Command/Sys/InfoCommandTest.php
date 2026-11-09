<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Command\Sys;

use InvalidArgumentException;
use Mage;
use OpenMage\Console\Application;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class InfoCommandTest extends TestCase
{
    public function testPrintsVersionAndRoot(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute([]));
        self::assertStringContainsString(Mage::getOpenMageVersion(), $tester->getDisplay());
        self::assertStringContainsString(Mage::getBaseDir(), $tester->getDisplay());
    }

    public function testKeyPrintsOnlyItsValueCaseInsensitively(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute(['key' => 'VERSION']));
        self::assertSame(Mage::getOpenMageVersion(), trim($tester->getDisplay()));
    }

    public function testUnknownKeyFails(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute(['key' => 'unknown_key'], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString('unknown_key', $tester->getErrorOutput());
    }

    public function testJsonListsNamesAndValues(): void
    {
        $names = array_column($this->executeJson(), 'value', 'name');

        self::assertSame(Mage::getOpenMageVersion(), $names['version']);
        self::assertSame(Mage::isInstalled() ? 'yes' : 'no', $names['installed']);
    }

    public function testNamesAreInSnakeCase(): void
    {
        $names = array_column($this->executeJson(), 'name');

        self::assertContains('magento_version', $names);
        self::assertContains('vendors_core', $names);
        foreach ($names as $name) {
            self::assertMatchesRegularExpression('/^[a-z]+(_[a-z]+)*$/', $name);
        }
    }

    public function testEntityCountsOnlyWhenInstalled(): void
    {
        $names = array_column($this->executeJson(), 'value', 'name');

        self::assertSame(Mage::isInstalled(), array_key_exists('product_count', $names));
        self::assertSame(Mage::isInstalled(), array_key_exists('install_date', $names));
    }

    public function testRunsWithoutInstallation(): void
    {
        $isInstalled = new ReflectionProperty(Mage::class, '_isInstalled');
        $wasInstalled = $isInstalled->getValue();
        $isInstalled->setValue(null, false);
        try {
            $names = array_column($this->executeJson(), 'value', 'name');
        } finally {
            $isInstalled->setValue(null, $wasInstalled);
        }

        self::assertSame('no', $names['installed']);
        self::assertArrayNotHasKey('product_count', $names);
    }

    public function testNeverPrintsTheCryptKey(): void
    {
        $tester = $this->createTester();
        $tester->execute([]);

        self::assertStringNotContainsStringIgnoringCase('crypt', $tester->getDisplay());
        $cryptKey = (string) Mage::app()->getConfig()->getNode('global/crypt/key');
        if ($cryptKey !== '') {
            self::assertStringNotContainsString($cryptKey, $tester->getDisplay());
        }
    }

    public function testUnknownFormatFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown format "xml"');

        $this->createTester()->execute(['--format' => 'xml']);
    }

    /**
     * @return list<array{name: string, value: int|string}>
     */
    private function executeJson(): array
    {
        $tester = $this->createTester();
        self::assertSame(Command::SUCCESS, $tester->execute(['--format' => 'json']));

        /** @var list<array{name: string, value: int|string}> $rows */
        $rows = json_decode($tester->getDisplay(), true, 512, JSON_THROW_ON_ERROR);

        return $rows;
    }

    private function createTester(): CommandTester
    {
        return new CommandTester(Application::create()->find('sys:info'));
    }
}
