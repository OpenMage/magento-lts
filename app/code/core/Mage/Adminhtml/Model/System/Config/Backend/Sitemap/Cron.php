<?php

/**
 * Backend Model for Currency import options
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Sitemap_Cron extends Mage_Core_Model_Config_Data
{
    public const CRON_STRING_PATH = 'crontab/jobs/sitemap_generate/schedule/cron_expr';
    public const CRON_MODEL_PATH = 'crontab/jobs/sitemap_generate/run/model';

    protected function _afterSave()
    {
        $time = $this->getData('groups/generate/fields/time/value');
        $frequency = $this->getData('groups/generate/frequency/value');

        $frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        $cronExprArray = [
            (int) $time[1],                                   # Minute
            (int) $time[0],                                   # Hour
            ($frequency == $frequencyMonthly) ? '1' : '*',          # Day of the Month
            '*',                                                    # Month of the Year
            ($frequency == $frequencyWeekly) ? '1' : '*',           # Day of the Week
        ];

        $cronExprString = implode(' ', $cronExprArray);

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            Mage::getModel('core/config_data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
        return $this;
    }
}
