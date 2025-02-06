<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Shipping
 */

/**
 * Fields:
 * - carrier: ups
 * - carrierTitle: United Parcel Service
 * - method: 2day
 * - methodTitle: UPS 2nd Day Priority
 * - price: $9.40 (cost+handling)
 * - cost: $8.00
 *
 * @category   Mage
 * @package    Mage_Shipping
 *
 * @method $this setCarrier(string $value)
 * @method $this setCarrierTitle(string $value)
 * @method string getMethod()
 * @method $this setMethod(string $value)
 * @method $this setMethodTitle(string $value)
 * @method float getPrice()
 * @method $this setCost(float $value)
 */
class Mage_Shipping_Model_Rate_Result_Method extends Mage_Shipping_Model_Rate_Result_Abstract
{
    /**
     * Round shipping carrier's method price
     *
     * @param string|float|int $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->setData('price', Mage::app()->getStore()->roundPrice($price));
        return $this;
    }
}
