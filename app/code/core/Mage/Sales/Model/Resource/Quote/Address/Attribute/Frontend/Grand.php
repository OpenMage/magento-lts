<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Quote address attribute frontend grand resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Grand extends Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Fetch grand total
     *
     * @return $this
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal([
            'code'  => 'grand_total',
            'title' => Mage::helper('sales')->__('Grand Total'),
            'value' => $address->getGrandTotal(),
            'area'  => 'footer',
        ]);
        return $this;
    }
}
