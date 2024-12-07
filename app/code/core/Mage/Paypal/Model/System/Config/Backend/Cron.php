<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
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
