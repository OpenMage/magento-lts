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
 * @package     Mage_Strikeiron
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sitemap model
 *
 * @category   Mage
 * @package    Mage_Strikeiron
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Strikeiron_Model_Strikeiron extends Mage_Core_Model_Abstract
{
    public function getApi($service, $options = array())
    {
        return Mage::getSingleton('strikeiron/service_'.$service, array_merge($this->getConfiguration(),$options));
    }

    protected function getConfiguration()
    {
        return array('username'=> $this->getConfigData('config', 'user') , 'password'=> $this->getConfigData('config', 'password'));
    }

    public function getConfigData($code, $field)
    {
        $path = 'strikeiron/'.$code.'/'.$field;
        return Mage::getStoreConfig($path);
    }

/*********************** EMAIL VERIFICATION***************************/
    /*
    verify email address is valid or not
    wsdl = http://ws.strikeiron.com/varien.StrikeIron/emailverify_3_0?WSDL
    */
    public function emailVerify($email)
    {
        if ($email && $this->getConfigData('email_verification', 'active')) {
            $_session = Mage::getSingleton('strikeiron/session');
            /*
            * following flag will set if the email is undetermined for the first time
            * for second time, we just need to return true
            */
            if ($_session->getStrikeironUndertermined()==$email) {
               $_session->unsStrikeironUndertermined();
               return true;
            }

            $emailApi = $this->getApi('emailVerification');

            $checkAllServer = $this->getConfigData('email_verification', 'check_allservers');
            $emailArr = array(
                'email' => $email,
                'checkAllServers' => ($checkAllServer ? 'True' : 'False')
            );
            $result = '';

            try {
                $subscriptionInfo = $emailApi->getSubscriptionInfo();
                if ($subscriptionInfo && $subscriptionInfo->remainingHits>0) {
                    $result = $emailApi->validateEmail($emailArr);
                    if ($result) {
                        switch ($result->IsValid) {
                           case 'INVALID':
                               Mage::throwException(Mage::helper('strikeiron')->__('Invalid email address'));
                           break;
                           case 'UNDETERMINED':
                               switch($this->getConfigData('email_verification', 'undetermined_action')) {
                                   case Mage_Strikeiron_Model_Service_EmailVerification::EMAIL_UNDETERMINED_REJECT:
                                       Mage::throwException(Mage::helper('strikeiron')->__('Invalid email address'));
                                   break;
                                   case  Mage_Strikeiron_Model_Service_EmailVerification::EMAIL_UNDETERMINED_CONFIRM:
                                          $_session->setStrikeironUndertermined($email);
                                          Mage::throwException(Mage::helper('strikeiron')->__('Email address cannot be verified. Please check again and make sure your email address entered correctly.'));
                                   break;
                               }
                           break;
                       }
                    } else {
                       Mage::throwException(Mage::helper('strikeiron')->__('There is an error in verifying an email. Please contact us.'));
                    }

                } else {
                   /*
                    * when there is no more remaining hits for service
                    * we will send email to email recipient for exception
                    */
                    /* @var $mailTemplate Mage_Core_Model_Email_Template */
                    $receipient = $this->getConfigData('email_verification', 'error_email');
                    if ($receipient) {
                        $translate = Mage::getSingleton('core/translate');
                        /* @var $translate Mage_Core_Model_Translate */
                        $translate->setTranslateInline(false);

                        $mailTemplate = Mage::getModel('core/email_template');
                        $mailTemplate->setDesignConfig(
                                array(
                                    'area'  => 'frontend',
                                )
                            )
                            ->sendTransactional(
                                $this->getConfigData('email_verification', 'error_email_template'),
                                $this->getConfigData('email_verification', 'error_email_identity'),
                                $receipient,
                                null,
                                array(
                                  'email'       => $email,
                                  'warnings'    => $e->getMessage(),
                                )
                            );

                        $translate->setTranslateInline(true);
                    }

                }
            } catch (Zend_Service_StrikeIron_Exception $e) {
               Mage::throwException(Mage::helper('strikeiron')->__('There is an error in verifying an email. Please contact us.'));
            }
        }
        return true;
    }

/*********************** FOREIGN CURRENCY EXCHANGE***************************/

    public function _getAllSupportedCurrencies($exchangeApi)
    {
        $result = $exchangeApi->GetSupportedCurrencies();
        $data = array();
        if ($result && $result->ServiceStatus && $result->ServiceStatus->StatusNbr == 210) {
            $listings = $result->ServiceResult->Listings;
            if ($listings && $listings->CurrencyListing) {
                foreach($listings->CurrencyListing as $listing){
                    $data[] = $listing->Symbol;
                }
            }
        }
        return $data;
    }

    /*
    retrieving foreign exchange rate for the currency
    wsdl = http://ws.strikeiron.com/varien.StrikeIron/ForeignExchangeRate?WSDL
    */
    public function fetchExchangeRate ($defaultCurrency, $currencies=array())
    {
        if(!$this->getConfigData('currency', 'foreigh_xrate')){
            Mage::throwException(Mage::helper('strikeiron')->__('Strikeiron foreign exchange rate is disabled'));
        }

        $data = array();
        $exchangeApi = $this->getApi('foreignExchangeRates');
        $result = '';
        try {
            $subscriptionInfo = $exchangeApi->getSubscriptionInfo();
            if ($subscriptionInfo && $subscriptionInfo->remainingHits>0) {
                $supportedCurrencies = $this->_getAllSupportedCurrencies($exchangeApi);
                if($supportedCurrencies) {
                    $availableCurrencies = array_intersect($currencies, $supportedCurrencies);
                    if($availableCurrencies && in_array($defaultCurrency,$supportedCurrencies)){
                        $currenciesStr = implode(', ' , $availableCurrencies);
                        $reqArr = array(
                            'CommaSeparatedListOfCurrenciesFrom' => $currenciesStr,
                            'SingleCurrencyTo' => $defaultCurrency
                        );
                        $result = $exchangeApi->GetLatestRates($reqArr);
                        if ($result) {
                            /*
                            212 = Currency rate data Found
                            */
                            if ($result->ServiceStatus && $result->ServiceStatus->StatusNbr == 212) {
                              $listings = $result->ServiceResult->Listings;
                              if($listings && $listings->ExchangeRateListing) {
                                  foreach ($listings->ExchangeRateListing as $listing) {
                                      $data[$listing->PerCurrency][$listing->Currency] = $listing->Value;
                                  }
                              }
                            } else {
                              Mage::throwException($result->ServiceStatus->StatusDescription);
                            }
                        } else {
                           Mage::throwException(Mage::helper('strikeiron')->__('There is no response back from Strikeiron server'));
                        }
                    }
                }
            } else {
                Mage::throwException(Mage::helper('strikeiron')->__('There is no more hits remaining for the foreign Exchange Rate Service.'));
            }
        } catch (Zend_Service_StrikeIron_Exception $e) {
               Mage::throwException(Mage::helper('strikeiron')->__('There is no response back from Strikeiron server'));
        }
        return $data;
    }

    public function customerSaveBeforeObserver($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $isAdmin = Mage::getDesign()->getArea()==='adminhtml';
        $email = $customer->getEmail();
        $host =  Mage::app()->getStore()->getConfig(Mage_Customer_Model_Customer::XML_PATH_DEFAULT_EMAIL_DOMAIN);
        $fakeEmail = $customer->getIncrementId().'@'. $host;
        if ($email && $email != $fakeEmail && $customer->dataHasChangedFor('email') &&
            (!$isAdmin || ($isAdmin && $this->getConfigData('email_verification', 'check_admin')))
        ) {
            $this->emailVerify($email);
        }
    }

/*********************** ADDRESS VERIFICATION***************************/

    public function addressSaveBeforeObserver($observer)
    {
        $address = $observer->getEvent()->getCustomerAddress();
        $us = $address->getCountryId() == 'US';
        $addressDataChange = sizeof($address) == 1 && ( $address->dataHasChangedFor('street') || $address->dataHasChangedFor('city') ||
            $address->dataHasChangedFor('postcode') || $address->dataHasChangedFor('country_id') || $address->dataHasChangedFor('region_id') ||
            $address->dataHasChangedFor('region'))
        ;
        if ($addressDataChange) {
            if ($us) {
                $this->UsAddressVerify($address);
            }
        }

    }

    /*
        verify US address is valid or not
        wsdl = http://ws.strikeiron.com/varien.StrikeIron/USAddressVerification4_0?WSDL
        $subscription = $taxBasic->getSubscriptionInfo();
        echo $subscription->remainingHits;

    */
    public function UsAddressVerify($address)
    {
//echo "<pre>";
//print_r($address);
return;
$_session = Mage::getSingleton('strikeiron/session');
        $usAddressApi = $this->getApi('usAddressVerification');
        $cityStateZip = $address->getCity()." ".$address->getRegionCode()." ".$address->getPostcode();
        $reqArr = array(
                    'firm' => $address->getCompany(),
                    'addressLine1' => $address->getStreet(1),
                    'addressLine2' => $address->getStreet(2),
                    'city_state_zip' => $cityStateZip )
        ;
        $result = '';
        try {
            $subscriptionInfo = $usAddressApi->getSubscriptionInfo();
            if ($subscriptionInfo && $subscriptionInfo->remainingHits>0) {
                $result = $usAddressApi->verifyAddressUSA($reqArr);
//$result = $_session->getUsAddressVerify();
//$_session->setUsAddressVerify($result);
//print_r($reqArr);
//print_r($result);
            } else {

            }

        } catch (Zend_Service_StrikeIron_Exception $e) {
               Mage::throwException(Mage::helper('strikeiron')->__('There is no response back from Strikeiron server'));
        }
        return true;
    }

/*********************** SALES AND TAX RATE***************************/
    /*
    retrieveing the sale tax rate by zip code for US and by province by canada
    wsdl = http://ws.strikeiron.com/varien.StrikeIron/TaxDataBasic4?WSDL
    wsdl = http://ws.strikeiron.com/varien.StrikeIron/TaxDataComplete4?WSDL
    this method is called by event handler
    event is added in Mage_Tax_Model_Rate_Data
    */
    public function getTaxRate($observer)
    {
        $data = $observer->getEvent()->getRequest();
//        $data = new Varien_Object();
//        $data->setProductClassId(2)
//             ->setCustomerClassId(3)
//             ->setCountryId('CN')
//             ->setRegionId('74')
//             ->setPostcode('95618');

        $tax_rate = 0;
        $customerTaxClass = array();
        $customerTaxClass = explode(',' ,$this->getConfigData('sales_tax', 'customer_tax_class'));
        $productTaxClass = array();
        $productTaxClass = explode(',' , $this->getConfigData('sales_tax', 'product_tax_class'));
        if ($this->getConfigData('sales_tax', 'active')
            && in_array($data->getCustomerClassId(), $customerTaxClass)
            && in_array($data->getProductClassId(), $productTaxClass)
            && ($data->getCountryId()=='US' || $data->getCountryId()=='CN')
            ) {
            $type = $this->getConfigData('sales_tax', 'type');
            $isBasic = false;
            if($type == 'B') {
                $isBasic = true;
            }

            $saleTaxApi = $this->getApi('salesUseTax'.($isBasic ? 'Basic' : 'Complete'));
            try {
                $subscriptionInfo = $saleTaxApi->getSubscriptionInfo();
                if ($subscriptionInfo && $subscriptionInfo->remainingHits>0) {
                    if ($data->getCountryId()=='US') {
                        if ($isBasic) {
                            $requestArr = array('zip_code' => $data->getPostcode());
                            $result = $saleTaxApi->GetTaxRateUS($requestArr);
                            if ($result) {
                                $tax_rate = $result->total_sales_tax;
                            }
                        } else {
                            $requestArr = array('zipCode' => $data->getPostcode());

                            $result = $saleTaxApi->GetUSATaxRatesByZipCode($requestArr);
                            if ($result && $result->USATaxRate) {
                                $tax_rate = $this->parseTaxRateComplete($result);
                            }
                        }
                    } else {
                        $region_code = Mage::getSingleton('directory/region')->load($data->getRegionId())->getCode();
                        $requestArr = array('province' => $region_code);
                        if ($isBasic) {
                            $result = $saleTaxApi->GetTaxRateCanada($requestArr);
                            if ($result) {
                                $tax_rate = $result->total;
                            }
                        } else {
                            $result = $saleTaxApi->GetCanadaTaxRatesByProvince($requestArr);
                            if ($result && $result->CanadaTaxRate) {
                                $tax_rate = $result->CanadaTaxRate->Total;
                            }
                        }
                    }
                }
            } catch (Zend_Service_StrikeIron_Exception $e) {
                //we won't throw exception
                //since the method is calling via event handler
               //Mage::throwException(Mage::helper('strikeiron')->__('There is an error in retrieving tax rate. Please contact us'));
            }
        }

        if ($tax_rate>0) {
            $tax_rate = $tax_rate * 100;
            $dbObj = Mage::getSingleton('strikeiron/taxrate')
                ->setTaxCountryId($data->getCountryId())
                ->setTaxRegionId($data->getRegionId())
                ->setTaxPostcode($data->getPostcode())
                ->setRateValue($tax_rate)
                ->save();
            $data->setRateValue($tax_rate);
            $data->setRateTitle(Mage::helper('strikeiron')->__('Tax'));
            $data->setRateId('strikeiron_tax');
        }
        return $this;

    }

    protected function parseTaxRateComplete($result)
    {
        $tax_array = array();
        foreach ($result->USATaxRate as $rate) {
            if($rate->TotalSalesTax>0) {
                $tax_array[] = $rate->TotalSalesTax;
            }
        }
        $minMax = strtolower($this->getConfigData('sales_tax', 'min_max'));
        return $minMax($tax_array);
    }
}
