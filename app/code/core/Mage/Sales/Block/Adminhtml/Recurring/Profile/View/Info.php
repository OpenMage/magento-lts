<?php
/**
 * Recurring profile info block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
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
