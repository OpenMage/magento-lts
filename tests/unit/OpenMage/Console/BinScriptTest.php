<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Runs bin/openmage in a child process to cover how it finds the OpenMage root
 */
final class BinScriptTest extends TestCase
{
    private const STUB_AUTOLOAD = "<?php echo 'autoload loaded' . PHP_EOL;";

    private const STUB_MAGE = "<?php echo 'stub root: ' . dirname(__DIR__) . PHP_EOL; exit(0);";

    private string $tempDir;

    protected function setUp(): void
    {
        $tempDir = sys_get_temp_dir() . '/openmage-bin-' . bin2hex(random_bytes(6));
        mkdir($tempDir);
        $this->tempDir = (string) realpath($tempDir);
    }

    protected function tearDown(): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->tempDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($files as $file) {
            self::assertInstanceOf(SplFileInfo::class, $file);
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }

        rmdir($this->tempDir);
    }

    public function testRootDirOptionStartsTheCli(): void
    {
        [$status, $stdout] = $this->runScript($this->script(), ['--root-dir=' . $this->repositoryRoot(), 'list']);

        self::assertSame(0, $status);
        self::assertStringContainsString('cache:clean', $stdout);
    }

    public function testRootDirOptionWinsOverTheClone(): void
    {
        $this->write('root/app/Mage.php', self::STUB_MAGE);

        [$status, $stdout] = $this->runScript($this->script(), ['--root-dir', $this->tempDir . '/root', 'list']);

        self::assertSame(0, $status);
        self::assertStringContainsString('stub root: ' . $this->tempDir . '/root', $stdout);
    }

    public function testMissingRootIsReported(): void
    {
        [$status, $stdout, $stderr] = $this->runScript($this->script(), ['--root-dir=' . $this->tempDir, 'list']);

        self::assertSame(1, $status);
        self::assertSame('', $stdout);
        self::assertStringContainsString(
            sprintf('No OpenMage installation found in "%s"', $this->tempDir),
            $stderr,
        );
    }

    public function testComposerProxyUsesMagentoRootDirOfTheProject(): void
    {
        $this->createComposerProject();
        $this->write('vendor/bin/openmage', sprintf(
            "<?php\n\$GLOBALS['_composer_bin_dir'] = __DIR__;\n"
            . "\$GLOBALS['_composer_autoload_path'] = __DIR__ . '/..'.'/autoload.php';\n"
            . "return include %s;\n",
            var_export($this->script(), true),
        ));

        [$status, $stdout] = $this->runScript($this->tempDir . '/vendor/bin/openmage', ['list']);

        self::assertSame(0, $status);
        self::assertStringContainsString('autoload loaded', $stdout);
        self::assertStringContainsString('stub root: ' . $this->tempDir . '/www', $stdout);
    }

    public function testPackageScriptUsesMagentoRootDirOfTheProject(): void
    {
        $this->createComposerProject();
        $this->write('vendor/openmage/magento-lts/app/Mage.php', self::STUB_MAGE);
        $this->write('vendor/openmage/magento-lts/bin/openmage', (string) file_get_contents($this->script()));

        [$status, $stdout] = $this->runScript($this->tempDir . '/vendor/openmage/magento-lts/bin/openmage', ['list']);

        self::assertSame(0, $status);
        self::assertStringContainsString('autoload loaded', $stdout);
        self::assertStringContainsString('stub root: ' . $this->tempDir . '/www', $stdout);
    }

    private function repositoryRoot(): string
    {
        return dirname(__DIR__, 4);
    }

    private function script(): string
    {
        return $this->repositoryRoot() . '/bin/openmage';
    }

    private function createComposerProject(): void
    {
        $this->write('composer.json', (string) json_encode(['extra' => ['magento-root-dir' => 'www']]));
        $this->write('vendor/autoload.php', self::STUB_AUTOLOAD);
        $this->write('vendor/composer/installed.json', '{"packages": []}');
        $this->write('www/app/Mage.php', self::STUB_MAGE);
    }

    private function write(string $path, string $content): void
    {
        $file = $this->tempDir . '/' . $path;
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0o777, true);
        }

        file_put_contents($file, $content);
    }

    /**
     * @param  list<string>               $arguments
     * @return array{int, string, string} Exit code, standard output, error output
     */
    private function runScript(string $script, array $arguments): array
    {
        $process = proc_open(
            [PHP_BINARY, $script, ...$arguments],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $this->tempDir,
        );
        self::assertIsResource($process);

        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [proc_close($process), $stdout, $stderr];
    }
}
