<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote address attribute frontend subtotal resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Subtotal extends Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Add total
     *
     * @return $this
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal([
            'code'  => 'subtotal',
            'title' => Mage::helper('sales')->__('Subtotal'),
            'value' => $address->getSubtotal(),
        ]);

        return $this;
    }
}
