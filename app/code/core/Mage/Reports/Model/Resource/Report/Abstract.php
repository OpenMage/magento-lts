<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract report aggregate resource model
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Reports_Model_Resource_Report_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Flag object
     *
     * @var Mage_Reports_Model_Flag
     */
    protected $_flag     = null;

    /**
     * Retrieve flag object
     *
     * @return Mage_Reports_Model_Flag
     */
    protected function _getFlag()
    {
        if ($this->_flag === null) {
            $this->_flag = Mage::getModel('reports/flag');
        }
        return $this->_flag;
    }

    /**
     * Saves flag
     *
     * @param string $code
     * @param mixed $value
     * @return Mage_Reports_Model_Resource_Report_Abstract
     */
    protected function _setFlagData($code, $value = null)
    {
        $this->_getFlag()
            ->setReportFlagCode($code)
            ->unsetData()
            ->loadSelf();

        if ($value !== null) {
            $this->_getFlag()->setFlagData($value);
        }

        $time = Varien_Date::toTimestamp(true);
        // touch last_update
        $this->_getFlag()->setLastUpdate($this->formatDate($time));

        $this->_getFlag()->save();

        return $this;
    }

    /**
     * Retrieve flag data
     *
     * @param string $code
     * @return mixed
     */
    protected function _getFlagData($code)
    {
        $this->_getFlag()
            ->setReportFlagCode($code)
            ->unsetData()
            ->loadSelf();

        return $this->_getFlag()->getFlagData();
    }

    /**
     * Truncate table
     *
     * @param string $table
     * @return Mage_Reports_Model_Resource_Report_Abstract
     */
    protected function _truncateTable($table)
    {
        if ($this->_getWriteAdapter()->getTransactionLevel() > 0) {
            $this->_getWriteAdapter()->delete($table);
        } else {
            $this->_getWriteAdapter()->truncateTable($table);
        }
        return $this;
    }

    /**
     * Clear report table by specified date range.
     * If specified source table parameters,
     * condition will be generated by source table subselect.
     *
     * @param string $table
     * @param string|null $from
     * @param string|null $to
     * @param Zend_Db_Select|string|null $subSelect
     * @param bool $doNotUseTruncate
     * @return Mage_Reports_Model_Resource_Report_Abstract
     */
    protected function _clearTableByDateRange(
        $table,
        $from = null,
        $to = null,
        $subSelect = null,
        $doNotUseTruncate = false
    ) {
        if ($from === null && $to === null && !$doNotUseTruncate) {
            $this->_truncateTable($table);
            return $this;
        }

        if ($subSelect !== null) {
            $deleteCondition = $this->_makeConditionFromDateRangeSelect($subSelect, 'period');
        } else {
            $condition = [];
            if ($from !== null) {
                $condition[] = $this->_getWriteAdapter()->quoteInto('period >= ?', $from);
            }

            if ($to !== null) {
                $condition[] = $this->_getWriteAdapter()->quoteInto('period <= ?', $to);
            }
            $deleteCondition = implode(' AND ', $condition);
        }
        $this->_getWriteAdapter()->delete($table, $deleteCondition);
        return $this;
    }

    /**
     * Generate table date range select
     *
     * @param string $table
     * @param string $column
     * @param string $whereColumn
     * @param string|null $from
     * @param string|null $to
     * @param array $additionalWhere
     * @param string $alias
     * @return Varien_Db_Select
     */
    protected function _getTableDateRangeSelect(
        $table,
        $column,
        $whereColumn,
        $from = null,
        $to = null,
        $additionalWhere = [],
        $alias = 'date_range_table'
    ) {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from(
                [$alias => $table],
                $adapter->getDatePartSql(
                    $this->getStoreTZOffsetQuery([$alias => $table], $alias . '.' . $column, $from, $to)
                )
            )
            ->distinct(true);

        if ($from !== null) {
            $select->where($alias . '.' . $whereColumn . ' >= ?', $from);
        }

        if ($to !== null) {
            $select->where($alias . '.' . $whereColumn . ' <= ?', $to);
        }

        if (!empty($additionalWhere)) {
            foreach ($additionalWhere as $condition) {
                if (is_array($condition) && count($condition) == 2) {
                    $condition = $adapter->quoteInto($condition[0], $condition[1]);
                } elseif (is_array($condition)) { // Invalid condition
                    continue;
                }
                $condition = str_replace('{{table}}', $adapter->quoteIdentifier($alias), $condition);
                $select->where($condition);
            }
        }

        return $select;
    }

    /**
     * Make condition for using in where section
     * from select statement with single date column
     *
     *
     * @param Varien_Db_Select $select
     * @param string $periodColumn
     * @return string|false
     */
    protected function _makeConditionFromDateRangeSelect($select, $periodColumn)
    {
        static $selectResultCache = [];
        $cacheKey = (string)$select;

        if (!array_key_exists($cacheKey, $selectResultCache)) {
            try {
                $selectResult = [];
                $query = $this->_getReadAdapter()->query($select);
                while ($date = $query->fetchColumn()) {
                    $selectResult[] = $date;
                }
            } catch (Exception $e) {
                $selectResult = false;
            }
            $selectResultCache[$cacheKey] = $selectResult;
        } else {
            $selectResult = $selectResultCache[$cacheKey];
        }
        if ($selectResult === false) {
            return false;
        }

        $whereCondition = [];
        $adapter = $this->_getReadAdapter();
        foreach ($selectResult as $date) {
            $date = substr($date, 0, 10); // to fix differences in oracle
            $whereCondition[] = $adapter->prepareSqlCondition($periodColumn, ['like' => $date]);
        }
        $whereCondition = implode(' OR ', $whereCondition);
        if ($whereCondition == '') {
            $whereCondition = '1=0';  // FALSE condition!
        }

        return $whereCondition;
    }

    /**
     * Generate table date range select
     *
     * @param string $table
     * @param string $relatedTable
     * @param array $joinCondition
     * @param string $column
     * @param string $whereColumn
     * @param string|null $from
     * @param string|null $to
     * @param array $additionalWhere
     * @param string $alias
     * @param string $relatedAlias
     * @return Varien_Db_Select
     */
    protected function _getTableDateRangeRelatedSelect(
        $table,
        $relatedTable,
        $joinCondition,
        $column,
        $whereColumn,
        $from = null,
        $to = null,
        $additionalWhere = [],
        $alias = 'date_range_table',
        $relatedAlias = 'related_date_range_table'
    ) {
        $adapter = $this->_getReadAdapter();
        $joinConditionSql = [];

        foreach ($joinCondition as $fkField => $pkField) {
            $joinConditionSql[] = sprintf('%s.%s = %s.%s', $alias, $fkField, $relatedAlias, $pkField);
        }

        $select = $adapter->select()
            ->from(
                [$alias => $table],
                $adapter->getDatePartSql(
                    $adapter->quoteIdentifier($alias . '.' . $column)
                )
            )
            ->joinInner(
                [$relatedAlias => $relatedTable],
                implode(' AND ', $joinConditionSql),
                []
            )
            ->distinct(true);

        if ($from !== null) {
            $select->where($relatedAlias . '.' . $whereColumn . ' >= ?', $from);
        }

        if ($to !== null) {
            $select->where($relatedAlias . '.' . $whereColumn . ' <= ?', $to);
        }

        if (!empty($additionalWhere)) {
            foreach ($additionalWhere as $condition) {
                if (is_array($condition) && count($condition) == 2) {
                    $condition = $adapter->quoteInto($condition[0], $condition[1]);
                } elseif (is_array($condition)) { // Invalid condition
                    continue;
                }
                $condition = str_replace(
                    ['{{table}}', '{{related_table}}'],
                    [
                        $adapter->quoteIdentifier($alias),
                        $adapter->quoteIdentifier($relatedAlias)
                    ],
                    $condition
                );
                $select->where($condition);
            }
        }

        return $select;
    }

    /**
     * Check range dates and transforms it to strings
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Reports_Model_Resource_Report_Abstract
     */
    protected function _checkDates(&$from, &$to)
    {
        if ($from !== null) {
            $from = $this->formatDate($from);
        }

        if ($to !== null) {
            $to = $this->formatDate($to);
        }

        return $this;
    }

    /**
    * Retrieve query for attribute with timezone conversion
    *
    * @param string|array $table
    * @param string $column
    * @param mixed $from
    * @param mixed $to
    * @param int|string|Mage_Core_Model_Store|null $store
    * @return string
    */
    public function getStoreTZOffsetQuery($table, $column, $from = null, $to = null, $store = null)
    {
        $column = $this->_getWriteAdapter()->quoteIdentifier($column);

        if (is_null($from)) {
            $selectOldest = $this->_getWriteAdapter()->select()
                ->from(
                    $table,
                    ["MIN($column)"]
                );
            $from = $this->_getWriteAdapter()->fetchOne($selectOldest);
        }

        $periods = $this->_getTZOffsetTransitions(
            Mage::app()->getLocale()->storeDate($store)->toString(Zend_Date::TIMEZONE_NAME),
            $from,
            $to
        );
        if (empty($periods)) {
            return $column;
        }

        $query = "";
        $periodsCount = count($periods);

        $i = 0;
        foreach ($periods as $offset => $timestamps) {
            $subParts = [];
            foreach ($timestamps as $ts) {
                $subParts[] = "($column between {$ts['from']} and {$ts['to']})";
            }

            $then = $this->_getWriteAdapter()
                ->getDateAddSql($column, $offset, Varien_Db_Adapter_Interface::INTERVAL_SECOND);

            $query .= (++$i == $periodsCount) ? $then : "CASE WHEN " . implode(" OR ", $subParts) . " THEN $then ELSE ";
        }

        return $query . str_repeat('END ', count($periods) - 1);
    }

    /**
     * Retrieve transitions for offsets of given timezone
     *
     * @param string $timezone
     * @param mixed $from
     * @param mixed $to
     * @return array
     */
    protected function _getTZOffsetTransitions($timezone, $from = null, $to = null)
    {
        $tzTransitions = [];
        try {
            if (!empty($from)) {
                $from = new Zend_Date($from, Varien_Date::DATETIME_INTERNAL_FORMAT);
                $from = $from->getTimestamp();
            }

            $to = new Zend_Date($to, Varien_Date::DATETIME_INTERNAL_FORMAT);
            $nextPeriod = $this->_getWriteAdapter()->formatDate($to->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            $to = $to->getTimestamp();

            $dtz = new DateTimeZone($timezone);
            $transitions = $dtz->getTransitions();
            $dateTimeObject = new Zend_Date('c');
            for ($i = count($transitions) - 1; $i >= 0; $i--) {
                $tr = $transitions[$i];
                if (!$this->_isValidTransition($tr, $to)) {
                    continue;
                }

                $dateTimeObject->set($tr['time']);
                $tr['time'] = $this->_getWriteAdapter()
                    ->formatDate($dateTimeObject->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
                $tzTransitions[$tr['offset']][] = ['from' => $tr['time'], 'to' => $nextPeriod];

                if (!empty($from) && $tr['ts'] < $from) {
                    break;
                }
                $nextPeriod = $tr['time'];
            }
        } catch (Exception $e) {
            $this->_logException($e);
        }

        return $tzTransitions;
    }

    /**
     * Logs the exceptions
     *
     * @param Exception $exception
     */
    protected function _logException($exception)
    {
        Mage::logException($exception);
    }

    /**
     * Verifies the transition and the "to" timestamp
     *
     * @param array      $transition
     * @param int|string $to
     * @return bool
     */
    protected function _isValidTransition($transition, $to)
    {
        $result         = true;
        $timeStamp      = $transition['ts'];
        $transitionYear = date('Y', $timeStamp);

        if ($transitionYear > 10000 || $transitionYear < -10000) {
            $result = false;
        } elseif ($timeStamp > $to) {
            $result = false;
        }

        return $result;
    }

    /**
     * Retrieve store timezone offset from UTC in the form acceptable by SQL's CONVERT_TZ()
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    protected function _getStoreTimezoneUtcOffset($store = null)
    {
        return Mage::app()->getLocale()->storeDate($store)->toString(Zend_Date::GMT_DIFF_SEP);
    }

    /**
     * Retrieve date in UTC timezone
     *
     * @param string|null $date
     * @return Zend_Date|null
     */
    protected function _dateToUtc($date)
    {
        if ($date === null) {
            return null;
        }
        $dateUtc = new Zend_Date($date);
        $dateUtc->setTimezone('Etc/UTC');
        return $dateUtc;
    }
}
