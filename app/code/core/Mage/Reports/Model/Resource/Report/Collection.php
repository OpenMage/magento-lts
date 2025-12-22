<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

use Carbon\Carbon;

/**
 * Report Reviews collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Report_Collection
{
    /**
     * From value
     *
     * @var Zend_Date
     */
    protected $_from;

    /**
     * To value
     *
     * @var Zend_Date
     */
    protected $_to;

    /**
     * Report period
     *
     * @var string
     */
    protected $_period;

    /**
     * Model object
     *
     * @var Mage_Reports_Model_Report
     */
    protected $_model;

    /**
     * Intervals
     *
     * @var array
     */
    protected $_intervals;

    /**
     * Page size
     *
     * @var int
     */
    protected $_pageSize;

    /**
     * Array of store ids
     *
     * @var array
     */
    protected $_storeIds;

    /**
     * @inheritDoc
     */
    protected function _construct() {}

    /**
     * Set period
     *
     * @param  string $period
     * @return $this
     */
    public function setPeriod($period)
    {
        $this->_period = $period;
        return $this;
    }

    /**
     * Set interval
     *
     * @param  Zend_Date $from
     * @param  Zend_Date $to
     * @return $this
     */
    public function setInterval($from, $to)
    {
        $this->_from = $from;
        $this->_to   = $to;

        return $this;
    }

    /**
     * Get intervals
     *
     * @return array
     * @throws Zend_Date_Exception
     */
    public function getIntervals()
    {
        if (!$this->_intervals) {
            $this->_intervals = [];
            if (!$this->_from && !$this->_to) {
                return $this->_intervals;
            }

            $dateStart  = new Zend_Date($this->_from);
            $dateEnd    = new Zend_Date($this->_to);

            $time = [];
            $firstInterval = true;
            while ($dateStart->compare($dateEnd) <= 0) {
                switch ($this->_period) {
                    case Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_DAY:
                        $time['title'] = $dateStart->toString(Mage::app()->getLocale()->getDateFormat());
                        $time['start'] = $dateStart->toString('yyyy-MM-dd HH:mm:ss');
                        $time['end'] = $dateStart->toString('yyyy-MM-dd 23:59:59');
                        $dateStart->addDay(1);
                        break;
                    case Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_MONTH:
                        $time['title'] = $dateStart->toString('MM/yyyy');
                        $time['start'] = ($firstInterval) ? $dateStart->toString('yyyy-MM-dd 00:00:00')
                            : $dateStart->toString('yyyy-MM-01 00:00:00');

                        $lastInterval = ($dateStart->compareMonth($dateEnd->getMonth()) == 0);

                        $time['end'] = ($lastInterval) ? $dateStart->setDay($dateEnd->getDay())
                            ->toString('yyyy-MM-dd 23:59:59')
                            : $dateStart->toString('yyyy-MM-' . Carbon::createFromTimestamp($dateStart->getTimestamp())->format('t') . ' 23:59:59');

                        $dateStart->addMonth(1);

                        if ($dateStart->compareMonth($dateEnd->getMonth()) == 0) {
                            $dateStart->setDay(1);
                        }

                        $firstInterval = false;
                        break;
                    case Mage_Reports_Helper_Data::REPORT_PERIOD_TYPE_YEAR:
                        $time['title'] = $dateStart->toString('yyyy');
                        $time['start'] = ($firstInterval) ? $dateStart->toString('yyyy-MM-dd 00:00:00')
                            : $dateStart->toString('yyyy-01-01 00:00:00');

                        $lastInterval = ($dateStart->compareYear($dateEnd->getYear()) == 0);

                        $time['end'] = ($lastInterval) ? $dateStart->setMonth($dateEnd->getMonth())
                            ->setDay($dateEnd->getDay())->toString('yyyy-MM-dd 23:59:59')
                            : $dateStart->toString('yyyy-12-31 23:59:59');
                        $dateStart->addYear(1);

                        if ($dateStart->compareYear($dateEnd->getYear()) == 0) {
                            $dateStart->setMonth(1)->setDay(1);
                        }

                        $firstInterval = false;
                        break;
                }

                $this->_intervals[$time['title']] = $time;
            }
        }

        return  $this->_intervals;
    }

    /**
     * Return date periods
     *
     * @return array
     */
    public function getPeriods()
    {
        return [
            'day'   => Mage::helper('reports')->__('Day'),
            'month' => Mage::helper('reports')->__('Month'),
            'year'  => Mage::helper('reports')->__('Year'),
        ];
    }

    /**
     * Set store ids
     *
     * @param  array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    /**
     * Get store ids
     *
     * @return array
     */
    public function getStoreIds()
    {
        return $this->_storeIds;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return count($this->getIntervals());
    }

    /**
     * Set page size
     *
     * @param  int   $size
     * @return $this
     */
    public function setPageSize($size)
    {
        $this->_pageSize = $size;
        return $this;
    }

    /**
     * Get page size
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->_pageSize;
    }

    /**
     * Init report
     *
     * @param  string $modelClass
     * @return $this
     */
    public function initReport($modelClass)
    {
        $this->_model = Mage::getModel('reports/report')
            ->setPageSize($this->getPageSize())
            ->setStoreIds($this->getStoreIds())
            ->initCollection($modelClass);

        return $this;
    }

    /**
     * get report full
     *
     * @param  string                    $from
     * @param  string                    $to
     * @return Mage_Reports_Model_Report
     */
    public function getReportFull($from, $to)
    {
        return $this->_model->getReportFull($this->timeShift($from), $this->timeShift($to));
    }

    /**
     * Get report
     *
     * @param  string                    $from
     * @param  string                    $to
     * @return Mage_Reports_Model_Report
     */
    public function getReport($from, $to)
    {
        return $this->_model->getReport($this->timeShift($from), $this->timeShift($to));
    }

    /**
     * Retrieve time shift
     *
     * @param  string $datetime
     * @return string
     */
    public function timeShift($datetime)
    {
        return Mage::app()->getLocale()
            ->utcDate(null, $datetime, true, Varien_Date::DATETIME_INTERNAL_FORMAT)
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }
}
