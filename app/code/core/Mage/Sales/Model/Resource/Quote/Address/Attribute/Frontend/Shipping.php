<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Quote address attribute frontend shipping resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Shipping extends Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Fetch totals
     *
     * @return $this
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getShippingAmount();
        if ($amount != 0) {
            $title = Mage::helper('sales')->__('Shipping & Handling');
            if ($address->getShippingDescription()) {
                $title .= sprintf(' (%s)', $address->getShippingDescription());
            }
            $address->addTotal([
                'code'  => 'shipping',
                'title' => $title,
                'value' => $address->getShippingAmount(),
            ]);
        }
        return $this;
    }
}
