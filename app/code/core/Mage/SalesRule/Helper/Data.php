<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_SalesRule';

    /**
     * Set store and base price which will be used during discount calculation to item object
     *
     * @param   float $basePrice
     * @param   float $price
     * @return  $this
     */
    public function setItemDiscountPrices(Mage_Sales_Model_Quote_Item_Abstract $item, $basePrice, $price)
    {
        $item->setDiscountCalculationPrice($price);
        $item->setBaseDiscountCalculationPrice($basePrice);
        return $this;
    }

    /**
     * Add additional amounts to discount calculation prices
     *
     * @param   float $basePrice
     * @param   float $price
     * @return  $this
     */
    public function addItemDiscountPrices(Mage_Sales_Model_Quote_Item_Abstract $item, $basePrice, $price)
    {
        $discountPrice      = $item->getDiscountCalculationPrice();
        $baseDiscountPrice  = $item->getBaseDiscountCalculationPrice();

        if ($discountPrice || $baseDiscountPrice || $basePrice || $price) {
            $discountPrice      = $discountPrice ? $discountPrice : $item->getCalculationPrice();
            $baseDiscountPrice  = $baseDiscountPrice ? $baseDiscountPrice : $item->getBaseCalculationPrice();
            $this->setItemDiscountPrices($item, $baseDiscountPrice + $basePrice, $discountPrice + $price);
        }

        return $this;
    }
}
