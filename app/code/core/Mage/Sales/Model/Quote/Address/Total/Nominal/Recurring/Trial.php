<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Total model for recurring profile trial payment
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Quote_Address_Total_Nominal_Recurring_Trial extends Mage_Sales_Model_Quote_Address_Total_Nominal_RecurringAbstract
{
    /**
     * Custom row total/profile keys
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'recurring_trial_payment';

    protected $_profileDataKey = 'trial_billing_amount';

    /**
     * Get trial payment label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('sales')->__('Trial Payment');
    }

    /**
     * Prevent compounding nominal subtotal in case if the trial payment exists
     *
     * @see Mage_Sales_Model_Quote_Address_Total_Nominal_Subtotal
     * @param Mage_Sales_Model_Quote_Address $address
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     */
    protected function _afterCollectSuccess($address, $item)
    {
        $item->setData('skip_compound_row_total', true);
    }
}
