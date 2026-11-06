<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Command;

use Mage;
use OpenMage\Console\Application;
use OpenMage\Console\CommandLoader;
use OpenMage\Tests\Unit\OpenMage\Console\Fixtures\UnreachableDatabaseCommand;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\ApplicationTester;

final class AbstractCommandTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testNotInstalledFailsBeforeStartingMageApp(): void
    {
        (new ReflectionProperty(Mage::class, '_isInstalled'))->setValue(null, false);
        $tester = $this->createTester(Application::create());

        self::assertSame(Command::FAILURE, $tester->run(['command' => 'cache:flush']));
        self::assertStringContainsString('OpenMage is not installed', $tester->getDisplay());
        self::assertStringContainsString('app/etc/local.xml', $tester->getDisplay());
        self::assertNull((new ReflectionProperty(Mage::class, '_app'))->getValue());
    }

    public function testUnreachableDatabaseNamesTheHostOfLocalXml(): void
    {
        if (!Mage::isInstalled()) {
            self::markTestSkipped('Needs an installed OpenMage.');
        }

        $host = (string) Mage::app()->getConfig()->getNode('global/resources/default_setup/connection/host');
        $tester = $this->createTester(new Application(new CommandLoader([
            'fixture_unreachable' => ['name' => 'fixture:unreachable', 'class' => UnreachableDatabaseCommand::class],
        ])));

        self::assertSame(Command::FAILURE, $tester->run(['command' => 'fixture:unreachable']));
        // The error block wraps lines at the terminal width.
        $display = (string) preg_replace('/\s+/', '', $tester->getDisplay());
        $expected = sprintf('Cannot connect to the database on host "%s" set in app/etc/local.xml: ', $host)
            . UnreachableDatabaseCommand::DRIVER_MESSAGE;
        self::assertStringContainsString((string) preg_replace('/\s+/', '', $expected), $display);
        self::assertStringNotContainsString('line', $display);
    }

    private function createTester(Application $application): ApplicationTester
    {
        $application->setAutoExit(false);

        return new ApplicationTester($application);
    }
}
