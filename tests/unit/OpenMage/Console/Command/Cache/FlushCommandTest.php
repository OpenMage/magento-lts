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
use OpenMage\Console\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class FlushCommandTest extends TestCase
{
    private const ENTRY = 'openmage_console_test_untagged';

    public function testFlushesTheWholeCacheStorage(): void
    {
        Mage::app()->saveCache('untagged', self::ENTRY);
        $tester = new CommandTester(Application::create()->find('cache:flush'));

        self::assertSame(Command::SUCCESS, $tester->execute([]));
        self::assertFalse(Mage::app()->loadCache(self::ENTRY));
        self::assertStringContainsString('flushed', $tester->getDisplay());
    }
}
