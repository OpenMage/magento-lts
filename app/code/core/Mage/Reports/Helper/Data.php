<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * @package    Mage_Reports
 */
class Mage_Reports_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const REPORT_PERIOD_TYPE_DAY    = 'day';

    public const REPORT_PERIOD_TYPE_MONTH  = 'month';

    public const REPORT_PERIOD_TYPE_YEAR   = 'year';

    public const PERIOD_CUSTOM      = 'custom';

    public const PERIOD_24_HOURS    = '24h';

    public const PERIOD_7_DAYS      = '7d';

    public const PERIOD_1_MONTH     = '1m';

    public const PERIOD_3_MONTHS    = '3m';

    public const PERIOD_6_MONTHS    = '6m';

    public const PERIOD_1_YEAR      = '1y';

    public const PERIOD_2_YEARS     = '2y';

    public const XML_PATH_REPORTS_ENABLED  = 'reports/general/enabled';

    protected $_moduleName = 'Mage_Reports';

    /**
     * Return reports flag enabled.
     *
     * @return bool
     */

    public function isReportsEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REPORTS_ENABLED);
    }

    /**
     * Retrieve array of intervals
     *
     * @param string $from
     * @param string $to
     * @param self::REPORT_PERIOD_TYPE_* $period
     * @return array
     * @throws Zend_Date_Exception
     */
    public function getIntervals($from, $to, $period = self::REPORT_PERIOD_TYPE_DAY)
    {
        $intervals = [];
        $dateStart = null;

        if (!$from && !$to) {
            return $intervals;
        }

        $start = new Zend_Date($from, Varien_Date::DATE_INTERNAL_FORMAT);

        if ($period == self::REPORT_PERIOD_TYPE_DAY) {
            $dateStart = $start;
        }

        if ($period == self::REPORT_PERIOD_TYPE_MONTH) {
            $dateStart = new Zend_Date(date('Y-m', $start->getTimestamp()), Varien_Date::DATE_INTERNAL_FORMAT);
        }

        if ($period == self::REPORT_PERIOD_TYPE_YEAR) {
            $dateStart = new Zend_Date(date('Y', $start->getTimestamp()), Varien_Date::DATE_INTERNAL_FORMAT);
        }

        if (!$period || !$dateStart) {
            return $intervals;
        }

        $dateEnd = new Zend_Date($to, Varien_Date::DATE_INTERNAL_FORMAT);

        while ($dateStart->compare($dateEnd) <= 0) {
            $time = '';
            switch ($period) {
                case self::REPORT_PERIOD_TYPE_DAY:
                    $time = $dateStart->toString('yyyy-MM-dd');
                    $dateStart->addDay(1);
                    break;
                case self::REPORT_PERIOD_TYPE_MONTH:
                    $time = $dateStart->toString('yyyy-MM');
                    $dateStart->addMonth(1);
                    break;
                case self::REPORT_PERIOD_TYPE_YEAR:
                    $time = $dateStart->toString('yyyy');
                    $dateStart->addYear(1);
                    break;
            }

            $intervals[] = $time;
        }

        return  $intervals;
    }

    /**
     * @param Varien_Data_Collection $collection
     * @param string $from
     * @param string $to
     * @param string $periodType
     */
    public function prepareIntervalsCollection($collection, $from, $to, $periodType = self::REPORT_PERIOD_TYPE_DAY)
    {
        $intervals = $this->getIntervals($from, $to, $periodType);

        foreach ($intervals as $interval) {
            $item = Mage::getModel('adminhtml/report_item');
            $item->setPeriod($interval);
            $item->setIsEmpty();
            $collection->addItem($item);
        }
    }
}
