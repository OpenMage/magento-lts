<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * Make (rule_id, customer_id) the primary key of salesrule_customer (GHSA-5rg7-22cv-c9q6)
 *
 * The per customer rule usage counter is incremented with INSERT ... ON DUPLICATE KEY UPDATE, which
 * needs a unique key to arbitrate concurrent first uses. It has to be the primary key: a duplicate on a
 * secondary unique key makes InnoDB lock the gap before the existing entry and the gap at the end of the
 * primary key, which blocks every first use of any rule until the order is committed. A duplicate on the
 * primary key only locks that row. rule_customer_id stays the auto increment id of the model.
 *
 * Rows that concurrent orders created for the same rule and customer are merged first: the lowest id is
 * kept with the sum of the uses. The script is idempotent. When orders placed while it runs create new
 * duplicates before the primary key is in place, it locks the table for writes and does it again.
 * Running it in maintenance mode avoids that.
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();
$table = $installer->getTable('salesrule/rule_customer');
$quotedTable = $connection->quoteIdentifier($table);

$hasNewPrimaryKey = static function () use ($connection, $table): bool {
    $connection->resetDdlCache($table);
    $indexes = $connection->getIndexList($table);

    return isset($indexes['PRIMARY'])
        && array_map(strtolower(...), $indexes['PRIMARY']['COLUMNS_LIST']) === ['rule_id', 'customer_id'];
};

/**
 * Merge the rows of the same rule and customer into the one with the lowest id
 *
 * A scan of the (rule_id, customer_id) index alone finds the duplicates, they are merged in chunks, each in
 * a transaction of its own unless the table is locked (starting a transaction would release that lock).
 */
$mergeDuplicates = static function (bool $useTransactions) use ($connection, $table): void {
    $duplicates = (array) $connection->fetchAll(
        $connection->select()
            ->from($table, ['rule_id', 'customer_id'])
            ->group(['rule_id', 'customer_id'])
            ->having('COUNT(*) > 1'),
    );
    foreach (array_chunk($duplicates, 500) as $chunk) {
        $keys = [];
        foreach ($chunk as $duplicate) {
            $keys[] = sprintf('(%d, %d)', $duplicate['rule_id'], $duplicate['customer_id']);
        }

        if ($useTransactions) {
            $connection->beginTransaction();
        }

        try {
            $select = $connection->select()
                ->from($table, ['rule_customer_id', 'rule_id', 'customer_id', 'times_used'])
                ->where('(rule_id, customer_id) IN (' . implode(', ', $keys) . ')')
                ->order('rule_customer_id ' . Varien_Db_Select::SQL_ASC)
                ->forUpdate($useTransactions);

            $merged = [];
            $obsoleteIds = [];
            foreach ((array) $connection->fetchAll($select) as $row) {
                $key = $row['rule_id'] . '-' . $row['customer_id'];
                if (!isset($merged[$key])) {
                    $merged[$key] = ['id' => (int) $row['rule_customer_id'], 'times_used' => 0];
                } else {
                    $obsoleteIds[] = (int) $row['rule_customer_id'];
                }

                $merged[$key]['times_used'] += (int) $row['times_used'];
            }

            foreach ($merged as $kept) {
                $connection->update(
                    $table,
                    ['times_used' => min($kept['times_used'], 65535)],
                    ['rule_customer_id = ?' => $kept['id']],
                );
            }

            if ($obsoleteIds !== []) {
                $connection->delete($table, ['rule_customer_id IN (?)' => $obsoleteIds]);
            }

            if ($useTransactions) {
                $connection->commit();
            }
        } catch (Throwable $throwable) {
            if ($useTransactions) {
                $connection->rollBack();
            }

            throw $throwable;
        }
    }
};

// a single statement: the table is rebuilt once and the auto increment column always keeps a key
$changePrimaryKey = static function (string $lock) use ($installer, $connection, $table, $quotedTable): void {
    $indexes = $connection->getIndexList($table);
    $uniqueIdName = $installer->getIdxName(
        'salesrule/rule_customer',
        ['rule_customer_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    );
    $oldIndexName = $installer->getIdxName('salesrule/rule_customer', ['rule_id', 'customer_id']);

    $alter = [];
    if (isset($indexes['PRIMARY'])) {
        $alter[] = 'DROP PRIMARY KEY';
    }

    $alter[] = 'ADD PRIMARY KEY (' . $connection->quoteIdentifier('rule_id') . ', ' . $connection->quoteIdentifier('customer_id') . ')';
    if (!isset($indexes[strtoupper($uniqueIdName)])) {
        $alter[] = 'ADD UNIQUE KEY ' . $connection->quoteIdentifier($uniqueIdName) . ' (' . $connection->quoteIdentifier('rule_customer_id') . ')';
    }

    // superseded by the primary key
    if (isset($indexes[strtoupper($oldIndexName)])) {
        $alter[] = 'DROP INDEX ' . $connection->quoteIdentifier($oldIndexName);
    }

    if ($lock !== '') {
        $alter[] = $lock;
    }

    $connection->query("ALTER TABLE {$quotedTable} " . implode(', ', $alter));
};

$installer->startSetup();

if (!$hasNewPrimaryKey()) {
    /**
     * Online first, so that orders can still be placed while the table is rebuilt. Orders placed meanwhile
     * by customers who used a rule before add duplicate rows (the new code relies on the key added here),
     * which fails the statement at its end. The table is then locked for writes, merged and rebuilt.
     */
    try {
        $mergeDuplicates(true);
        $changePrimaryKey('LOCK=NONE');
    } catch (Zend_Db_Exception $zendDbException) {
        $previous = $zendDbException->getPrevious();
        if (!$previous instanceof PDOException || (int) ($previous->errorInfo[1] ?? 0) !== 1062) {
            throw $zendDbException;
        }

        $connection->query("LOCK TABLES {$quotedTable} WRITE");
        try {
            $mergeDuplicates(false);
            $changePrimaryKey('');
        } finally {
            $connection->query('UNLOCK TABLES');
        }
    }
}

$connection->resetDdlCache($table);

$installer->endSetup();
