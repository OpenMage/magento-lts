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
use Symfony\Component\Console\Command\Command;

final class ReindexAllCommandTest extends AbstractIndexCommandTestCase
{
    public function testReindexesEveryIndex(): void
    {
        /** @var Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');
        $tester = $this->createTester('index:reindex:all');

        self::assertSame(Command::SUCCESS, $tester->execute([]));
        self::assertSame($indexer->getProcessesCollection()->count(), RecordingIndexer::$reindexCount);
        self::assertStringContainsString('Successfully re-indexed tag_summary', $tester->getDisplay());
    }

    public function testFailsWhenOneIndexFails(): void
    {
        $this->useFailingIndexer('tag_summary');
        $tester = $this->createTester('index:reindex:all');

        self::assertSame(Command::FAILURE, $tester->execute([], ['capture_stderr_separately' => true]));
        self::assertStringContainsString(FailingIndexer::MESSAGE, $tester->getErrorOutput());
        self::assertStringContainsString('Successfully re-indexed catalogsearch_fulltext', $tester->getDisplay());
    }
}
