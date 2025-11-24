<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Magento_Profiler
 */

/**
 * Abstract class that represents profiler output
 */
abstract class Magento_Profiler_OutputAbstract
{
    /**
     * PCRE Regular Expression for filter
     *
     * @var null|string
     */
    private $_filter;

    /**
     * List of threshold (minimal allowed) values for profiler data
     *
     * @var array
     */
    private $_thresholds = [
        Magento_Profiler::FETCH_TIME    => 0.001,
        Magento_Profiler::FETCH_COUNT   => 10,
        Magento_Profiler::FETCH_EMALLOC => 10000,
    ];

    /**
     * Initialize profiler output with timer identifiers filter
     *
     * @param null|string $filter Pattern to filter timers by their identifiers.
     *                            Supports syntax similar to SQL LIKE operator:
     *                            % - Matches any number of characters, even zero characters
     *                            _ - Matches exactly one character
     */
    public function __construct($filter = null)
    {
        $this->_filter = $filter;
    }

    /**
     * Override in descendants to display profiling results in appropriate format
     */
    abstract public function display();

    /**
     * Retrieve the list of (column_label; column_id) pairs
     *
     * @return array
     */
    protected function _getColumns()
    {
        return [
            'Timer Id' => 'timer_id',
            'Time'     => Magento_Profiler::FETCH_TIME,
            'Avg'      => Magento_Profiler::FETCH_AVG,
            'Cnt'      => Magento_Profiler::FETCH_COUNT,
            'Emalloc'  => Magento_Profiler::FETCH_EMALLOC,
            'RealMem'  => Magento_Profiler::FETCH_REALMEM,
        ];
    }

    /**
     * Render statistics column value for specified timer
     *
     * @param string $timerId
     * @param string $columnId
     */
    protected function _renderColumnValue($timerId, $columnId)
    {
        if ($columnId == 'timer_id') {
            return $this->_renderTimerId($timerId);
        }

        $value = (float) Magento_Profiler::fetch($timerId, $columnId);
        if (in_array($columnId, [Magento_Profiler::FETCH_TIME, Magento_Profiler::FETCH_AVG])) {
            $value = number_format($value, 6);
        } else {
            $value = number_format($value);
        }

        return $value;
    }

    /**
     * Render timer id column value
     *
     * @param string $timerId
     * @return string
     */
    protected function _renderTimerId($timerId)
    {
        return $timerId;
    }

    /**
     * Retrieve timer ids sorted to correspond the nesting
     *
     * @return array
     */
    private function _getSortedTimers()
    {
        $timerIds = Magento_Profiler::getTimers();
        if (count($timerIds) <= 2) {
            /* No sorting needed */
            return $timerIds;
        }

        /* Prepare PCRE once to use it inside the loop body */
        $nestingSep = preg_quote(Magento_Profiler::NESTING_SEPARATOR, '/');
        $patternLastTimerName = '/' . $nestingSep . '(?:.(?!' . $nestingSep . '))+$/';

        $prevTimerId = $timerIds[0];
        $result = [$prevTimerId];
        $counter = count($timerIds);
        for ($i = 1; $i < $counter; $i++) {
            $timerId = $timerIds[$i];
            /* Skip already added timer */
            if (!$timerId) {
                continue;
            }

            /* Loop over all timers that need to be closed under previous timer */
            while (!str_starts_with($timerId, $prevTimerId . Magento_Profiler::NESTING_SEPARATOR)) {
                /* Add to result all timers nested in the previous timer */
                for ($j = $i + 1; $j < count($timerIds); $j++) {
                    if (str_starts_with($timerIds[$j], $prevTimerId . Magento_Profiler::NESTING_SEPARATOR)) {
                        $result[] = $timerIds[$j];
                        /* Mark timer as already added */
                        $timerIds[$j] = null;
                    }
                }

                /* Go to upper level timer */
                $count = 0;
                $prevTimerId = preg_replace($patternLastTimerName, '', $prevTimerId, -1, $count);
                /* Break the loop if no replacements was done. It is possible when we are */
                /* working with top level (root) item */
                if (!$count) {
                    break;
                }
            }

            /* Add current timer to the result */
            $result[] = $timerId;
            $prevTimerId = $timerId;
        }

        return $result;
    }

    /**
     * Retrieve the list of timer Ids
     *
     * @return array
     */
    protected function _getTimers()
    {
        $pattern = $this->_filter;
        $timerIds = $this->_getSortedTimers();
        $result = [];
        foreach ($timerIds as $timerId) {
            /* Filter by timer id pattern */
            if ($pattern && !preg_match($pattern, $timerId)) {
                continue;
            }

            /* Filter by column value thresholds */
            $skip = false;
            foreach ($this->_thresholds as $fetchKey => $minAllowedValue) {
                $skip = (Magento_Profiler::fetch($timerId, $fetchKey) < $minAllowedValue);
                /* First value not less than the allowed one forces to include timer to the result */
                if (!$skip) {
                    break;
                }
            }

            if (!$skip) {
                $result[] = $timerId;
            }
        }

        return $result;
    }

    /**
     * Render a caption for the profiling results
     *
     * @return string
     */
    protected function _renderCaption()
    {
        $result = 'Code Profiler (Memory usage: real - %s, emalloc - %s)';
        return sprintf($result, memory_get_usage(true), memory_get_usage());
    }

    /**
     * Set threshold (minimal allowed) value for timer column.
     * Timer is being rendered if at least one of its columns is not less than the minimal allowed value.
     *
     * @param string $fetchKey
     * @param null|float|int $minAllowedValue
     */
    public function setThreshold($fetchKey, $minAllowedValue)
    {
        if ($minAllowedValue === null) {
            unset($this->_thresholds[$fetchKey]);
        } else {
            $this->_thresholds[$fetchKey] = $minAllowedValue;
        }
    }
}
