<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
 * @package    Mage_Shipping
 *
 * @method string getMethod()
 * @method float  getPrice()
 * @method $this  setCarrier(string $value)
 * @method $this  setCarrierTitle(string $value)
 * @method $this  setCost(float $value)
 * @method $this  setMethod(string $value)
 * @method $this  setMethodTitle(string $value)
 */
class Mage_Shipping_Model_Rate_Result_Method extends Mage_Shipping_Model_Rate_Result_Abstract
{
    /**
     * Round shipping carrier's method price
     *
     * @param  float|int|string $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->setData('price', Mage::app()->getStore()->roundPrice($price));
        return $this;
    }
}
