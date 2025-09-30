<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Backend_Cron extends Mage_Core_Model_Config_Data
{
    public const CRON_STRING_PATH = 'crontab/jobs/paypal_fetch_settlement_reports/schedule/cron_expr';
    public const CRON_MODEL_PATH_INTERVAL = 'paypal/fetch_reports/schedule';

    /**
     * Cron settings after save
     *
     * {@inheritDoc}
     */
    protected function _afterSave()
    {
        $cronExprString = '';
        $time = explode(',', Mage::getModel('core/config_data')->load('paypal/fetch_reports/time', 'path')->getValue());
        if (Mage::getModel('core/config_data')->load('paypal/fetch_reports/active', 'path')->getValue()) {
            $interval = Mage::getModel('core/config_data')->load(self::CRON_MODEL_PATH_INTERVAL, 'path')->getValue();
            $cronExprString = "{$time[1]} {$time[0]} */{$interval} * *";
        }

        Mage::getModel('core/config_data')
            ->load(self::CRON_STRING_PATH, 'path')
            ->setValue($cronExprString)
            ->setPath(self::CRON_STRING_PATH)
            ->save();

        return parent::_afterSave();
    }
}
