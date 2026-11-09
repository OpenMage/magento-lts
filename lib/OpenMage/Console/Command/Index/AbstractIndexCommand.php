<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command\Index;

use Carbon\Carbon;
use Mage;
use Mage_Core_Exception;
use Mage_Index_Model_Indexer;
use Mage_Index_Model_Process;
use OpenMage\Console\Command\AbstractCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Index commands ported from n98-magerun (https://github.com/netz98/n98-magerun), with the same names, arguments and messages
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
abstract class AbstractIndexCommand extends AbstractCommand
{
    /**
     * Visible index processes keyed by indexer code, in the order of the index_process table
     *
     * @return array<string, Mage_Index_Model_Process>
     */
    protected function getProcesses(): array
    {
        $processes = [];
        /** @var Mage_Index_Model_Process $process */
        foreach ($this->getIndexer()->getProcessesCollection() as $process) {
            if ($this->isVisible($process)) {
                $processes[$process->getIndexerCode()] = $process;
            }
        }

        return $processes;
    }

    /**
     * Elapsed time of the last run, empty when the index never ran
     */
    protected function getRuntime(Mage_Index_Model_Process $process): string
    {
        $startedAt = $process->getStartedAt();
        $endedAt = $process->getEndedAt();
        if (!$startedAt || !$endedAt) {
            return '';
        }

        $seconds = Carbon::parse($endedAt)->getTimestamp() - Carbon::parse($startedAt)->getTimestamp();

        return $seconds < 0 ? 'index not finished' : $this->formatDuration($seconds);
    }

    /**
     * Reindexes each process and goes on after a failure; returns false when at least one failed
     *
     * @param list<Mage_Index_Model_Process> $processes
     */
    protected function executeProcesses(OutputInterface $output, array $processes): bool
    {
        $isSuccessful = true;
        Mage::dispatchEvent('shell_reindex_init_process');
        try {
            foreach ($processes as $process) {
                $isSuccessful = $this->executeProcess($output, $process) && $isSuccessful;
            }
        } finally {
            Mage::dispatchEvent('shell_reindex_finalize_process');
        }

        return $isSuccessful;
    }

    /**
     * reindexEverything() reindexes the dependencies through the index/indexer singleton and flags each
     * process object as done: the same singleton avoids reindexing a dependency twice.
     */
    private function getIndexer(): Mage_Index_Model_Indexer
    {
        /** @var Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');

        return $indexer;
    }

    /**
     * A process whose indexer model cannot be loaded stays listed, its reindex reports the error.
     */
    private function isVisible(Mage_Index_Model_Process $process): bool
    {
        try {
            return $process->getIndexer()->isVisible() !== false;
        } catch (Mage_Core_Exception) {
            return true;
        }
    }

    private function executeProcess(OutputInterface $output, Mage_Index_Model_Process $process): bool
    {
        $code = $process->getIndexerCode();
        $output->writeln(sprintf('<info>Started reindex of: <comment>%s</comment></info>', $code));
        $startTime = microtime(true);

        try {
            $process->reindexEverything();
            Mage::dispatchEvent($code . '_shell_reindex_after');
        } catch (Throwable $throwable) {
            $this->writeError($output, sprintf(
                '<error>Reindex of %s finished with error message "%s"</error> (Runtime: <comment>%s</comment>)',
                $code,
                $throwable->getMessage(),
                $this->formatDuration((int) ceil(microtime(true) - $startTime)),
            ));

            return false;
        }

        $output->writeln(sprintf(
            '<info>Successfully re-indexed <comment>%s</comment> (Runtime: <comment>%s</comment>)</info>',
            $code,
            $this->formatDuration((int) ceil(microtime(true) - $startTime)),
        ));

        return true;
    }

    private function formatDuration(int $seconds): string
    {
        return sprintf('%02d:%02d:%02d', intdiv($seconds, 3600), intdiv($seconds % 3600, 60), $seconds % 60);
    }
}
