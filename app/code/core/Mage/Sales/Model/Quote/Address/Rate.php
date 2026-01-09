<?php

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
 * @method int                                                     getAddressId()
 * @method string                                                  getCarrier()
 * @method string                                                  getCarrierTitle()
 * @method string                                                  getCode()
 * @method Mage_Sales_Model_Resource_Quote_Address_Rate_Collection getCollection()
 * @method string                                                  getErrorMessage()
 * @method string                                                  getMethod()
 * @method string                                                  getMethodDescription()
 * @method string                                                  getMethodTitle()
 * @method float                                                   getPrice()
 * @method Mage_Sales_Model_Resource_Quote_Address_Rate            getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Rate_Collection getResourceCollection()
 * @method $this                                                   setAddressId(int $value)
 * @method $this                                                   setCarrier(string $value)
 * @method $this                                                   setCarrierTitle(string $value)
 * @method $this                                                   setCode(string $value)
 * @method $this                                                   setErrorMessage(string $value)
 * @method $this                                                   setMethod(string $value)
 * @method $this                                                   setMethodDescription(string $value)
 * @method $this                                                   setMethodTitle(string $value)
 * @method $this                                                   setPrice(float $value)
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
}
