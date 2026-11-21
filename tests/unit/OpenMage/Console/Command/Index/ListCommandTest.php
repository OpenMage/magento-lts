<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Command\Index;

use InvalidArgumentException;
use OpenMage\Tests\Unit\OpenMage\Console\Command\Index\Fixtures\RecordingIndexer;
use Symfony\Component\Console\Command\Command;

final class ListCommandTest extends AbstractIndexCommandTestCase
{
    public function testListsIndexesWithStatusAndRuntime(): void
    {
        $tester = $this->createTester('index:list');

        self::assertSame(Command::SUCCESS, $tester->execute([]));
        self::assertSame(0, RecordingIndexer::$reindexCount);
        self::assertMatchesRegularExpression('/code\s+\|\s+status\s+\|\s+time/', $tester->getDisplay());
        self::assertMatchesRegularExpression('/tag_summary\s+\|\s+\w+/', $tester->getDisplay());
    }

    public function testListsIndexesAsJson(): void
    {
        $tester = $this->createTester('index:list');

        self::assertSame(Command::SUCCESS, $tester->execute(['--format' => 'json']));
        $rows = json_decode($tester->getDisplay(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($rows);
        self::assertNotSame([], $rows);
        foreach ($rows as $row) {
            self::assertIsArray($row);
            self::assertSame(['code', 'status', 'time'], array_keys($row));
        }

        self::assertContains('tag_summary', array_column($rows, 'code'));
    }

    public function testListsIndexesAsCsv(): void
    {
        $tester = $this->createTester('index:list');

        self::assertSame(Command::SUCCESS, $tester->execute(['--format' => 'csv']));
        $lines = explode("\n", trim($tester->getDisplay()));
        self::assertSame('code,status,time', $lines[0]);
        self::assertMatchesRegularExpression('/^tag_summary,\w+,/m', $tester->getDisplay());
    }

    public function testUnknownFormatFails(): void
    {
        $tester = $this->createTester('index:list');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown format "xml"');
        $tester->execute(['--format' => 'xml']);
    }

    public function testFlagsAnIndexWhoseEndDateIsBeforeItsStartDate(): void
    {
        $this->getProcess('tag_summary')
            ->setData('started_at', '2026-01-01 10:00:00')
            ->setData('ended_at', '2026-01-01 09:00:00')
            ->save();
        $tester = $this->createTester('index:list');

        self::assertSame(Command::SUCCESS, $tester->execute(['--format' => 'json']));
        $rows = json_decode($tester->getDisplay(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($rows);
        self::assertSame('index not finished', array_column($rows, 'time', 'code')['tag_summary']);
    }
}
