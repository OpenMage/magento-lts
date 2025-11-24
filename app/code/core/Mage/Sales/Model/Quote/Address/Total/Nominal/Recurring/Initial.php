<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Total model for recurring profile initial fee
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Quote_Address_Total_Nominal_Recurring_Initial extends Mage_Sales_Model_Quote_Address_Total_Nominal_RecurringAbstract
{
    /**
     * Custom row total/profile keys
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'recurring_initial_fee';

    protected $_profileDataKey = 'init_amount';

    /**
     * Get initial fee label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('sales')->__('Initial Fee');
    }
}
