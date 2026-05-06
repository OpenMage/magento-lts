<?php

declare(strict_types=1);

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

    public function getMethod(): string
    {
        return (string) $this->_getData('method');
    }

    public function getPrice(): float
    {
        return (float) $this->_getData('price');
    }

    public function setCarrier(string $value): static
    {
        return $this->setData('carrier', $value);
    }

    public function setCarrierTitle(string $value): static
    {
        return $this->setData('carrier_title', $value);
    }

    public function setCost(float $value): static
    {
        return $this->setData('cost', $value);
    }

    public function setMethod(string $value): static
    {
        return $this->setData('method', $value);
    }

    public function setMethodTitle(string $value): static
    {
        return $this->setData('method_title', $value);
    }
}
