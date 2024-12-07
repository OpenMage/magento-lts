<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend Model for Currency import options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Currency_Cron extends Mage_Core_Model_Config_Data
{
    public const CRON_STRING_PATH = 'crontab/jobs/currency_rates_update/schedule/cron_expr';

    protected function _afterSave()
    {
        $time = $this->getData('groups/import/fields/time/value');
        $frequency = $this->getData('groups/import/fields/frequency/value');

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
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
        return $this;
    }
}
