<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource;

use Magento_Db_Adapter_Pdo_Mysql;
use Override;
use Throwable;

/**
 * Database adapter that records the statements it is given instead of talking to a database
 */
final class AdapterFixture extends Magento_Db_Adapter_Pdo_Mysql
{
    /**
     * @var list<array{op: string, table: mixed, bind: array<mixed>, where: mixed}>
     */
    public array $calls = [];

    /**
     * @var list<string>
     */
    public array $queries = [];

    public int $affectedRows = 1;

    public ?Throwable $insertFails = null;

    public false|string $fetchOneResult = false;

    /**
     * @var array<string, mixed>|false
     */
    public array|false $fetchRowResult = false;

    /**
     * @var array<int|string, mixed>
     */
    public array $fetchPairsResult = [];

    /**
     * No connection is ever made, so none of the connection configuration is needed
     */
    public function __construct() {} // @phpstan-ignore constructor.missingParentCall

    #[Override]
    protected function _connect() {}

    /**
     * @param mixed $value
     * @param mixed $type
     */
    #[Override]
    public function quote($value, $type = null): string
    {
        if (is_array($value)) {
            return implode(', ', array_map(fn($item): string => $this->quote($item), $value));
        }

        return is_int($value) ? (string) $value : "'" . addslashes((string) $value) . "'";
    }

    /**
     * @param mixed        $table
     * @param array<mixed> $bind
     * @param mixed        $where
     */
    #[Override]
    public function update($table, array $bind, $where = ''): int
    {
        $this->calls[] = ['op' => 'update', 'table' => $table, 'bind' => $bind, 'where' => $where];
        return $this->affectedRows;
    }

    /**
     * @param mixed        $table
     * @param array<mixed> $bind
     */
    #[Override]
    public function insert($table, array $bind): int
    {
        $this->calls[] = ['op' => 'insert', 'table' => $table, 'bind' => $bind, 'where' => null];
        if ($this->insertFails instanceof Throwable) {
            throw $this->insertFails;
        }

        return 1;
    }

    /**
     * @return false|string false without a row, like the real adapter
     */
    // @phpstan-ignore method.childReturnType (Varien_Db_Adapter_Interface documents string only)
    #[Override]
    public function fetchOne($sql, $bind = [])
    {
        $this->queries[] = (string) $sql;
        return $this->fetchOneResult;
    }

    /**
     * @return array<string, mixed>|false false without a row, like the real adapter
     */
    // @phpstan-ignore method.childReturnType (Varien_Db_Adapter_Interface documents array only)
    #[Override]
    public function fetchRow($sql, $bind = [], $fetchMode = null)
    {
        $this->queries[] = (string) $sql;
        return $this->fetchRowResult;
    }

    /**
     * @param  mixed                    $sql
     * @param  mixed                    $bind
     * @param  mixed                    $fetchMode
     * @return array<int|string, mixed>
     */
    #[Override]
    public function fetchPairs($sql, $bind = [], $fetchMode = null): array
    {
        $this->queries[] = (string) $sql;
        return $this->fetchPairsResult;
    }
}
