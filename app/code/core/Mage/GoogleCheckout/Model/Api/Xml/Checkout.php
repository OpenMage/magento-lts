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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_GoogleCheckout_Model_Api_Xml_Checkout extends Mage_GoogleCheckout_Model_Api_Xml_Abstract
{
    protected $_currency;
    protected $_shippingCalculated = false;

    protected function _getApiUrl()
    {
        $url = $this->_getBaseApiUrl();
        $url .= 'merchantCheckout/Merchant/'.$this->getMerchantId();
        return $url;
    }

    public function checkout()
    {
        $quote = $this->getQuote();

        if (!($quote instanceof Mage_Sales_Model_Quote)) {
            Mage::throwException('Invalid quote');
        }

        $xml = <<<EOT
<checkout-shopping-cart xmlns="http://checkout.google.com/schema/2">
    <shopping-cart>
{$this->_getItemsXml()}
{$this->_getMerchantPrivateDataXml()}
{$this->_getCartExpirationXml()}
    </shopping-cart>
    <checkout-flow-support>
{$this->_getMerchantCheckoutFlowSupportXml()}
    </checkout-flow-support>
    <order-processing-support>
{$this->_getRequestInitialAuthDetailsXml()}
    </order-processing-support>
</checkout-shopping-cart>
EOT;
#echo "<xmp>".$xml."</xmp>";
        $result = $this->_call($xml);

        $this->setRedirectUrl($result->{'redirect-url'});

        return $this;
    }

    protected function _getItemsXml()
    {
        $xml = <<<EOT
        <items>

EOT;
        $weightUnit = 'LB';
        foreach ($this->getQuote()->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $taxClass = ($item->getTaxClassId() == 0 ? 'none' : $item->getTaxClassId());
            $weight = (float) $item->getWeight();

            $xml .= <<<EOT
            <item>
                <merchant-item-id><![CDATA[{$item->getSku()}]]></merchant-item-id>
                <item-name><![CDATA[{$item->getName()}]]></item-name>
                <item-description><![CDATA[{$item->getDescription()}]]></item-description>
                <unit-price currency="{$this->getCurrency()}">{$item->getBaseCalculationPrice()}</unit-price>
                <quantity>{$item->getQty()}</quantity>
                <item-weight unit="{$weightUnit}" value="{$weight}" />
                <tax-table-selector>{$taxClass}</tax-table-selector>
                {$this->_getDigitalContentXml($item->getIsVirtual())}
                {$this->_getMerchantPrivateItemDataXml($item)}
            </item>

EOT;
        }

        $shippingDiscount = (float)$this->getQuote()->getShippingAddress()->getBaseDiscountAmount();
        $billingDiscount = (float)$this->getQuote()->getBillingAddress()->getBaseDiscountAmount();
        if ($discount = $billingDiscount + $shippingDiscount) {
            $xml .= <<<EOT
            <item>
                <merchant-item-id>_INTERNAL_DISCOUNT_</merchant-item-id>
                <item-name>{$this->__('Cart Discount')}</item-name>
                <item-description>{$this->__('Virtual item to reflect discount total')}</item-description>
                <unit-price currency="{$this->getCurrency()}">{$discount}</unit-price>
                <quantity>1</quantity>
                <item-weight unit="{$weightUnit}" value="0.00" />
                <tax-table-selector>none</tax-table-selector>
                {$this->_getDigitalContentXml($this->getQuote()->isVirtual())}
            </item>

EOT;
        }
        $xml .= <<<EOT
        </items>
EOT;
        return $xml;
    }

    protected function _getDigitalContentXml($isVirtual)
    {
        if (!$isVirtual) {
            return '';
        }

        $active = Mage::getStoreConfigFlag('google/checkout_shipping_virtual/active', $this->getQuote()->getStoreId());
        if (!$active) {
            return '';
        }

        $schedule = Mage::getStoreConfig('google/checkout_shipping_virtual/schedule', $this->getQuote()->getStoreId());
        $method = Mage::getStoreConfig('google/checkout_shipping_virtual/method', $this->getQuote()->getStoreId());

        $xml = "<display-disposition>{$schedule}</display-disposition>";

        if ($method == 'email') {
            $xml .= "<email-delivery>true</email-delivery>";
        } elseif ($method == 'key_url') {
        } elseif ($method == 'description_based') {
        }

        $xml = "<digital-content>{$xml}</digital-content>";

        return $xml;
    }

    protected function _getMerchantPrivateItemDataXml($item)
    {
        $xml = "<merchant-private-item-data><quote-item-id>{$item->getId()}</quote-item-id></merchant-private-item-data>";
        return $xml;
    }
    protected function _getMerchantPrivateDataXml()
    {
        $xml = <<<EOT
            <merchant-private-data>
                <quote-id><![CDATA[{$this->getQuote()->getId()}]]></quote-id>
                <store-id><![CDATA[{$this->getQuote()->getStoreId()}]]></store-id>
            </merchant-private-data>
EOT;
        return $xml;
    }

    protected function _getCartExpirationXml()
    {
        $xml = <<<EOT
EOT;
        return $xml;
    }

    protected function _getMerchantCheckoutFlowSupportXml()
    {
        $xml = <<<EOT
        <merchant-checkout-flow-support>
            <edit-cart-url><![CDATA[{$this->_getEditCartUrl()}]]></edit-cart-url>
            <continue-shopping-url><![CDATA[{$this->_getContinueShoppingUrl()}]]></continue-shopping-url>
            {$this->_getRequestBuyerPhoneNumberXml()}
            {$this->_getMerchantCalculationsXml()}
            {$this->_getShippingMethodsXml()}
            {$this->_getAllTaxTablesXml()}
            {$this->_getParameterizedUrlsXml()}
            {$this->_getPlatformIdXml()}
            {$this->_getAnalyticsDataXml()}
        </merchant-checkout-flow-support>
EOT;
        return $xml;
    }

    protected function _getRequestBuyerPhoneNumberXml()
    {
        $requestPhone = Mage::getStoreConfig('google/checkout/request_phone', $this->getQuote()->getStoreId()) ? 'true' : 'false';
        $xml = <<<EOT
            <request-buyer-phone-number>{$requestPhone}</request-buyer-phone-number>
EOT;
        return $xml;
    }

    protected function _getMerchantCalculationsXml()
    {
        $xml = <<<EOT
            <merchant-calculations>
                <merchant-calculations-url><![CDATA[{$this->_getCalculationsUrl()}]]></merchant-calculations-url>
            </merchant-calculations>
EOT;
        return $xml;
    }

    protected function _getVirtualOrderShippingXml()
    {
        $title = Mage::helper('googlecheckout')->__('Free Shipping');

        $xml = <<<EOT
            <shipping-methods>
                <flat-rate-shipping name="{$title}">
                    <shipping-restrictions><allowed-areas><world-area /></allowed-areas></shipping-restrictions>
                    <price currency="{$this->getCurrency()}">0</price>
                </flat-rate-shipping>
            </shipping-methods>
EOT;
        return $xml;
    }

    protected function _getShippingMethodsXml()
    {
        if ($this->_isOrderVirtual()) {
            return $this->_getVirtualOrderShippingXml();
        }

        $xml = <<<EOT
            <shipping-methods>
                {$this->_getCarrierCalculatedShippingXml()}
                {$this->_getFlatRateShippingXml()}
                {$this->_getMerchantCalculatedShippingXml()}
                {$this->_getPickupXml()}
            </shipping-methods>
EOT;
        return $xml;
    }

    protected function _getCarrierCalculatedShippingXml()
    {
        /*
        we want to send ONLY ONE shipping option to google
        */
        if ($this->_shippingCalculated) {
            return '';
        }

        $active = Mage::getStoreConfigFlag('google/checkout_shipping_carrier/active', $this->getQuote()->getStoreId());
        $methods = Mage::getStoreConfig('google/checkout_shipping_carrier/methods', $this->getQuote()->getStoreId());
        if (!$active || !$methods) {
            return '';
        }

        $country = Mage::getStoreConfig('shipping/origin/country_id', $this->getQuote()->getStoreId());
        $region = Mage::getStoreConfig('shipping/origin/region_id', $this->getQuote()->getStoreId());
        $postcode = Mage::getStoreConfig('shipping/origin/postcode', $this->getQuote()->getStoreId());
        $city = Mage::getStoreConfig('shipping/origin/city', $this->getQuote()->getStoreId());

        $sizeUnit = 'IN';#Mage::getStoreConfig('google/checkout_shipping_carrier/default_unit');
        $defPrice = (float)Mage::getStoreConfig('google/checkout_shipping_carrier/default_price', $this->getQuote()->getStoreId());
        $width = Mage::getStoreConfig('google/checkout_shipping_carrier/default_width', $this->getQuote()->getStoreId());
        $height = Mage::getStoreConfig('google/checkout_shipping_carrier/default_height', $this->getQuote()->getStoreId());
        $length = Mage::getStoreConfig('google/checkout_shipping_carrier/default_length', $this->getQuote()->getStoreId());

        $addressCategory = Mage::getStoreConfig('google/checkout_shipping_carrier/address_category', $this->getQuote()->getStoreId());

        $defPrice = (float) Mage::helper('tax')->getShippingPrice($defPrice, false, false);

//      $taxRate = $this->_getShippingTaxRate();
//      <additional-variable-charge-percent>{$taxRate}</additional-variable-charge-percent>

        $xml = <<<EOT
                <carrier-calculated-shipping>
                    <shipping-packages>
                        <shipping-package>
                            <ship-from id="Origin">
                                <city>{$city}</city>
                                <region>{$region}</region>
                                <postal-code>{$postcode}</postal-code>
                                <country-code>{$country}</country-code>
                            </ship-from>
                            <width unit="{$sizeUnit}" value="{$width}"/>
                            <height unit="{$sizeUnit}" value="{$height}"/>
                            <length unit="{$sizeUnit}" value="{$length}"/>
                            <delivery-address-category>{$addressCategory}</delivery-address-category>
                        </shipping-package>
                    </shipping-packages>
                    <carrier-calculated-shipping-options>
EOT;

        foreach (explode(',', $methods) as $method) {
            list($company, $type) = explode('/', $method);
            $xml .= <<<EOT
                        <carrier-calculated-shipping-option>
                            <shipping-company>{$company}</shipping-company>
                            <shipping-type>{$type}</shipping-type>
                            <price currency="{$this->getCurrency()}">{$defPrice}</price>
                        </carrier-calculated-shipping-option>
EOT;
        }

        $xml .= <<<EOT
                    </carrier-calculated-shipping-options>
                </carrier-calculated-shipping>
EOT;
        $this->_shippingCalculated = true;
        return $xml;
    }

    protected function _getFlatRateShippingXml()
    {
        /*
        we want to send ONLY ONE shipping option to google
        */
        if ($this->_shippingCalculated) {
            return '';
        }

        if (!Mage::getStoreConfigFlag('google/checkout_shipping_flatrate/active', $this->getQuote()->getStoreId())) {
            return '';
        }

        for ($xml='', $i=1; $i<=3; $i++) {
            $allowSpecific = Mage::getStoreConfigFlag('google/checkout_shipping_flatrate/sallowspecific_'.$i, $this->getQuote()->getStoreId());
            $specificCountries = Mage::getStoreConfig('google/checkout_shipping_flatrate/specificcountry_'.$i, $this->getQuote()->getStoreId());
            $allowedAreasXml = $this->_getAllowedCountries($allowSpecific, $specificCountries);

            $title = Mage::getStoreConfig('google/checkout_shipping_flatrate/title_'.$i, $this->getQuote()->getStoreId());
            $price = Mage::getStoreConfig('google/checkout_shipping_flatrate/price_'.$i, $this->getQuote()->getStoreId());
            $price = number_format($price, 2, '.','');
            $price = (float) Mage::helper('tax')->getShippingPrice($price, false, false);

            if (empty($title) || $price <= 0) {
                continue;
            }

            $xml .= <<<EOT
                <flat-rate-shipping name="{$title}">
                    <shipping-restrictions>
                        <allowed-areas>
                        {$allowedAreasXml}
                        </allowed-areas>
                    </shipping-restrictions>
                    <price currency="{$this->getCurrency()}">{$price}</price>
                </flat-rate-shipping>
EOT;
        }
        $this->_shippingCalculated = true;
        return $xml;
    }

    protected function _getAllowedCountries($allowSpecific, $specific)
    {
        $xml = '';
        if ($allowSpecific == 1) {
            if($specific) {
                foreach (explode(',', $specific) as $country) {
                    $xml .= "<postal-area><country-code>{$country}</country-code></postal-area>\n";
                }
            }
        }

        if ($xml) {
            return $xml;
        }

        return '<world-area />';
    }

    protected function _getMerchantCalculatedShippingXml()
    {
        /*
        we want to send ONLY ONE shipping option to google
        */
        if ($this->_shippingCalculated) {
            return '';
        }

        $active = Mage::getStoreConfigFlag('google/checkout_shipping_merchant/active', $this->getQuote()->getStoreId());
        $methods = Mage::getStoreConfig('google/checkout_shipping_merchant/allowed_methods', $this->getQuote()->getStoreId());

        if (!$active || !$methods) {
            return '';
        }

        $methods = unserialize($methods);

        $xml = '';
        foreach ($methods['method'] as $i=>$method) {
            if (!$i || !$method) {
                continue;
            }
            list($carrierCode, $methodCode) = explode('/', $method);
            if ($carrierCode) {
                $carrier = Mage::getModel('shipping/shipping')->getCarrierByCode($carrierCode);
                if ($carrier) {
                    $allowedMethods = $carrier->getAllowedMethods();

                    if (isset($allowedMethods[$methodCode])) {
                        $method = Mage::getStoreConfig('carriers/'.$carrierCode.'/title', $this->getQuote()->getStoreId());
                        $method .= ' - '.$allowedMethods[$methodCode];
                    }

                    $defaultPrice = (float) $methods['price'][$i];
                    $defaultPrice = Mage::helper('tax')->getShippingPrice($defaultPrice, false, false);

                    $allowedAreasXml = $this->_getAllowedCountries($carrier->getConfigData('sallowspecific'), $carrier->getConfigData('specificcountry'));

                    $xml .= <<<EOT
                        <merchant-calculated-shipping name="{$method}">
                            <address-filters>
                                <allowed-areas>
                                    {$allowedAreasXml}
                                </allowed-areas>
                            </address-filters>
                            <price currency="{$this->getCurrency()}">{$defaultPrice}</price>
                        </merchant-calculated-shipping>
EOT;
                }
            }
        }
        $this->_shippingCalculated = true;
        return $xml;
    }

    protected function _getPickupXml()
    {
        if (!Mage::getStoreConfig('google/checkout_shipping_pickup/active', $this->getQuote()->getStoreId())) {
            return '';
        }

        $title = Mage::getStoreConfig('google/checkout_shipping_pickup/title', $this->getQuote()->getStoreId());
        $price = Mage::getStoreConfig('google/checkout_shipping_pickup/price', $this->getQuote()->getStoreId());
        $price = (float) Mage::helper('tax')->getShippingPrice($price, false, false);

        $xml = <<<EOT
                <pickup name="{$title}">
                    <price currency="{$this->getCurrency()}">{$price}</price>
                </pickup>
EOT;
        return $xml;
    }

    protected function _getTaxTableXml($rules, $type)
    {
        $xml = '';
        if (is_array($rules)) {
            foreach ($rules as $group=>$taxRates) {
                if ($type != 'default') {
                    $nameAttribute = "name=\"{$group}\"";
                    $standaloneAttribute = "standalone=\"true\"";
                    $rulesTag = "{$type}-tax-rules";
                    $shippingTaxed = false;
                } else {
                    $nameAttribute = '';
                    $standaloneAttribute = '';
                    $rulesTag = "tax-rules";
                    $shippingTaxed = true;
                }


                $xml .= <<<EOT
                        <{$type}-tax-table {$nameAttribute} {$standaloneAttribute}>
                            <{$rulesTag}>
EOT;
                if (is_array($taxRates)) {
                    foreach ($taxRates as $rate) {
                        $xml .= <<<EOT
                                    <{$type}-tax-rule>
                                        <tax-area>

EOT;
                        if ($rate['country']==='US') {
                            if (!empty($rate['postcode']) && $rate['postcode']!=='*') {
                                $xml .= <<<EOT
                                            <us-zip-area>
                                                <zip-pattern>{$rate['postcode']}</zip-pattern>
                                            </us-zip-area>

EOT;
                            } elseif (!empty($rate['state'])) {
                                $xml .= <<<EOT
                                            <us-state-area>
                                                <state>{$rate['state']}</state>
                                            </us-state-area>

EOT;
                            } else {
                                $xml .= <<<EOT
                                            <us-zip-area>
                                                <zip-pattern>*</zip-pattern>
                                            </us-zip-area>

EOT;
                            }
                        } else {
                            if (!empty($rate['postcode'])) {
                                $xml .= <<<EOT
                                            <postal-area>
                                                <country-code>{$rate['country']}</country-code>
EOT;
                                if (!empty($rate['postcode']) && $rate['postcode']!=='*') {
                                    $xml .= <<<EOT
                                                <postal-code-pattern>{$rate['postcode']}</postal-code-pattern>

EOT;
                                }
                                $xml .= <<<EOT
                                            </postal-area>

EOT;
                            }
                        }
                        $xml .= <<<EOT
                                        </tax-area>
                                        <rate>{$rate['value']}</rate>
EOT;
                        if ($shippingTaxed) {
                            $xml .= '<shipping-taxed>true</shipping-taxed>';
                        }
                        $xml .= "</{$type}-tax-rule>";
                    }

                } else {
                    $taxRate = $taxRates/100;
                    $xml .= <<<EOT
                                <{$type}-tax-rule>
                                    <tax-area>
                                        <world-area/>
                                    </tax-area>
                                    <rate>{$taxRate}</rate>
EOT;
                        if ($shippingTaxed) {
                            $xml .= '<shipping-taxed>true</shipping-taxed>';
                        }
                    $xml .= "</{$type}-tax-rule>";
                }

                $xml .= <<<EOT
                            </$rulesTag>
                        </{$type}-tax-table>
EOT;
            }
        } else {
            if (is_numeric($rules)) {
                $taxRate = $rules/100;

                $xml .= <<<EOT
                        <{$type}-tax-table>
                            <tax-rules>
                                <{$type}-tax-rule>
                                    <tax-area>
                                        <world-area/>
                                    </tax-area>
                                    <rate>{$taxRate}</rate>
                                    <shipping-taxed>true</shipping-taxed>
                                </{$type}-tax-rule>
                            </tax-rules>
                        </{$type}-tax-table>
EOT;
            }
        }

        return $xml;
    }

    protected function _getAllTaxTablesXml()
    {
        if (Mage::getStoreConfigFlag('google/checkout/disable_default_tax_tables', $this->getQuote()->getStoreId())) {
            return '<tax-tables merchant-calculated="true" />';
        }


        $xml = <<<EOT
            <tax-tables merchant-calculated="true">
                {$this->_getTaxTableXml($this->_getShippingTaxRules(), 'default')}

                <!-- default-tax-table>
                    <tax-rules>
                        <default-tax-rule>
                        </default-tax-rule>
                    </tax-rules>
                </default-tax-table -->

                <alternate-tax-tables>
                    <alternate-tax-table name="none" standalone="true">
                        <alternate-tax-rules>
                            <alternate-tax-rule>
                                <tax-area>
                                    <world-area/>
                                </tax-area>
                                <rate>0</rate>
                            </alternate-tax-rule>
                        </alternate-tax-rules>
                    </alternate-tax-table>
                    {$this->_getTaxTableXml($this->_getTaxRules(), 'alternate')}
                </alternate-tax-tables>
            </tax-tables>
EOT;
        return $xml;
    }

    protected function _getCustomerTaxClass()
    {
        $customerGroup = $this->getQuote()->getCustomerGroupId();
        if (!$customerGroup) {
            $customerGroup = Mage::getStoreConfig('customer/create_account/default_group', $this->getQuote()->getStoreId());
        }
        return Mage::getModel('customer/group')->load($customerGroup)->getTaxClassId();
    }

    protected function _getShippingTaxRules()
    {
        $customerTaxClass = $this->_getCustomerTaxClass();
        if ($shippingTaxClass = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $this->getQuote()->getStoreId())) {
            if (Mage::helper('tax')->getTaxBasedOn() == 'origin') {
                $request = Mage::getSingleton('tax/calculation')->getRateRequest();
                $request
                    ->setCustomerClassId($customerTaxClass)
                    ->setProductClassId($shippingTaxClass);

                return Mage::getSingleton('tax/calculation')->getRate($request);
            }
            $customerRules = Mage::getSingleton('tax/calculation')->getRatesByCustomerAndProductTaxClasses($customerTaxClass, $shippingTaxClass);
            $rules = array();
            foreach ($customerRules as $rule) {
                $rules[$rule['product_class']][] = $rule;
            }
            return $rules;
        } else {
            return array();
        }
    }

    protected function _getTaxRules()
    {
        $customerTaxClass = $this->_getCustomerTaxClass();
        if (Mage::helper('tax')->getTaxBasedOn() == 'origin') {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest();
            return Mage::getSingleton('tax/calculation')->getRatesForAllProductTaxClasses($request->setCustomerClassId($customerTaxClass));
        } else {
            $customerRules = Mage::getSingleton('tax/calculation')->getRatesByCustomerTaxClass($customerTaxClass);
            $rules = array();
            foreach ($customerRules as $rule) {
                $rules[$rule['product_class']][] = $rule;
            }
            return $rules;
        }
    }

    protected function _getRequestInitialAuthDetailsXml()
    {
        $xml = <<<EOT
        <request-initial-auth-details>true</request-initial-auth-details>
EOT;
        return $xml;
    }

    protected function _getParameterizedUrlsXml()
    {
        return '';
        $xml = <<<EOT
            <parameterized-urls>
                <parameterized-url url="{$this->_getParameterizedUrl()}" />
            </parameterized-urls>
EOT;
        return $xml;
    }

    protected function _getPlatformIdXml()
    {
        $xml = <<<EOT
            <platform-id>473325629220583</platform-id>
EOT;
        return $xml;
    }

    protected function _getAnalyticsDataXml()
    {
        if (!($analytics = $this->getApi()->getAnalyticsData())) {
            return '';
        }
        $xml = <<<EOT
            <analytics-data><![CDATA[{$analytics}]]></analytics-data>
EOT;
        return $xml;
    }

    protected function _getEditCartUrl()
    {
        return Mage::getUrl('googlecheckout/redirect/cart');
    }

    protected function _getContinueShoppingUrl()
    {
        return Mage::getUrl('googlecheckout/redirect/continue');
    }

    protected function _getNotificationsUrl()
    {
        return $this->_getCallbackUrl();
    }

    protected function _getCalculationsUrl()
    {
        return $this->_getCallbackUrl();
    }

    protected function _getParameterizedUrl()
    {
        return Mage::getUrl('googlecheckout/api/beacon');
    }

    protected function _isOrderVirtual()
    {
        $orderIsVirual = true;
        foreach ($this->getQuote()->getAllItems() as $item) {
            if (!$item->getIsVirtual()) {
                $orderIsVirual = false;
                break;
            }
        }
        return $orderIsVirual;
    }
}