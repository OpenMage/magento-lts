<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
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
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const REPORT_PERIOD_TYPE_DAY    = 'day';
    public const REPORT_PERIOD_TYPE_MONTH  = 'month';
    public const REPORT_PERIOD_TYPE_YEAR   = 'year';

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
     * @param string $period
     * @return array
     */
    public function getIntervals($from, $to, $period = self::REPORT_PERIOD_TYPE_DAY)
    {
        $intervals = [];
        if (!$from && !$to) {
            return $intervals;
        }

        $start = new Zend_Date($from, Varien_Date::DATE_INTERNAL_FORMAT);

        if ($period == self::REPORT_PERIOD_TYPE_DAY) {
            $dateStart = $start;
        }

        if ($period == self::REPORT_PERIOD_TYPE_MONTH) {
            $dateStart = new Zend_Date(date("Y-m", $start->getTimestamp()), Varien_Date::DATE_INTERNAL_FORMAT);
        }

        if ($period == self::REPORT_PERIOD_TYPE_YEAR) {
            $dateStart = new Zend_Date(date("Y", $start->getTimestamp()), Varien_Date::DATE_INTERNAL_FORMAT);
        }

        $dateEnd = new Zend_Date($to, Varien_Date::DATE_INTERNAL_FORMAT);

        while ($dateStart->compare($dateEnd) <= 0) {
            switch ($period) {
                case self::REPORT_PERIOD_TYPE_DAY:
                    $t = $dateStart->toString('yyyy-MM-dd');
                    $dateStart->addDay(1);
                    break;
                case self::REPORT_PERIOD_TYPE_MONTH:
                    $t = $dateStart->toString('yyyy-MM');
                    $dateStart->addMonth(1);
                    break;
                case self::REPORT_PERIOD_TYPE_YEAR:
                    $t = $dateStart->toString('yyyy');
                    $dateStart->addYear(1);
                    break;
            }
            $intervals[] = $t;
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
