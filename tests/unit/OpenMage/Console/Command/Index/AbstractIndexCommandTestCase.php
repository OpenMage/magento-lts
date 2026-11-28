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
use Mage_Core_Model_Config;
use Mage_Core_Model_Config_Element;
use Mage_Index_Model_Indexer;
use Mage_Index_Model_Process;
use OpenMage\Console\Application;
use OpenMage\Tests\Unit\OpenMage\Console\Command\Index\Fixtures\FailingIndexer;
use OpenMage\Tests\Unit\OpenMage\Console\Command\Index\Fixtures\RecordingIndexer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Varien_Db_Adapter_Interface;

/**
 * Replaces every indexer model by a test double and restores the index_process rows afterwards
 */
abstract class AbstractIndexCommandTestCase extends TestCase
{
    /**
     * @var array<string, string>
     */
    private array $indexerModels = [];

    /**
     * @var list<array<string, mixed>>
     */
    private array $processRows = [];

    protected function setUp(): void
    {
        Mage::app();
        Mage::unregister('_singleton/index/indexer');
        RecordingIndexer::$reindexCount = 0;

        $connection = $this->getConnection();
        /** @var list<array<string, mixed>> $rows */
        $rows = $connection->fetchAll($connection->select()->from($this->getProcessTable()));
        $this->processRows = $rows;

        // On a Docker Desktop host mount, a flock taken through the handle that created the file is not seen
        // by other handles: the lock files exist before the core or a test opens them.
        foreach ($rows as $row) {
            $lockFile = $this->getConfig()->getVarDir('locks') . DS . 'index_process_' . $row['process_id'] . '.lock';
            self::assertTrue(touch($lockFile));
        }

        $indexers = $this->getConfig()->getNode(Mage_Index_Model_Process::XML_PATH_INDEXER_DATA);
        self::assertInstanceOf(Mage_Core_Model_Config_Element::class, $indexers);
        foreach ($indexers->children() as $code => $node) {
            $this->indexerModels[$code] = (string) $node->model;
            $this->setIndexerModel($code, RecordingIndexer::class);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->indexerModels as $code => $model) {
            $this->setIndexerModel($code, $model);
        }

        foreach ($this->processRows as $row) {
            $this->getConnection()->update($this->getProcessTable(), $row, ['process_id = ?' => $row['process_id']]);
        }

        Mage::unregister('_singleton/index/indexer');
    }

    protected function useFailingIndexer(string $code): void
    {
        $this->setIndexerModel($code, FailingIndexer::class);
    }

    protected function getProcess(string $code): Mage_Index_Model_Process
    {
        /** @var Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');
        $process = $indexer->getProcessByCode($code);
        self::assertInstanceOf(Mage_Index_Model_Process::class, $process);

        return $process;
    }

    protected function getConfig(): Mage_Core_Model_Config
    {
        $config = Mage::getConfig();
        self::assertInstanceOf(Mage_Core_Model_Config::class, $config);

        return $config;
    }

    protected function createTester(string $name): CommandTester
    {
        return new CommandTester(Application::create()->find($name));
    }

    private function setIndexerModel(string $code, string $model): void
    {
        $this->getConfig()->setNode(Mage_Index_Model_Process::XML_PATH_INDEXER_DATA . '/' . $code . '/model', $model);
    }

    private function getConnection(): Varien_Db_Adapter_Interface
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        self::assertInstanceOf(Varien_Db_Adapter_Interface::class, $connection);

        return $connection;
    }

    private function getProcessTable(): string
    {
        return Mage::getSingleton('core/resource')->getTableName('index/process');
    }
}
