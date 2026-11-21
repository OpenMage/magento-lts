<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Command\Config;

use Mage;
use Mage_Core_Model_Resource;
use Varien_Db_Adapter_Interface;
use Varien_Db_Select;

/**
 * Snapshots core_config_data rows touched by a test and restores them afterwards
 *
 * The test database is shared: rows are restored as they were, never truncated.
 */
trait ConfigDataFixture
{
    /**
     * @var list<array<string, mixed>>
     */
    private array $configSnapshot = [];

    /**
     * @var list<string>
     */
    private array $snapshotPaths = [];

    /**
     * @param list<string> $paths
     */
    private function snapshotConfig(array $paths): void
    {
        Mage::app();
        if (!Mage::isInstalled()) {
            self::markTestSkipped('Needs an installed OpenMage database.');
        }

        $this->snapshotPaths = $paths;
        /** @var list<array<string, mixed>> $rows */
        $rows = $this->connection()->fetchAll($this->selectRows());
        $this->configSnapshot = $rows;
    }

    private function restoreConfig(): void
    {
        if (!Mage::isInstalled()) {
            return;
        }

        $connection = $this->connection();
        $connection->delete($this->table(), $this->rowsCondition());
        foreach ($this->configSnapshot as $row) {
            $connection->insert($this->table(), $row);
        }

        Mage::app()->getCacheInstance()->cleanType('config');
        Mage::app()->getConfig()->reinit();
        Mage::app()->reinitStores();
    }

    /**
     * @return array<string, array{scope: string, scope_id: int, value: null|string}>
     */
    private function storedRows(string $path): array
    {
        $select = $this->connection()->select()
            ->from($this->table(), ['scope', 'scope_id', 'value'])
            ->where('path = ?', $path);

        $rows = [];
        /** @var array{scope: string, scope_id: int|string, value: null|string} $row */
        foreach ($this->connection()->fetchAll($select) as $row) {
            $rows[$row['scope'] . '/' . $row['scope_id']] = [
                'scope' => $row['scope'],
                'scope_id' => (int) $row['scope_id'],
                'value' => $row['value'],
            ];
        }

        return $rows;
    }

    private function selectRows(): Varien_Db_Select
    {
        return $this->connection()->select()->from($this->table())->where($this->rowsCondition());
    }

    private function rowsCondition(): string
    {
        $connection = $this->connection();
        $condition = $connection->quoteInto('path LIKE ?', 'openmage_console_test/%');
        if ($this->snapshotPaths !== []) {
            $condition .= ' OR ' . $connection->quoteInto('path IN (?)', $this->snapshotPaths);
        }

        return $condition;
    }

    private function connection(): Varien_Db_Adapter_Interface
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');

        $connection = $resource->getConnection('core_write');
        self::assertInstanceOf(Varien_Db_Adapter_Interface::class, $connection);

        return $connection;
    }

    private function table(): string
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');

        return $resource->getTableName('core/config_data');
    }
}
