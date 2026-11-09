<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Command\Config;

use InvalidArgumentException;
use Mage;
use Mage_Core_Helper_Data;
use OpenMage\Console\Application;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class GetCommandTest extends TestCase
{
    use ConfigDataFixture;

    private const PATH = 'openmage_console_test/group/my_field';

    private const NO_MATCH = 'openmage_console_test/none';

    protected function setUp(): void
    {
        $this->snapshotConfig([]);

        $config = Mage::app()->getConfig();
        $config->saveConfig(self::PATH, 'store', 'stores', 1);
        $config->saveConfig(self::PATH, 'website', 'websites', 1);
        $config->saveConfig(self::PATH, 'default');
        $config->saveConfig('openmage_console_test/group/myXfield', 'underscore match');
        /** @phpstan-ignore argument.type */
        $config->saveConfig('openmage_console_test/group/empty', null);
        $config->saveConfig('openmage_console_test/other/secret', $this->coreHelper()->encrypt('plain'));
    }

    protected function tearDown(): void
    {
        $this->restoreConfig();
    }

    public function testListsEveryScopeInOverrideOrder(): void
    {
        self::assertSame(
            [
                ['path' => self::PATH, 'scope' => 'default', 'scope_id' => 0, 'value' => 'default'],
                ['path' => self::PATH, 'scope' => 'websites', 'scope_id' => 1, 'value' => 'website'],
                ['path' => self::PATH, 'scope' => 'stores', 'scope_id' => 1, 'value' => 'store'],
            ],
            $this->executeJson(['path' => self::PATH]),
        );
    }

    public function testTrailingSlashListsTheChildren(): void
    {
        $paths = array_column($this->executeJson(['path' => 'openmage_console_test/group/']), 'path');

        self::assertSame(
            ['openmage_console_test/group/empty', 'openmage_console_test/group/myXfield', self::PATH, self::PATH, self::PATH],
            $paths,
        );
    }

    public function testWildcardMatchesAnySegment(): void
    {
        $paths = array_column($this->executeJson(['path' => 'openmage_console_test/*/secret']), 'path');

        self::assertSame(['openmage_console_test/other/secret'], $paths);
    }

    public function testFiltersByScopeAndWebsiteCode(): void
    {
        $rows = $this->executeJson(['path' => self::PATH, '--scope' => 'websites', '--scope-id' => Mage::app()->getWebsite(1)->getCode()]);

        self::assertSame(['website'], array_column($rows, 'value'));
    }

    public function testFiltersByScopeIdZero(): void
    {
        $rows = $this->executeJson(['path' => self::PATH, '--scope-id' => '0']);

        self::assertSame(['default'], array_column($rows, 'value'));
    }

    public function testNoMatchInTableFormatSucceedsWithANotice(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute(['path' => self::NO_MATCH], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString(self::NO_MATCH, $tester->getErrorOutput());
    }

    public function testNoMatchInJsonPrintsAnEmptyArray(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute(['path' => self::NO_MATCH, '--format' => 'json'], ['capture_stderr_separately' => true]));
        self::assertSame("[]\n", $tester->getDisplay(true));
        self::assertSame('', $tester->getErrorOutput());
    }

    #[DataProvider('provideSilentNoMatchOptions')]
    public function testNoMatchPrintsNothing(string $option, bool|string $value): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute(['path' => self::NO_MATCH, $option => $value], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertSame('', $tester->getErrorOutput());
    }

    /**
     * @return iterable<string, array{string, bool|string}>
     */
    public static function provideSilentNoMatchOptions(): iterable
    {
        yield 'csv' => ['--format', 'csv'];
        yield 'magerun script' => ['--magerun-script', true];
        yield 'update script' => ['--update-script', true];
    }

    public function testUnknownFormatFailsEvenWithoutMatch(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('xml');

        $this->createTester()->execute(['path' => self::NO_MATCH, '--format' => 'xml']);
    }

    public function testDecrypt(): void
    {
        $rows = $this->executeJson(['path' => 'openmage_console_test/other/secret', '--decrypt' => true]);

        self::assertSame(['plain'], array_column($rows, 'value'));
    }

    public function testDecryptKeepsPlainValuesInJson(): void
    {
        $this->savePlainValues();

        $rows = $this->executeJson(['path' => 'openmage_console_test/other/', '--decrypt' => true]);

        self::assertSame(
            [
                'openmage_console_test/other/flag' => '1',
                'openmage_console_test/other/secret' => 'plain',
                'openmage_console_test/other/token' => 'd41d8cd98f00b204e9800998ecf8427e',
                'openmage_console_test/other/url' => 'http://example.com/',
            ],
            array_column($rows, 'value', 'path'),
        );
    }

    public function testDecryptKeepsPlainValuesInTable(): void
    {
        $this->savePlainValues();

        $tester = $this->createTester();
        self::assertSame(Command::SUCCESS, $tester->execute(['path' => 'openmage_console_test/other/', '--decrypt' => true]));

        $display = $tester->getDisplay();
        self::assertTrue(mb_check_encoding($display, 'UTF-8'));
        self::assertMatchesRegularExpression('#other/secret \| default \| 0 +\| plain #', $display);
        self::assertStringContainsString('http://example.com/', $display);
        self::assertStringContainsString('d41d8cd98f00b204e9800998ecf8427e', $display);
    }

    public function testMagerunScriptEncryptsDecryptedValues(): void
    {
        $this->savePlainValues();

        $tester = $this->createTester();
        $tester->execute(['path' => 'openmage_console_test/other/', '--decrypt' => true, '--magerun-script' => true]);

        $display = $tester->getDisplay();
        self::assertStringContainsString("config:set --encrypt --scope-id=0 --scope=default -- 'openmage_console_test/other/secret' 'plain'", $display);
        self::assertStringContainsString("config:set --scope-id=0 --scope=default -- 'openmage_console_test/other/url' 'http://example.com/'", $display);
        self::assertSame(1, substr_count($display, '--encrypt'));
    }

    public function testMagerunScriptWithoutDecryptKeepsTheStoredValue(): void
    {
        $tester = $this->createTester();
        $tester->execute(['path' => 'openmage_console_test/other/secret', '--magerun-script' => true]);

        self::assertStringNotContainsString('--encrypt', $tester->getDisplay());
        self::assertStringNotContainsString("'plain'", $tester->getDisplay());
    }

    public function testNoMatchWithoutPath(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute(['--scope' => 'stores', '--scope-id' => '987654'], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString('Could not find any configuration value.', $tester->getErrorOutput());
        self::assertStringNotContainsString('""', $tester->getErrorOutput());
    }

    public function testCsvFormat(): void
    {
        $tester = $this->createTester();
        self::assertSame(Command::SUCCESS, $tester->execute(['path' => 'openmage_console_test/group/', '--scope' => 'default', '--format' => 'csv']));

        self::assertSame(
            [
                'path,scope,scope_id,value',
                'openmage_console_test/group/empty,default,0,NULL',
                'openmage_console_test/group/myXfield,default,0,"underscore match"',
                self::PATH . ',default,0,default',
            ],
            explode("\n", trim($tester->getDisplay(true))),
        );
    }

    public function testCodeInScopeIdNeedsScope(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('needs --scope');

        $this->createTester()->execute(['path' => self::PATH, '--scope-id' => Mage::app()->getWebsite(1)->getCode()]);
    }

    public function testNullValue(): void
    {
        self::assertSame([null], array_column($this->executeJson(['path' => 'openmage_console_test/group/empty']), 'value'));

        $tester = $this->createTester();
        $tester->execute(['path' => 'openmage_console_test/group/empty']);
        self::assertStringContainsString('NULL', $tester->getDisplay());
    }

    public function testMagerunScript(): void
    {
        $tester = $this->createTester();
        $tester->execute(['path' => self::PATH, '--magerun-script' => true]);

        self::assertStringContainsString(
            "config:set --scope-id=1 --scope=websites -- 'openmage_console_test/group/my_field' 'website'",
            $tester->getDisplay(),
        );
    }

    public function testUpdateScript(): void
    {
        $tester = $this->createTester();
        $tester->execute(['path' => self::PATH, '--update-script' => true]);

        self::assertStringContainsString("\$installer->setConfigData('openmage_console_test/group/my_field', 'default');", $tester->getDisplay());
        self::assertStringContainsString("\$installer->setConfigData('openmage_console_test/group/my_field', 'store', 'stores', 1);", $tester->getDisplay());
    }

    /**
     * @param  array<string, bool|string>                                                    $input
     * @return list<array{Path: string, Scope: string, 'scope_id': int, Value: null|string}>
     */
    private function executeJson(array $input): array
    {
        $tester = $this->createTester();
        self::assertSame(Command::SUCCESS, $tester->execute([...$input, '--format' => 'json']));

        /** @var list<array{Path: string, Scope: string, 'scope_id': int, Value: null|string}> $rows */
        $rows = json_decode($tester->getDisplay(), true, 512, JSON_THROW_ON_ERROR);

        return $rows;
    }

    /**
     * Plain values next to the encrypted one; the token is valid base64 of whole cipher blocks
     */
    private function savePlainValues(): void
    {
        $config = Mage::app()->getConfig();
        $config->saveConfig('openmage_console_test/other/flag', '1');
        $config->saveConfig('openmage_console_test/other/token', 'd41d8cd98f00b204e9800998ecf8427e');
        $config->saveConfig('openmage_console_test/other/url', 'http://example.com/');
    }

    private function coreHelper(): Mage_Core_Helper_Data
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = Mage::helper('core');

        return $helper;
    }

    private function createTester(): CommandTester
    {
        return new CommandTester(Application::create()->find('config:get'));
    }
}
