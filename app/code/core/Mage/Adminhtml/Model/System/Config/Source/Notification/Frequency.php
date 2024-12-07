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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AdminNotification update frequency source
 *
 * @category   Mage
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
