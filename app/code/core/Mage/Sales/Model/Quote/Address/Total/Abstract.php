<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Quote Address Total  abstract model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Total Code name
     *
     * @var string
     */
    protected $_code;
    protected $_address = null;

    /**
     * Set total code code name
     *
     * @param string $code
     * @return Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * Retrieve total code name
     *
     * @return unknown
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Collect totals process.
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_setAddress($address);
        /**
         * Reset amounts
         */
        $this->_setAmount(0);
        $this->_setBaseAmount(0);
        return $this;
    }

    /**
     * Fetch (Retrieve data as array)
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return array
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_setAddress($address);
        return array();
    }

    /**
     * Set address shich can be used inside totals calculation
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _setAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        return $this;
    }

    /**
     * Get quote address object
     *
     * @throw   Mage_Core_Exception if address not declared
     * @return  Mage_Sales_Model_Quote_Address
     */
    protected function _getAddress()
    {
        if ($this->_address === null) {
            Mage::throwException(
                Mage::helper('sales')->__('Address model is not defined')
            );
        }
        return $this->_address;
    }

    /**
     * Set total model amount value to address
     *
     * @param   float $amount
     * @return  Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _setAmount($amount)
    {
        $this->_getAddress()->setTotalAmount($this->getCode(), $amount);
        return $this;
    }

    /**
     * Set total model base amount value to address
     *
     * @param   float $amount
     * @return  Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _setBaseAmount($baseAmount)
    {
        $this->_getAddress()->setBaseTotalAmount($this->getCode(), $baseAmount);
        return $this;
    }

    /**
     * Add total model amount value to address
     *
     * @param   float $amount
     * @return  Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _addAmount($amount)
    {
        $this->_getAddress()->addTotalAmount($this->getCode(),$amount);
        return $this;
    }

    /**
     * Add total model base amount value to address
     *
     * @param   float $amount
     * @return  Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _addBaseAmount($baseAmount)
    {
        $this->_getAddress()->addBaseTotalAmount($this->getCode(), $baseAmount);
        return $this;
    }

    /**
     * Process model configuration array.
     * This method can be used for changing models apply sort order
     *
     * @param   array $config
     * @param   store $store
     * @return  array
     */
    public function processConfigArray($config, $store)
    {
        return $config;
    }
}
