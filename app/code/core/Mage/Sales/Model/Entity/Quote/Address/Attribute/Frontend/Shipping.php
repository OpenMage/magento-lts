<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Shipping extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    /**
     * @return $this
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getShippingAmount();
        if ($amount != 0) {
            $title = Mage::helper('sales')->__('Shipping & Handling');
            if ($address->getShippingDescription()) {
                $title .= ' (' . $address->getShippingDescription() . ')';
            }
            $address->addTotal([
                'code' => 'shipping',
                'title' => $title,
                'value' => $address->getShippingAmount(),
            ]);
        }
        return $this;
    }
}
