<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Paypal Express Onepage checkout block
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Express_Review extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Currently selected shipping rate
     *
     * @var Mage_Sales_Model_Quote_Address_Rate
     */
    protected $_currentShippingRate;

    /**
     * Paypal action prefix
     *
     * @var string
     */
    protected $_paypalActionPrefix = 'paypal';

    /**
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address;

    /**
     * Quote object setter
     *
     * @return $this
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Return quote billing address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->_quote->getBillingAddress();
    }

    /**
     * Return quote shipping address
     *
     * @return Mage_Sales_Model_Quote_Address|false
     */
    public function getShippingAddress()
    {
        if ($this->_quote->getIsVirtual()) {
            return false;
        }

        return $this->_quote->getShippingAddress();
    }

    /**
     * Get HTML output for specified address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    public function renderAddress($address)
    {
        return $address->getFormated(true);
    }

    /**
     * Return carrier name from config, base on carrier code
     *
     * @param string $carrierCode
     * @return string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig("carriers/{$carrierCode}/title")) {
            return $name;
        }

        return $carrierCode;
    }

    /**
     * Get either shipping rate code or empty value on error
     *
     * @return string
     */
    public function renderShippingRateValue(Varien_Object $rate)
    {
        if ($rate->getErrorMessage()) {
            return '';
        }

        return $rate->getCode();
    }

    /**
     * Get shipping rate code title and its price or error message
     *
     * @param Varien_Object $rate
     * @param string $format
     * @param string $inclTaxFormat
     * @return string
     */
    public function renderShippingRateOption($rate, $format = '%s - %s%s', $inclTaxFormat = ' (%s %s)')
    {
        $renderedInclTax = '';
        if ($rate->getErrorMessage()) {
            $price = $rate->getErrorMessage();
        } else {
            /** @var Mage_Tax_Helper_Data $helper */
            $helper = $this->helper('tax');

            $price = $this->_getShippingPrice($rate->getPrice(), $helper->displayShippingPriceIncludingTax());
            $incl = $this->_getShippingPrice($rate->getPrice(), true);
            if (($incl != $price) && $helper->displayShippingBothPrices()) {
                $renderedInclTax = sprintf($inclTaxFormat, Mage::helper('tax')->__('Incl. Tax'), $incl);
            }
        }

        return sprintf($format, $this->escapeHtml($rate->getMethodTitle()), $price, $renderedInclTax);
    }

    /**
     * Getter for current shipping rate
     *
     * @return Mage_Sales_Model_Quote_Address_Rate
     */
    public function getCurrentShippingRate()
    {
        return $this->_currentShippingRate;
    }

    /**
     * Set paypal actions prefix
     * @param string $prefix
     */
    public function setPaypalActionPrefix($prefix)
    {
        $this->_paypalActionPrefix = $prefix;
    }

    /**
     * Return formatted shipping price
     *
     * @param float $price
     * @param bool $isInclTax
     * @return float
     */
    protected function _getShippingPrice($price, $isInclTax)
    {
        /** @var Mage_Tax_Helper_Data $helper */
        $helper = $this->helper('tax');
        return $helper->getShippingPrice($price, $isInclTax, $this->_address);
    }

    /**
     * Format price base on store convert price method
     *
     * @param float $price
     * @return float
     */
    protected function _formatPrice($price)
    {
        return $this->_quote->getStore()->convertPrice($price, true);
    }

    /**
     * Retrieve payment method and assign additional template values
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $methodInstance = $this->_quote->getPayment()->getMethodInstance();
        $this->setPaymentMethodTitle($methodInstance->getTitle());

        $this->setShippingRateRequired(true);
        if ($this->_quote->getIsVirtual()) {
            $this->setShippingRateRequired(false);
        } else {
            // prepare shipping rates
            $this->_address = $this->_quote->getShippingAddress();
            $groups = $this->_address->getGroupedAllShippingRates();
            if ($groups && $this->_address) {
                $this->setShippingRateGroups($groups);
                // determine current selected code & name
                foreach ($groups as $rates) {
                    foreach ($rates as $rate) {
                        if ($this->_address->getShippingMethod() == $rate->getCode()) {
                            $this->_currentShippingRate = $rate;
                            break(2);
                        }
                    }
                }
            }

            $canEditShippingAddress = $this->_quote->getMayEditShippingAddress() && $this->_quote->getPayment()
                    ->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_BUTTON) == 1;
            // misc shipping parameters
            $this->setShippingMethodSubmitUrl($this->getUrl("{$this->_paypalActionPrefix}/express/saveShippingMethod"))
                ->setCanEditShippingAddress($canEditShippingAddress)
                ->setCanEditShippingMethod($this->_quote->getMayEditShippingMethod())
            ;
        }

        $this->setEditUrl($this->getUrl("{$this->_paypalActionPrefix}/express/edit"))
            ->setPlaceOrderUrl($this->getUrl("{$this->_paypalActionPrefix}/express/placeOrder"));

        return parent::_beforeToHtml();
    }
}
