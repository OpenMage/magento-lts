<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Source model for available settlement report fetching intervals
 *
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
