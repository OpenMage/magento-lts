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
 * @package     Mage_Shipping
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


abstract class Mage_Shipping_Model_Carrier_Abstract extends Varien_Object
{
    protected $_code;
    protected $_rates = null;
    protected $_numBoxes = 1;

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @var bool
     */
    protected $_isFixed = false;

    const HANDLING_TYPE_PERCENT = 'P';
    const HANDLING_TYPE_FIXED = 'F';

    const HANDLING_ACTION_PERPACKAGE = 'P';
    const HANDLING_ACTION_PERORDER = 'O';

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array();

    public function __construct()
    {

    }

    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'carriers/'.$this->_code.'/'.$field;
        return Mage::getStoreConfig($path, $this->getStore());
    }

    public function getConfigFlag($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'carriers/'.$this->_code.'/'.$field;
        return Mage::getStoreConfigFlag($path, $this->getStore());
    }

    abstract public function collectRates(Mage_Shipping_Model_Rate_Request $request);

    public function checkAvailableShipCountries(Mage_Shipping_Model_Rate_Request $request)
    {
        $speCountriesAllow = $this->getConfigData('sallowspecific');
        /*
        * for specific countries, the flag will be 1
        */
        if($speCountriesAllow && $speCountriesAllow==1){
             $showMethod = $this->getConfigData('showmethod');
             $availableCountries = array();
             if( $this->getConfigData('specificcountry') ) {
                $availableCountries = explode(',',$this->getConfigData('specificcountry'));
             }
             if ($availableCountries && in_array($request->getDestCountryId(), $availableCountries)) {
                 return $this;
             } elseif ($showMethod && (!$availableCountries || ($availableCountries && !in_array($request->getDestCountryId(), $availableCountries)))){
                   $error = Mage::getModel('shipping/rate_result_error');
                   $error->setCarrier($this->_code);
                   $error->setCarrierTitle($this->getConfigData('title'));
                   $errorMsg = $this->getConfigData('specificerrmsg');
                   $error->setErrorMessage($errorMsg?$errorMsg:Mage::helper('shipping')->__('The shipping module is not available for selected delivery country.'));
                   return $error;
             } else {
                 /*
                * The admin set not to show the shipping module if the devliery country is not within specific countries
                */
                return false;
             }
        }
        return $this;
    }


    /**
     * Processing additional validation to check is carrier applicable.
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Carrier_Abstract|Mage_Shipping_Model_Rate_Result_Error|boolean
     */
    public function proccessAdditionalValidation(Mage_Shipping_Model_Rate_Request $request)
    {
        return $this;
    }

    public function isActive()
    {
        $active = $this->getConfigData('active');
        return $active==1 || $active=='true';
    }

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @return bool
     */
    public function isFixed()
    {
        return $this->_isFixed;
    }

    /**
     * Check if carrier has shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return false;
    }

    public function getSortOrder()
    {
        return $this->getConfigData('sort_order');
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return void
     */
    protected function _updateFreeMethodQuote($request)
    {
        if ($request->getFreeMethodWeight()==$request->getPackageWeight()
            || !$request->hasFreeMethodWeight()) {
            return;
        }

        if (!$freeMethod = $this->getConfigData('free_method')) {
            return;
        }
        $freeRateId = false;

        if (is_object($this->_result)) {
            foreach ($this->_result->getAllRates() as $i=>$item) {
                if ($item->getMethod()==$freeMethod) {
                    $freeRateId = $i;
                    break;
                }
            }
        }

        if ($freeRateId===false) {
            return;
        }
        $price = null;
        if ($request->getFreeMethodWeight()>0) {
            $this->_setFreeMethodRequest($freeMethod);

            $result = $this->_getQuotes();
            if ($result && ($rates = $result->getAllRates()) && count($rates)>0) {
                if ((count($rates) == 1) && ($rates[0] instanceof Mage_Shipping_Model_Rate_Result_Method)) {
                    $price = $rates[0]->getPrice();
                }
                if (count($rates) > 1) {
                    foreach ($rates as $rate) {
                        if ($rate instanceof Mage_Shipping_Model_Rate_Result_Method && $rate->getMethod() == $freeMethod) {
                            $price = $rate->getPrice();
                        }
                    }
                }
            }
        } else {
            /**
             * if we can apply free shipping for all order we should force price
             * to $0.00 for shipping with out sending second request to carrier
             */
            $price = 0;
        }

        /**
         * if we did not get our free shipping method in response we must use its old price
         */
        if (!is_null($price)) {
            $this->_result->getRateById($freeRateId)->setPrice($price);
        }
    }

    public function getMethodPrice($cost, $method='')
    {
        if ($method == $this->getConfigData('free_method') &&
            $this->getConfigData('free_shipping_enable') &&
            $this->getConfigData('free_shipping_subtotal') <= $this->_rawRequest->getValueWithDiscount())
        {
            $price = '0.00';
        } else {
            $price = $this->getFinalPriceWithHandlingFee($cost);
        }
        return $price;
    }

   /**
     * get the handling fee for the shipping + cost
     *
     * @return final price for shipping emthod
     */
    public function getFinalPriceWithHandlingFee($cost)
    {
        $handlingFee = $this->getConfigData('handling_fee');
        $finalMethodPrice = 0;
        $handlingType = $this->getConfigData('handling_type');
        if (!$handlingType) {
            $handlingType = self::HANDLING_TYPE_FIXED;
        }
        $handlingAction = $this->getConfigData('handling_action');
        if (!$handlingAction) {
            $handlingAction = self::HANDLING_ACTION_PERORDER;
        }

        if($handlingAction == self::HANDLING_ACTION_PERPACKAGE)
        {
            if ($handlingType == self::HANDLING_TYPE_PERCENT) {
                $finalMethodPrice = ($cost + ($cost * $handlingFee/100)) * $this->_numBoxes;
            } else {
                $finalMethodPrice = ($cost + $handlingFee) * $this->_numBoxes;
            }
        } else {
            if ($handlingType == self::HANDLING_TYPE_PERCENT) {
                $finalMethodPrice = ($cost * $this->_numBoxes) + ($cost * $this->_numBoxes * $handlingFee/100);
            } else {
                $finalMethodPrice = ($cost * $this->_numBoxes) + $handlingFee;
            }

        }
        return $finalMethodPrice;
    }

    /**
     *  Return weight in pounds
     *
     *  @param    integer Weight in someone measure
     *  @return	  float Weight in pounds
     */
    public function convertWeightToLbs($weight)
    {
        return $weight;
    }

    /**
     * set the number of boxes for shipping
     *
     * @return weight
     */
    public function getTotalNumOfBoxes($weight)
    {
        /*
        reset num box first before retrieve again
        */
        $this->_numBoxes = 1;
        $weight = $this->convertWeightToLbs($weight);
        $maxPackageWeight = $this->getConfigData('max_package_weight');
        if($weight > $maxPackageWeight && $maxPackageWeight != 0) {
            $this->_numBoxes = ceil($weight/$maxPackageWeight);
            $weight = $weight/$this->_numBoxes;
        }
        return $weight;
    }

    public function isStateProvinceRequired()
    {
        return false;
    }

    public function isCityRequired()
    {
        return false;
    }

    public function isZipCodeRequired()
    {
        return false;
    }

    /**
     * Log debug data to file
     *
     * @param mixed $debugData
     */
    protected function _debug($debugData)
    {
        if ($this->getDebugFlag()) {
            Mage::getModel('core/log_adapter', 'shipping_' . $this->getCarrierCode() . '.log')
               ->setFilterDataKeys($this->_debugReplacePrivateDataKeys)
               ->log($debugData);
        }
    }

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag()
    {
        return $this->getConfigData('debug');
    }

    /**
     * Used to call debug method from not Paymant Method context
     *
     * @param mixed $debugData
     */
    public function debugData($debugData)
    {
        $this->_debug($debugData);
    }

    /**
     * Getter for carrier code
     *
     * @return string
     */
    public function getCarrierCode()
    {
        return $this->_code;
    }
}
