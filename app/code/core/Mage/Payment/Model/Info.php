<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment information model
 *
 * @category   Mage
 * @package    Mage_Payment
 *
 * @method Mage_Sales_Model_Order getOrder()
 * @method Mage_Sales_Model_Quote getQuote()
 *
 * @method string getAdditionalData()
 * @method $this setAdditionalData(string $value)
 * @method string getCcCid()
 * @method $this setCcCid(string $value)
 * @method string getCcCidEnc()
 * @method string getCcExpMonth()
 * @method $this setCcExpMonth(string $value)
 * @method string getCcExpYear()
 * @method $this setCcExpYear(string $value)
 * @method string getCcLast4()
 * @method $this setCcLast4(string $value)
 * @method string getCcNumber()
 * @method $this setCcNumber(string $value)
 * @method string getCcNumberEnc()
 * @method $this setCcNumberEnc(string $value)
 * @method string getCcOwner()
 * @method $this setCcOwner(string $value)
 * @method string getCcSsIssue()
 * @method $this setCcSsIssue(string $value)
 * @method string getCcSsStartMonth()
 * @method $this setCcSsStartMonth(string $value)
 * @method string getCcSsStartYear()
 * @method $this setCcSsStartYear(string $value)
 * @method string getCcType()
 * @method $this setCcType(string $value)
 * @method string getMethod()
 * @method bool hasMethodInstance()
 * @method $this setMethodInstance(false|Mage_Payment_Model_Method_Abstract $value)
 * @method $this setPoNumber(string $value)
 */
class Mage_Payment_Model_Info extends Mage_Core_Model_Abstract
{
    /**
     * Additional information container
     *
     * @var array|int
     */
    protected $_additionalInformation = -1;

    /**
     * Retrieve data
     *
     * @inheritDoc
     */
    public function getData($key = '', $index = null)
    {
        if ($key === 'cc_number') {
            if (empty($this->_data['cc_number']) && !empty($this->_data['cc_number_enc'])) {
                $this->_data['cc_number'] = $this->decrypt($this->getCcNumberEnc());
            }
        }
        if ($key === 'cc_cid') {
            if (empty($this->_data['cc_cid']) && !empty($this->_data['cc_cid_enc'])) {
                $this->_data['cc_cid'] = $this->decrypt($this->getCcCidEnc());
            }
        }
        return parent::getData($key, $index);
    }

    /**
     * Retrieve payment method model object
     *
     * @return Mage_Payment_Model_Method_Abstract
     * @throws Mage_Core_Exception
     */
    public function getMethodInstance()
    {
        if (!$this->hasMethodInstance()) {
            if ($this->getMethod()) {
                $instance = Mage::helper('payment')->getMethodInstance($this->getMethod());
                if ($instance) {
                    $instance->setInfoInstance($this);
                    $this->setMethodInstance($instance);
                    return $instance;
                }
            }
            Mage::throwException(Mage::helper('payment')->__('The requested Payment Method is not available.'));
        }

        return $this->_getData('method_instance');
    }

    /**
     * Encrypt data
     *
     * @param   string $data
     * @return  string
     */
    public function encrypt($data)
    {
        if ($data) {
            return Mage::helper('core')->encrypt($data);
        }
        return $data;
    }

    /**
     * Decrypt data
     *
     * @param   string $data
     * @return  string
     */
    public function decrypt($data)
    {
        if ($data) {
            return Mage::helper('core')->decrypt($data);
        }
        return $data;
    }

    /**
     * Additional information setter
     * Updates data inside the 'additional_information' array
     * or all 'additional_information' if key is data array
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setAdditionalInformation($key, $value = null)
    {
        if (is_object($value)) {
            Mage::throwException(Mage::helper('sales')->__('Payment disallow storing objects.'));
        }
        $this->_initAdditionalInformation();
        if (is_array($key) && is_null($value)) {
            $this->_additionalInformation = $key;
        } else {
            $this->_additionalInformation[$key] = $value;
        }
        return $this->setData('additional_information', $this->_additionalInformation);
    }

    /**
     * Getter for entire additional_information value or one of its element by key
     *
     * @param string $key
     * @return array|null|mixed
     */
    public function getAdditionalInformation($key = null)
    {
        $this->_initAdditionalInformation();
        if ($key === null) {
            return $this->_additionalInformation;
        }
        return $this->_additionalInformation[$key] ?? null;
    }

    /**
     * Unsetter for entire additional_information value or one of its element by key
     *
     * @param string $key
     * @return $this
     */
    public function unsAdditionalInformation($key = null)
    {
        if ($key && isset($this->_additionalInformation[$key])) {
            unset($this->_additionalInformation[$key]);
            return $this->setData('additional_information', $this->_additionalInformation);
        }
        $this->_additionalInformation = -1;
        return $this->unsetData('additional_information');
    }

    /**
     * Check whether there is additional information by specified key
     *
     * @param string $key
     * @return bool
     */
    public function hasAdditionalInformation($key = null)
    {
        $this->_initAdditionalInformation();
        return $key === null
            ? !empty($this->_additionalInformation)
            : array_key_exists($key, $this->_additionalInformation);
    }

    /**
     * Make sure _additionalInformation is an array
     */
    protected function _initAdditionalInformation()
    {
        if ($this->_additionalInformation === -1) {
            $this->_additionalInformation = $this->_getData('additional_information');
        }
        if ($this->_additionalInformation === null) {
            $this->_additionalInformation = [];
        }
    }
}
