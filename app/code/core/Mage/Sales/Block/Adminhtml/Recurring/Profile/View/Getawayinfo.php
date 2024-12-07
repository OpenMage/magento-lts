<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Recurring profile getaway info block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Recurring_Profile_View_Getawayinfo extends Mage_Adminhtml_Block_Widget
{
    /**
     * Return recurring profile getaway information for view
     *
     * @return array
     */
    public function getRecurringProfileGetawayInformation()
    {
        $recurringProfile = Mage::registry('current_recurring_profile');
        $information = [];
        foreach ($recurringProfile->getData() as $kay => $value) {
            $information[$recurringProfile->getFieldLabel($kay)] = $value;
        }
        return $information;
    }
}
