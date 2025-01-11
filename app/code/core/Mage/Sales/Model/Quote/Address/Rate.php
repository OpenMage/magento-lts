<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Quote_Address_Rate _getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Rate getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Rate_Collection getCollection()
 *
 * @method int getAddressId()
 * @method $this setAddressId(int $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 * @method string getCarrier()
 * @method $this setCarrier(string $value)
 * @method string getCarrierTitle()
 * @method $this setCarrierTitle(string $value)
 * @method string getCode()
 * @method $this setCode(string $value)
 * @method string getMethod()
 * @method $this setMethod(string $value)
 * @method string getMethodDescription()
 * @method $this setMethodDescription(string $value)
 * @method float getPrice()
 * @method $this setPrice(float $value)
 * @method string getErrorMessage()
 * @method $this setErrorMessage(string $value)
 * @method string getMethodTitle()
 * @method $this setMethodTitle(string $value)
 */
class Mage_Sales_Model_Quote_Address_Rate extends Mage_Shipping_Model_Rate_Abstract
{
    protected $_address;

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
