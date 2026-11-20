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
use Mage_Core_Model_Store;
use OpenMage\Console\Application;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class SetCommandTest extends TestCase
{
    use ConfigDataFixture;

    private const PATH = 'openmage_console_test/group/field';

    private const BASE_URL = 'web/unsecure/base_url';

    private const ENCRYPTED = 'carriers/dhl/password';

    private const UNKNOWN_FIELD = 'web/unsecure/base_ur';

    private const CONFIG_PATH_FIELD = 'payment/account/merchant_country';

    private const CONFIG_PATH_TARGET = 'paypal/general/merchant_country';

    private const FILE_FIELD = 'design/head/shortcut_icon';

    private const LOG_PATHS = ['system/log/enabled', 'system/log/time', 'system/log/frequency',
        'crontab/jobs/log_clean/schedule/cron_expr', 'crontab/jobs/log_clean/run/model'];

    protected function setUp(): void
    {
        $this->snapshotConfig([
            self::BASE_URL, self::ENCRYPTED, self::UNKNOWN_FIELD, self::CONFIG_PATH_FIELD, self::CONFIG_PATH_TARGET, self::FILE_FIELD, ...self::LOG_PATHS,
        ]);
    }

    protected function tearDown(): void
    {
        $this->restoreConfig();
    }

    public function testWritesTheDefaultScope(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute(['path' => self::PATH, 'value' => 'one', '--no-reinit' => true]));
        self::assertSame(['default/0' => ['scope' => 'default', 'scope_id' => 0, 'value' => 'one']], $this->storedRows(self::PATH));
        self::assertStringContainsString(self::PATH, $tester->getDisplay());
    }

    public function testReinitMakesTheValueVisible(): void
    {
        $this->createTester()->execute(['path' => self::PATH, 'value' => 'visible']);

        self::assertSame('visible', (string) Mage::app()->getConfig()->getNode('default/' . self::PATH));
    }

    public function testResolvesAStoreCode(): void
    {
        $store = Mage::app()->getStore(1);
        self::assertInstanceOf(Mage_Core_Model_Store::class, $store);

        $this->createTester()->execute([
            'path' => self::PATH, 'value' => 'store', '--scope' => 'stores', '--scope-id' => $store->getCode(), '--no-reinit' => true,
        ]);

        self::assertSame('store', $this->storedRows(self::PATH)['stores/1']['value'] ?? null);
    }

    public function testResolvesAWebsiteCode(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute([
            'path' => self::PATH, 'value' => 'website', '--scope' => 'websites', '--scope-id' => Mage::app()->getWebsite(1)->getCode(),
        ]));
        self::assertSame(['websites/1' => ['scope' => 'websites', 'scope_id' => 1, 'value' => 'website']], $this->storedRows(self::PATH));
        self::assertSame('website', Mage::app()->getWebsite(1)->getConfig(self::PATH));
    }

    public function testUnknownStoreIdNeedsForce(): void
    {
        $tester = $this->createTester();
        try {
            $tester->execute(['path' => self::PATH, 'value' => 'x', '--scope' => 'stores', '--scope-id' => '987654', '--no-reinit' => true]);
            self::fail('An unknown store ID was accepted without --force.');
        } catch (InvalidArgumentException $invalidArgumentException) {
            self::assertStringContainsString('Unknown ID "987654"', $invalidArgumentException->getMessage());
            self::assertSame([], $this->storedRows(self::PATH));
        }

        $tester->execute(['path' => self::PATH, 'value' => 'x', '--scope' => 'stores', '--scope-id' => '987654', '--force' => true, '--no-reinit' => true]);
        self::assertSame(['stores/987654' => ['scope' => 'stores', 'scope_id' => 987654, 'value' => 'x']], $this->storedRows(self::PATH));
    }

    #[DataProvider('provideTruncatedPaths')]
    public function testRejectsAPathOfLessThanThreeSegments(string $path): void
    {
        $before = $this->storedRows($path);

        try {
            $this->createTester()->execute(['path' => $path, 'value' => '1', '--force' => true, '--no-reinit' => true]);
            self::fail('A truncated path was accepted.');
        } catch (InvalidArgumentException $invalidArgumentException) {
            self::assertStringContainsString(sprintf('Invalid path "%s"', $path), $invalidArgumentException->getMessage());
            self::assertSame($before, $this->storedRows($path));
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideTruncatedPaths(): iterable
    {
        yield 'two segments' => ['openmage_console_test/group'];
        yield 'one segment' => ['openmage_console_test'];
        yield 'trailing slash' => ['openmage_console_test/group/'];
        yield 'empty middle segment' => ['openmage_console_test//field'];
        yield 'empty path' => [''];
    }

    public function testConvertsLiteralLineBreaks(): void
    {
        $this->createTester()->execute(['path' => self::PATH, 'value' => 'one\ntwo\r\nthree', '--no-reinit' => true]);

        self::assertSame("one\ntwo\r\nthree", $this->storedRows(self::PATH)['default/0']['value']);
    }

    public function testMagerunScriptRoundTripKeepsLineBreaks(): void
    {
        $stored = "one\ntwo\r\nthree";
        Mage::app()->getConfig()->saveConfig(self::PATH, $stored);

        $getTester = new CommandTester(Application::create()->find('config:get'));
        $getTester->execute(['path' => self::PATH, '--magerun-script' => true]);

        $line = trim($getTester->getDisplay());
        self::assertSame("config:set --scope-id=0 --scope=default -- 'openmage_console_test/group/field' 'one\\ntwo\\r\\nthree'", $line);

        Mage::app()->getConfig()->saveConfig(self::PATH, 'overwritten');
        $this->createTester()->execute(['path' => self::PATH, 'value' => 'one\ntwo\r\nthree', '--no-reinit' => true]);

        self::assertSame($stored, $this->storedRows(self::PATH)['default/0']['value']);
    }

    public function testRejectsAnUnknownScope(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid scope "store"');

        $this->createTester()->execute(['path' => self::PATH, 'value' => 'x', '--scope' => 'store']);
    }

    public function testDefaultScopeOnlyAcceptsScopeIdZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must be 0');

        $this->createTester()->execute(['path' => self::PATH, 'value' => 'x', '--scope-id' => '1']);
    }

    public function testAdminStoreNeedsForce(): void
    {
        $tester = $this->createTester();
        try {
            $tester->execute(['path' => self::PATH, 'value' => 'x', '--scope' => 'stores', '--scope-id' => '0', '--no-reinit' => true]);
            self::fail('Scope ID 0 was accepted without --force.');
        } catch (InvalidArgumentException $invalidArgumentException) {
            self::assertStringContainsString('--force', $invalidArgumentException->getMessage());
            self::assertSame([], $this->storedRows(self::PATH));
        }

        $tester->execute(['path' => self::PATH, 'value' => 'x', '--scope' => 'stores', '--scope-id' => '0', '--force' => true, '--no-reinit' => true]);
        self::assertArrayHasKey('stores/0', $this->storedRows(self::PATH));
    }

    #[DataProvider('provideAdminScopes')]
    public function testAdminCodeNeedsForce(string $scope, string $entity): void
    {
        $tester = $this->createTester();
        try {
            $tester->execute(['path' => self::PATH, 'value' => 'x', '--scope' => $scope, '--scope-id' => 'admin', '--no-reinit' => true]);
            self::fail('The admin code was accepted without --force.');
        } catch (InvalidArgumentException $invalidArgumentException) {
            self::assertSame(
                sprintf('%s "admin" in scope %s is the admin %s (ID 0), use --force to write it anyway.', ucfirst($entity), $scope, $entity),
                $invalidArgumentException->getMessage(),
            );
            self::assertSame([], $this->storedRows(self::PATH));
        }

        $tester->execute(['path' => self::PATH, 'value' => 'x', '--scope' => $scope, '--scope-id' => 'admin', '--force' => true, '--no-reinit' => true]);
        self::assertArrayHasKey($scope . '/0', $this->storedRows(self::PATH));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideAdminScopes(): iterable
    {
        yield 'store' => ['stores', 'store'];
        yield 'website' => ['websites', 'website'];
    }

    public function testNullValueUnlessNoNull(): void
    {
        $tester = $this->createTester();

        $tester->execute(['path' => self::PATH, 'value' => 'NULL', '--no-reinit' => true]);
        self::assertNull($this->storedRows(self::PATH)['default/0']['value']);

        $tester->execute(['path' => self::PATH, 'value' => 'NULL', '--no-null' => true, '--no-reinit' => true]);
        self::assertSame('NULL', $this->storedRows(self::PATH)['default/0']['value']);
    }

    public function testEncryptsOnRequest(): void
    {
        $this->createTester()->execute(['path' => self::PATH, 'value' => 'secret', '--encrypt' => true, '--no-reinit' => true]);

        $stored = (string) $this->storedRows(self::PATH)['default/0']['value'];
        self::assertNotSame('secret', $stored);
        self::assertSame('secret', $this->coreHelper()->decrypt($stored));
    }

    public function testValidateRejectsWhatTheBackendModelRejects(): void
    {
        $before = $this->storedRows(self::BASE_URL);
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute(['path' => self::BASE_URL, 'value' => 'ftp://example.com/', '--validate' => true], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertSame($before, $this->storedRows(self::BASE_URL));
    }

    public function testValidateRejectsAPathThatIsNotASystemField(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute(['path' => self::UNKNOWN_FIELD, 'value' => 'x', '--validate' => true], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString(self::UNKNOWN_FIELD, $tester->getErrorOutput());
        self::assertSame([], $this->storedRows(self::UNKNOWN_FIELD));
    }

    public function testValidateRejectsAPathDeeperThanAField(): void
    {
        $path = self::PATH . '/deeper';
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute(['path' => $path, 'value' => 'x', '--validate' => true], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString($path, $tester->getErrorOutput());
        self::assertSame([], $this->storedRows($path));
    }

    public function testValidateRefusesAFieldSavedUnderAnotherPath(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute(['path' => self::CONFIG_PATH_FIELD, 'value' => 'FR', '--validate' => true], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString(
            sprintf('The field %s is saved under %s: set that path without --validate.', self::CONFIG_PATH_FIELD, self::CONFIG_PATH_TARGET),
            $tester->getErrorOutput(),
        );
        self::assertSame([], $this->storedRows(self::CONFIG_PATH_FIELD));
        self::assertSame([], $this->storedRows(self::CONFIG_PATH_TARGET));
    }

    public function testValidateRefusesAFileField(): void
    {
        $before = $this->storedRows(self::FILE_FIELD);
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute(['path' => self::FILE_FIELD, 'value' => 'favicon.ico', '--validate' => true], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString(
            sprintf('The field %s uploads a file: --validate cannot save it, nothing was saved.', self::FILE_FIELD),
            $tester->getErrorOutput(),
        );
        self::assertSame($before, $this->storedRows(self::FILE_FIELD));
    }

    public function testValidateEncryptsProtectedFields(): void
    {
        $tester = $this->createTester();
        $tester->execute([
            'path' => self::ENCRYPTED, 'value' => 'secret', '--scope' => 'stores', '--scope-id' => '1', '--validate' => true, '--no-reinit' => true,
        ]);

        $stored = (string) ($this->storedRows(self::ENCRYPTED)['stores/1']['value'] ?? '');
        self::assertNotSame('secret', $stored);
        self::assertSame('secret', $this->coreHelper()->decrypt($stored));
        self::assertStringContainsString('(encrypted)', $tester->getDisplay());
        self::assertStringNotContainsString('secret', $tester->getDisplay());
    }

    public function testValidateRefusesASecondEncryption(): void
    {
        $before = $this->storedRows(self::ENCRYPTED);
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute(['path' => self::ENCRYPTED, 'value' => 'secret', '--validate' => true, '--encrypt' => true], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertSame($before, $this->storedRows(self::ENCRYPTED));
    }

    public function testValidateGivesTheOtherFieldsOfTheGroupToTheBackendModel(): void
    {
        Mage::app()->getConfig()->saveConfig('system/log/enabled', '1');
        Mage::app()->getConfig()->saveConfig('system/log/time', '02,30,00');
        Mage::app()->getConfig()->reinit();

        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute(['path' => 'system/log/frequency', 'value' => 'W', '--validate' => true]));
        self::assertSame('30 2 * * 1', $this->storedRows('crontab/jobs/log_clean/schedule/cron_expr')['default/0']['value'] ?? null);
    }

    private function coreHelper(): Mage_Core_Helper_Data
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = Mage::helper('core');

        return $helper;
    }

    private function createTester(): CommandTester
    {
        return new CommandTester(Application::create()->find('config:set'));
    }
}
