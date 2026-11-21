<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console;

use Mage;
use OpenMage\Console\Application;
use OpenMage\Console\Command\Cache\CleanCommand;
use OpenMage\Console\Command\Cache\FlushCommand;
use OpenMage\Console\CommandLoader;
use OpenMage\Tests\Unit\OpenMage\Console\Fixtures\HelloCommand;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Console\Tester\ApplicationTester;

final class ApplicationTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testListRunsWithoutStartingMageApp(): void
    {
        $tester = $this->createTester(Application::create());

        self::assertSame(0, $tester->run(['command' => 'list']));
        self::assertStringContainsString('cache:clean', $tester->getDisplay());
        self::assertStringContainsString('cache:flush', $tester->getDisplay());
        self::assertNull((new ReflectionProperty(Mage::class, '_app'))->getValue());
    }

    public function testCoreDeclaresCacheCommandsInModuleConfig(): void
    {
        $application = Application::create();

        self::assertInstanceOf(CleanCommand::class, $application->find('cache:clean'));
        self::assertInstanceOf(FlushCommand::class, $application->find('cache:flush'));
    }

    public function testInvalidDeclarationIsReportedWithoutStoppingTheCli(): void
    {
        $tester = $this->createTester(new Application(new CommandLoader([
            'fixture_missing' => ['name' => 'fixture:missing', 'class' => 'OpenMage\Tests\Missing\Command'],
            'fixture_hello' => ['name' => 'fixture:hello', 'class' => HelloCommand::class],
        ])));

        $status = $tester->run(['command' => 'fixture:hello'], ['capture_stderr_separately' => true]);

        self::assertSame(0, $status);
        self::assertStringContainsString('hello', $tester->getDisplay());
        self::assertStringContainsString('OpenMage\Tests\Missing\Command', $tester->getErrorOutput());
    }

    public function testRootDirOptionIsAccepted(): void
    {
        $tester = $this->createTester(new Application(new CommandLoader([])));

        self::assertSame(0, $tester->run(['command' => 'list', '--root-dir' => dirname(__DIR__, 4)]));
    }

    private function createTester(Application $application): ApplicationTester
    {
        $application->setAutoExit(false);

        return new ApplicationTester($application);
    }
}
