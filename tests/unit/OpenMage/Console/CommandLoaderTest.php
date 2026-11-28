<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console;

use Mage_Core_Model_Config_Base;
use OpenMage\Console\CommandLoader;
use OpenMage\Tests\Unit\OpenMage\Console\Fixtures\GoodbyeCommand;
use OpenMage\Tests\Unit\OpenMage\Console\Fixtures\HelloCommand;
use OpenMage\Tests\Unit\OpenMage\Console\Fixtures\NotACommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\CommandNotFoundException;

final class CommandLoaderTest extends TestCase
{
    public function testDeclaredCommandsAreListedByName(): void
    {
        $loader = new CommandLoader([
            'fixture_hello' => ['name' => 'fixture:hello', 'class' => HelloCommand::class],
            'fixture_goodbye' => ['name' => 'fixture:goodbye', 'class' => GoodbyeCommand::class],
        ]);

        self::assertSame(['fixture:hello', 'fixture:goodbye'], $loader->getNames());
        self::assertTrue($loader->has('fixture:hello'));
        self::assertFalse($loader->has('fixture:unknown'));
        self::assertSame([], $loader->getErrors());
    }

    public function testGetReturnsCommandNamedAfterItsDeclaration(): void
    {
        $loader = new CommandLoader([
            'fixture_hello' => ['name' => 'fixture:hello', 'class' => '\\' . HelloCommand::class],
        ]);

        $command = $loader->get('fixture:hello');

        self::assertInstanceOf(HelloCommand::class, $command);
        self::assertSame('fixture:hello', $command->getName());
    }

    public function testGetUnknownCommandThrows(): void
    {
        $this->expectException(CommandNotFoundException::class);

        (new CommandLoader([]))->get('fixture:unknown');
    }

    public function testDisabledDeclarationIsSkipped(): void
    {
        $loader = new CommandLoader([
            'fixture_hello' => ['name' => 'fixture:hello', 'class' => HelloCommand::class, 'disabled' => '1'],
            'fixture_goodbye' => ['name' => 'fixture:goodbye', 'class' => GoodbyeCommand::class, 'disabled' => '0'],
        ]);

        self::assertSame(['fixture:goodbye'], $loader->getNames());
        self::assertSame([], $loader->getErrors());
    }

    public function testMissingClassIsReportedAndSkipped(): void
    {
        $loader = new CommandLoader([
            'fixture_missing' => ['name' => 'fixture:missing', 'class' => 'OpenMage\Tests\Missing\Command'],
            'fixture_hello' => ['name' => 'fixture:hello', 'class' => HelloCommand::class],
        ]);

        self::assertSame(['fixture:hello'], $loader->getNames());
        self::assertCount(1, $loader->getErrors());
        self::assertStringContainsString('fixture_missing', $loader->getErrors()[0]);
        self::assertStringContainsString('OpenMage\Tests\Missing\Command', $loader->getErrors()[0]);
    }

    public function testClassThatIsNotACommandIsReportedAndSkipped(): void
    {
        $loader = new CommandLoader([
            'fixture_wrong' => ['name' => 'fixture:wrong', 'class' => NotACommand::class],
        ]);

        self::assertSame([], $loader->getNames());
        self::assertCount(1, $loader->getErrors());
        self::assertStringContainsString(NotACommand::class, $loader->getErrors()[0]);
    }

    public function testIncompleteDeclarationIsReportedAndSkipped(): void
    {
        $loader = new CommandLoader([
            'fixture_no_name' => ['class' => HelloCommand::class],
            'fixture_no_class' => ['name' => 'fixture:no-class'],
            'fixture_empty' => '',
        ]);

        self::assertSame([], $loader->getNames());
        self::assertCount(3, $loader->getErrors());
    }

    public function testSameNameUnderTwoKeysIsReportedAndLastWins(): void
    {
        $loader = new CommandLoader([
            'fixture_hello' => ['name' => 'fixture:hello', 'class' => HelloCommand::class],
            'fixture_goodbye' => ['name' => 'fixture:hello', 'class' => GoodbyeCommand::class],
        ]);

        self::assertSame(['fixture:hello'], $loader->getNames());
        self::assertInstanceOf(GoodbyeCommand::class, $loader->get('fixture:hello'));
        self::assertSame(
            ['Console command "fixture_goodbye" replaces "fixture_hello": both declare the name "fixture:hello".'],
            $loader->getErrors(),
        );
    }

    public function testFromConfigReadsConsoleCommandsNode(): void
    {
        $config = $this->createConfig('
            <fixture_hello>
                <name>fixture:hello</name>
                <class>' . HelloCommand::class . '</class>
            </fixture_hello>
        ');

        $loader = CommandLoader::fromConfig($config);

        self::assertSame(['fixture:hello'], $loader->getNames());
        self::assertInstanceOf(HelloCommand::class, $loader->get('fixture:hello'));
    }

    public function testFromConfigWithoutConsoleNodeIsEmpty(): void
    {
        $config = new Mage_Core_Model_Config_Base('<config><global/></config>');

        self::assertSame([], CommandLoader::fromConfig($config)->getNames());
    }

    public function testLaterModuleReplacesCommandClassByKey(): void
    {
        $config = $this->createConfig('
            <fixture_hello>
                <name>fixture:hello</name>
                <class>' . HelloCommand::class . '</class>
            </fixture_hello>
        ');
        $config->extend($this->createConfig('
            <fixture_hello>
                <class>' . GoodbyeCommand::class . '</class>
            </fixture_hello>
        '));

        $command = CommandLoader::fromConfig($config)->get('fixture:hello');

        self::assertInstanceOf(GoodbyeCommand::class, $command);
        self::assertSame('fixture:hello', $command->getName());
    }

    public function testLaterModuleDisablesCommandByKey(): void
    {
        $config = $this->createConfig('
            <fixture_hello>
                <name>fixture:hello</name>
                <class>' . HelloCommand::class . '</class>
            </fixture_hello>
        ');
        $config->extend($this->createConfig('
            <fixture_hello>
                <disabled>1</disabled>
            </fixture_hello>
        '));

        self::assertSame([], CommandLoader::fromConfig($config)->getNames());
    }

    private function createConfig(string $commands): Mage_Core_Model_Config_Base
    {
        return new Mage_Core_Model_Config_Base(
            '<config><global><console><commands>' . $commands . '</commands></console></global></config>',
        );
    }
}
