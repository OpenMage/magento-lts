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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source model for available settlement report fetching intervals
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Source_FetchingSchedule
{
    public function toOptionArray()
    {
        return [
            1 => Mage::helper('paypal')->__('Daily'),
            3 => Mage::helper('paypal')->__('Every 3 days'),
            7 => Mage::helper('paypal')->__('Every 7 days'),
            10 => Mage::helper('paypal')->__('Every 10 days'),
            14 => Mage::helper('paypal')->__('Every 14 days'),
            30 => Mage::helper('paypal')->__('Every 30 days'),
            40 => Mage::helper('paypal')->__('Every 40 days'),
        ];
    }
}
