<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Model to calculate grand total or an order
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Quote_Address_Total_Grand extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Collect grand total address amount
     *
     * @return Mage_Sales_Model_Quote_Address_Total_Grand
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $grandTotal     = $address->getGrandTotal();
        $baseGrandTotal = $address->getBaseGrandTotal();

        $store      = $address->getQuote()->getStore();
        $totals     = array_sum($address->getAllTotalAmounts());
        $totals     = $store->roundPrice($totals);

        $baseTotals = array_sum($address->getAllBaseTotalAmounts());
        $baseTotals = $store->roundPrice($baseTotals);

        $address->setGrandTotal($grandTotal + $totals);
        $address->setBaseGrandTotal($baseGrandTotal + $baseTotals);
        return $this;
    }

    /**
     * Add grand total information to address
     *
     * @return Mage_Sales_Model_Quote_Address_Total_Grand
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal([
            'code'  => $this->getCode(),
            'title' => Mage::helper('sales')->__('Grand Total'),
            'value' => $address->getGrandTotal(),
            'area'  => 'footer',
        ]);
        return $this;
    }
}
