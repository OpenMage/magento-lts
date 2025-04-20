<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Carbon\Carbon;

/**
 * @category   Mage
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
     * @param string $dateFrom
     * @param string $dateTo
     * @param self::REPORT_PERIOD_TYPE_* $period
     * @return array
     */
    public function getIntervals($dateFrom, $dateTo, $period = self::REPORT_PERIOD_TYPE_DAY)
    {
        $intervals = [];
        if (!$dateFrom && !$dateTo) {
            return $intervals;
        }

        $dateStart  = Carbon::now()->setTimeFromTimeString($dateFrom)->format('Y-m-d');
        $endDate    = Carbon::now()->setTimeFromTimeString($dateTo)->format('Y-m-d');

        switch ($period) {
            case self::REPORT_PERIOD_TYPE_DAY:
                $format     = 'YYYY-MM-DD';
                $modifier   = 'addDay';
                break;
            case self::REPORT_PERIOD_TYPE_MONTH:
                $format     = 'YYYY-MM';
                $modifier   = 'addMonth';
                break;
            case self::REPORT_PERIOD_TYPE_YEAR:
            default:
                $format     = 'YYYY';
                $modifier   = 'addYear';
                $dateStart->addYear();
                break;
        }

        for ($date = $dateStart->copy(); $date->lte($endDate); $date->$modifier()) {
            $intervals[] = $date->isoFormat($format);
        }

        return  $intervals;
    }

    /**
     * @param Varien_Data_Collection $collection
     * @param string $dateFrom
     * @param string $dateTo
     * @param self::REPORT_PERIOD_TYPE_* $periodType
     */
    public function prepareIntervalsCollection($collection, $dateFrom, $dateTo, $periodType = self::REPORT_PERIOD_TYPE_DAY)
    {
        $intervals = $this->getIntervals($dateFrom, $dateTo, $periodType);

        foreach ($intervals as $interval) {
            $item = Mage::getModel('adminhtml/report_item');
            $item->setPeriod($interval);
            $item->setIsEmpty();
            $collection->addItem($item);
        }
    }
}
