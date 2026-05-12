<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Quote_Address_Rate            _getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Rate_Collection getCollection()
 * @method Mage_Sales_Model_Resource_Quote_Address_Rate            getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Rate_Collection getResourceCollection()
 */
class Mage_Sales_Model_Quote_Address_Rate extends Mage_Shipping_Model_Rate_Abstract
{
    protected $_address;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/quote_address_rate');
    }

    /**
     * @return $this
     */
    #[Override]
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->getAddress()) {
            $this->setAddressId($this->getAddress()->getId());
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * @return $this
     */
    public function importShippingRate(Mage_Shipping_Model_Rate_Result_Abstract $rate)
    {
        if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error) {
            $this
                ->setCode($rate->getCarrier() . '_error')
                ->setCarrier($rate->getCarrier())
                ->setCarrierTitle($rate->getCarrierTitle())
                ->setErrorMessage($rate->getErrorMessage())
            ;
        } elseif ($rate instanceof Mage_Shipping_Model_Rate_Result_Method) {
            $this
                ->setCode($rate->getCarrier() . '_' . $rate->getMethod())
                ->setCarrier($rate->getCarrier())
                ->setCarrierTitle($rate->getCarrierTitle())
                ->setMethod($rate->getMethod())
                ->setMethodTitle($rate->getMethodTitle())
                ->setMethodDescription($rate->getMethodDescription())
                ->setPrice($rate->getPrice())
            ;
        }

        return $this;
    }

    public function getAddressId(): int
    {
        return (int) $this->_getData('address_id');
    }

    public function setAddressId(int $value): static
    {
        return $this->setData('address_id', $value);
    }

    public function getCarrier(): string
    {
        return (string) $this->_getData('carrier');
    }

    public function setCarrier(string $value): static
    {
        return $this->setData('carrier', $value);
    }

    public function getCarrierSortOrder(): ?int
    {
        $value = $this->_getData('carrier_sort_order');
        return $value !== null ? (int) $value : null;
    }

    public function setCarrierSortOrder(int $value): static
    {
        return $this->setData('carrier_sort_order', $value);
    }

    public function getCarrierTitle(): ?string
    {
        $value = $this->_getData('carrier_title');
        return $value !== null ? (string) $value : null;
    }

    public function setCarrierTitle(string $value): static
    {
        return $this->setData('carrier_title', $value);
    }

    public function getCode(): string
    {
        return (string) $this->_getData('code');
    }

    public function setCode(string $value): static
    {
        return $this->setData('code', $value);
    }

    public function getErrorMessage(): ?string
    {
        $value = $this->_getData('error_message');
        return $value !== null ? (string) $value : null;
    }

    public function setErrorMessage(string $value): static
    {
        return $this->setData('error_message', $value);
    }

    public function getMethod(): ?string
    {
        $value = $this->_getData('method');
        return $value !== null ? (string) $value : null;
    }

    public function setMethod(string $value): static
    {
        return $this->setData('method', $value);
    }

    public function getMethodDescription(): ?string
    {
        $value = $this->_getData('method_description');
        return $value !== null ? (string) $value : null;
    }

    public function setMethodDescription(string $value): static
    {
        return $this->setData('method_description', $value);
    }

    public function getMethodTitle(): ?string
    {
        $value = $this->_getData('method_title');
        return $value !== null ? (string) $value : null;
    }

    public function setMethodTitle(string $value): static
    {
        return $this->setData('method_title', $value);
    }

    public function getPrice(): float
    {
        return (float) $this->_getData('price');
    }

    public function setPrice(float $value): static
    {
        return $this->setData('price', $value);
    }
}
