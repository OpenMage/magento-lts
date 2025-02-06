<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Recurring profile info block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Recurring_Profile_View_Info extends Mage_Adminhtml_Block_Widget
{
    /**
     * Return recurring profile information for view
     *
     * @return array
     */
    public function getRecurringProfileInformation()
    {
        $recurringProfile = Mage::registry('current_recurring_profile');
        $information = [];
        foreach ($recurringProfile->getData() as $kay => $value) {
            $information[$recurringProfile->getFieldLabel($kay)] = $value;
        }
        return $information;
    }
}
