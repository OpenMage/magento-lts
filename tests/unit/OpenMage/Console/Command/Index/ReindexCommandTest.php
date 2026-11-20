<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Command\Index;

use Mage;
use Mage_Index_Model_Indexer;
use OpenMage\Tests\Unit\OpenMage\Console\Command\Index\Fixtures\FailingIndexer;
use OpenMage\Tests\Unit\OpenMage\Console\Command\Index\Fixtures\RecordingIndexer;
use OpenMage\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Tester\CommandTester;

final class ReindexCommandTest extends AbstractIndexCommandTestCase
{
    public function testReindexesCommaSeparatedCodes(): void
    {
        $tester = $this->createTester('index:reindex');

        self::assertSame(Command::SUCCESS, $tester->execute(['index_code' => ['tag_summary,catalogsearch_fulltext']]));
        self::assertSame(2, RecordingIndexer::$reindexCount);
        self::assertStringContainsString('Successfully re-indexed tag_summary', $tester->getDisplay());
        self::assertStringContainsString('Successfully re-indexed catalogsearch_fulltext', $tester->getDisplay());
    }

    public function testReindexesSpaceSeparatedCodes(): void
    {
        $tester = $this->createTester('index:reindex');

        self::assertSame(Command::SUCCESS, $tester->execute(['index_code' => ['tag_summary', 'catalogsearch_fulltext']]));
        self::assertSame(2, RecordingIndexer::$reindexCount);
    }

