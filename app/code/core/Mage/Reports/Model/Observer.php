<?php

/**
 * Reports Observer
 */
class Mage_Reports_Model_Observer
{
    /**
     * Refresh viewed report statistics for last day
     *
     * @param  Mage_Cron_Model_Schedule $schedule
     * @return $this
     */
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
