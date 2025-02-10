<?php
/**
 * Source model of customer address types
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Customer_Address_Type
{
    /**
     * Retrieve possible customer address types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            Mage_Customer_Model_Address_Abstract::TYPE_BILLING => Mage::helper('adminhtml')->__('Billing Address'),
            Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING => Mage::helper('adminhtml')->__('Shipping Address'),
        ];
    }
}
