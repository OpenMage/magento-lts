<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Log Cron Backend Model
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Log_Cron extends Mage_Core_Model_Config_Data
{
    public const CRON_STRING_PATH  = 'crontab/jobs/log_clean/schedule/cron_expr';

    public const CRON_MODEL_PATH   = 'crontab/jobs/log_clean/run/model';

    /**
     * Cron settings after save
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $enabled    = $this->getData('groups/log/fields/enabled/value');
        $time       = $this->getData('groups/log/fields/time/value');
        $frequency   = $this->getData('groups/log/fields/frequency/value');

        $frequencyWeekly    = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly   = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        if ($enabled) {
            $cronExprArray = [
                (int) $time[1],                                   # Minute
                (int) $time[0],                                   # Hour
                ($frequency == $frequencyMonthly) ? '1' : '*',          # Day of the Month
                '*',                                                    # Month of the Year
                ($frequency == $frequencyWeekly) ? '1' : '*',           # Day of the Week
            ];
            $cronExprString = implode(' ', $cronExprArray);
        } else {
            $cronExprString = '';
        }

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
        } catch (Exception) {
            Mage::throwException(Mage::helper('adminhtml')->__('Unable to save the cron expression.'));
        }

        return $this;
    }
}
