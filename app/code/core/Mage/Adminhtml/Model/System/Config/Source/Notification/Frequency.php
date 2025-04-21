<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * AdminNotification update frequency source
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Notification_Frequency
{
    public function toOptionArray()
    {
        return [
            1   => Mage::helper('adminhtml')->__('1 Hour'),
            2   => Mage::helper('adminhtml')->__('2 Hours'),
            6   => Mage::helper('adminhtml')->__('6 Hours'),
            12  => Mage::helper('adminhtml')->__('12 Hours'),
            24  => Mage::helper('adminhtml')->__('24 Hours'),
        ];
    }
}
