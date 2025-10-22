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
class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Tax extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    /**
     * @return $this
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getTaxAmount();
        if ($amount != 0) {
            $address->addTotal([
                'code' => 'tax',
                'title' => Mage::helper('sales')->__('Tax'),
                'value' => $amount,
            ]);
        }

        return $this;
    }
}
