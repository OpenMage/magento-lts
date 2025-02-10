<?php
/**
 * Refresh viewed report statistics for last day
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @param Mage_Cron_Model_Schedule $schedule
 * @return $this
 */
/**
 * Reports Observer
 */
class Mage_Reports_Model_Observer
{
    
    public function aggregateReportsReportProductViewedData($schedule)
    {
        Mage::app()->getLocale()->emulate(0);
        $currentDate = Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);
        Mage::getResourceModel('reports/report_product_viewed')->aggregate($date);
        Mage::app()->getLocale()->revert();
        return $this;
    }
}