    public function testUnknownCodeFailsBeforeReindexing(): void
    {
        $tester = $this->createTester('index:reindex');

        self::assertSame(Command::FAILURE, $tester->execute(['index_code' => ['tag_summary,unknown_index']], ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertSame(0, RecordingIndexer::$reindexCount);
        self::assertStringContainsString('unknown_index', $tester->getErrorOutput());
        self::assertStringContainsString('catalogsearch_fulltext', $tester->getErrorOutput());
    }

    public function testFailedIndexDoesNotStopTheNextOnes(): void
    {
        $this->useFailingIndexer('tag_summary');
        $tester = $this->createTester('index:reindex');

        self::assertSame(Command::FAILURE, $tester->execute(['index_code' => ['tag_summary,catalogsearch_fulltext']], ['capture_stderr_separately' => true]));
        self::assertSame(1, RecordingIndexer::$reindexCount);
        self::assertStringContainsString(FailingIndexer::MESSAGE, $tester->getErrorOutput());
        self::assertStringNotContainsString(FailingIndexer::MESSAGE, $tester->getDisplay());
        self::assertStringContainsString('Successfully re-indexed catalogsearch_fulltext', $tester->getDisplay());
    }

    public function testIndexIsSkippedWhenItsDependencyFailed(): void
    {
        $this->useFailingIndexer('cataloginventory_stock');
        $tester = $this->createTester('index:reindex');

        self::assertSame(Command::FAILURE, $tester->execute(
            ['index_code' => ['cataloginventory_stock,catalog_product_price,tag_summary']],
            ['capture_stderr_separately' => true],
        ));
        self::assertSame(1, RecordingIndexer::$reindexCount);
        self::assertStringContainsString(
            'Reindex of catalog_product_price skipped: dependency cataloginventory_stock failed',
            $tester->getErrorOutput(),
        );
        self::assertStringNotContainsString('Started reindex of: catalog_product_price', $tester->getDisplay());
        self::assertStringContainsString('Successfully re-indexed tag_summary', $tester->getDisplay());
    }

    public function testDependencyIsReindexedFirstAndReportedOnce(): void
    {
        $this->useFailingIndexer('cataloginventory_stock');
        $tester = $this->createTester('index:reindex');

        self::assertSame(Command::FAILURE, $tester->execute(
            ['index_code' => ['catalog_product_price,cataloginventory_stock']],
            ['capture_stderr_separately' => true],
        ));
        self::assertSame(0, RecordingIndexer::$reindexCount);
        self::assertSame(1, substr_count($tester->getErrorOutput(), 'Reindex of cataloginventory_stock finished with error'));
        self::assertStringContainsString('Reindex of catalog_product_price skipped', $tester->getErrorOutput());
        self::assertStringNotContainsString('Successfully re-indexed', $tester->getDisplay());
    }

    public function testSharedDependencyRunsOnce(): void
    {
        $tester = $this->createTester('index:reindex');

        self::assertSame(Command::SUCCESS, $tester->execute(['index_code' => ['catalog_product_price,catalog_product_attribute']]));
        self::assertSame(3, RecordingIndexer::$reindexCount);
        self::assertSame(1, substr_count($tester->getDisplay(), 'Successfully re-indexed cataloginventory_stock'));
        self::assertLessThan(
            strpos($tester->getDisplay(), 'Started reindex of: catalog_product_price'),
            strpos($tester->getDisplay(), 'Successfully re-indexed cataloginventory_stock'),
        );
    }

    public function testLockedIndexFailsWithoutWaiting(): void
    {
        $lockFile = $this->getConfig()->getVarDir('locks') . DS
            . $this->getProcess('catalogsearch_fulltext')->getProcessLockName() . '.lock';
        $handle = fopen($lockFile, 'c');
        self::assertIsResource($handle);
        self::assertTrue(flock($handle, LOCK_EX | LOCK_NB));

        try {
            $tester = $this->createTester('index:reindex');
            self::assertSame(Command::FAILURE, $tester->execute(['index_code' => ['catalogsearch_fulltext,tag_summary']], ['capture_stderr_separately' => true]));
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }

        self::assertSame(1, RecordingIndexer::$reindexCount);
        self::assertStringContainsString('working now', $tester->getErrorOutput());
        self::assertStringContainsString('Successfully re-indexed tag_summary', $tester->getDisplay());
    }

    public function testFailsWithoutCodeInNonInteractiveMode(): void
    {
        $tester = $this->createTester('index:reindex');

        self::assertSame(Command::FAILURE, $tester->execute([], ['interactive' => false, 'capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertSame(0, RecordingIndexer::$reindexCount);
        self::assertStringContainsString('tag_summary', $tester->getErrorOutput());
    }

    public function testAsksForIndexesByCodeOrNumber(): void
    {
        /** @var Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');
        $codes = [];
        foreach ($indexer->getProcessesCollection() as $process) {
            $codes[] = $process->getIndexerCode();
        }

        $tester = $this->createTester('index:reindex');
        $tester->setInputs(['tag_summary, ' . array_search('catalogsearch_fulltext', $codes, true)]);

        self::assertSame(Command::SUCCESS, $tester->execute([]));
        self::assertSame(2, RecordingIndexer::$reindexCount);
        self::assertStringContainsString('Successfully re-indexed catalogsearch_fulltext', $tester->getDisplay());
    }

    public function testAutocompletesWithIndexCodesOnly(): void
    {
        $helper = new class extends QuestionHelper {
            public ?Question $question = null;

            public function ask(InputInterface $input, OutputInterface $output, Question $question): mixed
            {
                $this->question = $question;

                return parent::ask($input, $output, $question);
            }
        };
        $command = Application::create()->find('index:reindex');
        $helperSet = $command->getHelperSet();
        self::assertInstanceOf(HelperSet::class, $helperSet);
        $helperSet->set($helper);

        $tester = new CommandTester($command);
        $tester->setInputs(['tag_summary']);

        self::assertSame(Command::SUCCESS, $tester->execute([]));
        self::assertInstanceOf(Question::class, $helper->question);
        $values = $helper->question->getAutocompleterValues();
        self::assertIsArray($values);
        self::assertContains('tag_summary', $values);
        self::assertContains('catalogsearch_fulltext', $values);
        foreach ($values as $value) {
            self::assertMatchesRegularExpression('/^\w+$/', (string) $value);
        }
    }
}
