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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Express Onepage checkout block
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Express_Review extends Mage_Core_Block_Template
{
    protected $_method='express';
    /**
     * Get PayPal Express Review Information
     *
     * @return Mage_Paypal_Model_Express_Review
     */
    public function getReview()
    {
        return Mage::getSingleton('paypal/express_review');
    }

    /**
     * Return quote billing address
     *
     * @return Mage_Sales_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->getReview()->getQuote()->getBillingAddress();
    }

    /**
     * Return quote shipping address
     *
     * @return Mage_Sales_Quote_Address
     */
    public function getShippingAddress()
    {
        return $this->getReview()->getQuote()->getShippingAddress();
    }

    /**
     * Return address base on quote shipping address
     *
     * @return Mage_Sales_Quote_Address
     */
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getReview()->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    /**
     * Return shipping rates
     *
     * @return array
     */
    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            #$this->getAddress()->collectShippingRates()->save();

            $groups = $this->getAddress()->getGroupedAllShippingRates();
            /*if (!empty($groups)) {
                $ratesFilter = new Varien_Filter_Object_Grid();
                $ratesFilter->addFilter(Mage::app()->getStore()->getPriceFilter(), 'price');

                foreach ($groups as $code => $groupItems) {
                    $groups[$code] = $ratesFilter->filter($groupItems);
                }
            }*/
            return $this->_rates = $groups;
        }
        return $this->_rates;
    }

    /**
     * Return carrier name from config, base on carrier code
     *
     * @param $carrierCode string
     * @return string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    /**
     * get shipping method
     *
     * @return string
     */
    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    /**
     * set payment method.
     *
     * @param $varName string
     */
    public function setMethod($varName)
    {
        $this->_method=$varName;
    }

    /**
     * Return formated shipping price
     *
     * @param $price float
     * @param $flag bool
     *
     * @return bool
     */
    public function getShippingPrice($price, $flag)
    {
        return $this->formatPrice($this->helper('tax')->getShippingPrice($price, $flag, $this->getAddress()));
    }

    /**
     * Format price base on store convert price method
     *
     * @param $price float
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getReview()->getQuote()->getStore()->convertPrice($price, true);
    }

    /**
     * Return quote virtual status
     *
     * @return bool
     */
    public function isVirtual()
    {
        return $this->getReview()->getQuote()->getIsVirtual();
    }
}
