<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Magento_Db
 */

/**
 * Magento PDO MySQL DB adapter
 *
 * @package    Magento_Db
 */
class Magento_Db_Adapter_Pdo_Mysql extends Varien_Db_Adapter_Pdo_Mysql
{
    /**
     * Returns flag is transaction now?
     *
     * @return bool
     */
    public function isTransaction()
    {
        return (bool) $this->_transactionLevel;
    }

    /**
     * Batched insert of specified select
     *
     * @param string $table
     * @param bool $mode
     * @param int $step
     * @return int
     */
    public function insertBatchFromSelect(
        Varien_Db_Select $select,
        $table,
        array $fields = [],
        $mode = false,
        $step = 10000
    ) {
        $limitOffset = 0;
        $totalAffectedRows = 0;

        do {
            $select->limit($step, $limitOffset);
            $result = $this->query(
                $this->insertFromSelect($select, $table, $fields, $mode),
            );

            $affectedRows = $result->rowCount();
            $totalAffectedRows += $affectedRows;
            $limitOffset += $step;
        } while ($affectedRows > 0);

        return $totalAffectedRows;
    }

    /**
     * Retrieve bunch of queries for specified select split by specified step
     *
     * @param string $entityIdField
     * @param int $step
     * @return array
     */
    public function splitSelect(Varien_Db_Select $select, $entityIdField = '*', $step = 10000)
    {
        $countSelect = clone $select;

        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->columns('COUNT(' . $entityIdField . ')');

        $row = $this->fetchRow($countSelect);
        $totalRows = array_shift($row);

        $bunches = [];
        for ($i = 0; $i <= $totalRows; $i += $step) {
            $bunchSelect = clone $select;
            $bunches[] = $bunchSelect->limit($step, $i);
        }

        return $bunches;
    }

    /**
     * Quote a raw string.
     *
     * @param float|string $value   Raw string
     * @return float|string         Quoted string
     */
    protected function _quote($value)
    {
        if (is_float($value)) {
            return $this->_convertFloat($value);
        }

        // Fix for null-byte injection
        if (is_string($value)) {
            $value = addcslashes($value, "\000\032");
        }

        return parent::_quote($value);
    }

    /**
     * Safely quotes a value for an SQL statement.
     *
     * If an array is passed as the value, the array values are quote
     * and then returned as a comma-separated string.
     *
     * @param null|array|float|int|string|Zend_Db_Expr|Zend_Db_Select $value OPTIONAL A single value to quote into the condition
     * @param null|int|string $type  OPTIONAL The type of the given value e.g. Zend_Db::INT_TYPE, "INT"
     * @return string an SQL-safe quoted value (or string of separated values)
     */
    public function quote($value, $type = null)
    {
        $this->_connect();

        if ($type !== null
            && array_key_exists($type = strtoupper($type), $this->_numericDataTypes)
            && $this->_numericDataTypes[$type] == Zend_Db::FLOAT_TYPE
        ) {
            $value = $this->_convertFloat($value);
            return sprintf('%F', $value);
        } elseif (is_float($value)) {
            return $this->_quote($value);
        }

        return parent::quote($value, $type);
    }

    /**
     * Convert float values that are not supported by MySQL to alternative representation value.
     * Value 99999999.9999 is a maximum value that may be stored in Magento decimal columns in DB.
     *
     * @param float $value
     * @return float
     */
    protected function _convertFloat($value)
    {
        $value = (float) $value;

        if (is_infinite($value)) {
            $value = ($value > 0)
                ? 99999999.9999
                : -99999999.9999;
        } elseif (is_nan($value)) {
            $value = 0.0;
        }

        return $value;
    }
}
