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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract class for Paypal API wrappers
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Paypal_Model_Api_Abstract extends Varien_Object
{
    /**
     * Config instance
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * Global private to public interface map
     * @var array
     */
    protected $_globalMap = array();

    /**
     * Filter callbacks for exporting $this data to API call
     *
     * @var array
     */
    protected $_exportToRequestFilters = array();

    /**
     * Filter callbacks for importing API result to $this data
     *
     * @var array
     */
    protected $_importFromRequestFilters = array();

    /**
     * Line items export to request mapping settings
     * @var array
     */
    protected $_lineItemExportItemsFormat = array();
    protected $_lineItemExportItemsFilters = array();
    protected $_lineItemTotalExportMap = array();

    /**
     * PayPal shopping cart instance
     *
     * @var Mage_Paypal_Model_Cart
     */
    protected $_cart = null;

    /**
     * Shipping options export to request mapping settings
     * @var array
     */
    protected $_shippingOptionsExportItemsFormat = array();

    /**
     * Imported recurring profiles array
     *
     * @var array
     */
    protected $_recurringPaymentProfiles = array();

   /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array();

    /**
     * Return Paypal Api user name based on config data
     *
     * @return string
     */
    public function getApiUsername()
    {
        return $this->_config->apiUsername;
    }

    /**
     * Return Paypal Api password based on config data
     *
     * @return string
     */
    public function getApiPassword()
    {
        return $this->_config->apiPassword;
    }

    /**
     * Return Paypal Api signature based on config data
     *
     * @return string
     */
    public function getApiSignature()
    {
        return $this->_config->apiSignature;
    }

    /**
     * Return Paypal Api certificate based on config data
     *
     * @return string
     */
    public function getApiCertificate()
    {
        return $this->_config->getApiCertificate();
    }

    /**
     * BN code getter
     *
     * @return string
     */
    public function getBuildNotationCode()
    {
        return $this->_config->getBuildNotationCode();
    }

    /**
     * Return Paypal Api proxy status based on config data
     *
     * @return bool
     */
    public function getUseProxy()
    {
        return $this->_getDataOrConfig('use_proxy', false);
    }

    /**
     * Return Paypal Api proxy host based on config data
     *
     * @return string
     */
    public function getProxyHost()
    {
        return $this->_getDataOrConfig('proxy_host', '127.0.0.1');
    }

    /**
     * Return Paypal Api proxy port based on config data
     *
     * @return string
     */
    public function getProxyPort()
    {
        return $this->_getDataOrConfig('proxy_port', '808');
    }

    /**
     * @deprecated after 1.4.1.0
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->getDebugFlag();
    }

    /**
     * PayPal page CSS getter
     *
     * @return string
     */
    public function getPageStyle()
    {
        return $this->_getDataOrConfig('page_style');
    }

    /**
     * PayPal page header image URL getter
     *
     * @return string
     */
    public function getHdrimg()
    {
        return $this->_getDataOrConfig('paypal_hdrimg');
    }

    /**
     * PayPal page header border color getter
     *
     * @return string
     */
    public function getHdrbordercolor()
    {
        return $this->_getDataOrConfig('paypal_hdrbordercolor');
    }

    /**
     * PayPal page header background color getter
     *
     * @return string
     */
    public function getHdrbackcolor()
    {
        return $this->_getDataOrConfig('paypal_hdrbackcolor');
    }

    /**
     * PayPal page "payflow color" (?) getter
     *
     * @return string
     */
    public function getPayflowcolor()
    {
        return $this->_getDataOrConfig('paypal_payflowcolor');
    }

    /**
     * Payment action getter
     *
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->_getDataOrConfig('payment_action');
    }

    /**
     * PayPal merchant email getter
     */
    public function getBusinessAccount()
    {
        return $this->_getDataOrConfig('business_account');
    }

    /**
     * Import $this public data to specified object or array
     *
     * @param array|Varien_Object $to
     * @param array $publicMap
     * @return array|Varien_Object
     */
    public function &import($to, array $publicMap = array())
    {
        return Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $to, $publicMap);
    }

    /**
     * Export $this public data from specified object or array
     *
     * @param array|Varien_Object $from
     * @param array $publicMap
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function export($from, array $publicMap = array())
    {
        Varien_Object_Mapper::accumulateByMap($from, array($this, 'setDataUsingMethod'), $publicMap);
        return $this;
    }

    /**
     * Set PayPal cart instance
     *
     * @param Mage_Paypal_Model_Cart $cart
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setPaypalCart(Mage_Paypal_Model_Cart $cart)
    {
        $this->_cart = $cart;
        return $this;
    }

    /**
     * Config instance setter
     * @param Mage_Paypal_Model_Config $config
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setConfigObject(Mage_Paypal_Model_Config $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Current locale code getter
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }

    /**
     * Always take into accoun
     */
    public function getFraudManagementFiltersEnabled()
    {
        return 1;
    }

    /**
     * Set recurring profiles
     *
     * @param array $items
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function addRecurringPaymentProfiles(array $items)
    {
        if ($items) {
            $this->_recurringPaymentProfiles = $items;
        }
        return $this;
    }

    /**
     * Export $this public data to private request array
     *
     * @param array $internalRequestMap
     * @param array $request
     * @return array
     */
    protected function &_exportToRequest(array $privateRequestMap, array $request = array())
    {
        $map = array();
        foreach ($privateRequestMap as $key) {
            if (isset($this->_globalMap[$key])) {
                $map[$this->_globalMap[$key]] = $key;
            }
        }
        $result = Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $request, $map);
        foreach ($privateRequestMap as $key) {
            if (isset($this->_exportToRequestFilters[$key]) && isset($result[$key])) {
                $callback   = $this->_exportToRequestFilters[$key];
                $privateKey = $result[$key];
                $publicKey  = $map[$this->_globalMap[$key]];
                $result[$key] = call_user_func(array($this, $callback), $privateKey, $publicKey);
            }
        }
        return $result;
    }

    /**
     * Import $this public data from a private response array
     *
     * @param array $privateResponseMap
     * @param array $response
     */
    protected function _importFromResponse(array $privateResponseMap, array $response)
    {
        $map = array();
        foreach ($privateResponseMap as $key) {
            if (isset($this->_globalMap[$key])) {
                $map[$key] = $this->_globalMap[$key];
            }
            if (isset($response[$key]) && isset($this->_importFromRequestFilters[$key])) {
                $callback = $this->_importFromRequestFilters[$key];
                $response[$key] = call_user_func(array($this, $callback), $response[$key], $key, $map[$key]);
            }
        }
        Varien_Object_Mapper::accumulateByMap($response, array($this, 'setDataUsingMethod'), $map);
    }

    /**
     * Prepare line items request
     *
     * Returns true if there were line items added
     *
     * @param array &$request
     * @param int $i
     * @return true|bool
     */
    protected function _exportLineItems(array &$request, $i = 0)
    {
        if (!$this->_cart) {
            return;
        }

        // always add cart totals, even if line items are not requested
        if ($this->_lineItemTotalExportMap) {
            foreach ($this->_cart->getTotals() as $key => $total) {
                if (isset($this->_lineItemTotalExportMap[$key])) { // !empty($total)
                    $privateKey = $this->_lineItemTotalExportMap[$key];
                    $request[$privateKey] = $this->_filterAmount($total);
                }
            }
        }

        // add cart line items
        $items = $this->_cart->getItems();
        if (empty($items) || !$this->getIsLineItemsEnabled()) {
            return;
        }
        $result = null;
        foreach ($items as $item) {
            foreach ($this->_lineItemExportItemsFormat as $publicKey => $privateFormat) {
                $result = true;
                $value = $item->getDataUsingMethod($publicKey);
                if (isset($this->_lineItemExportItemsFilters[$publicKey])) {
                    $callback   = $this->_lineItemExportItemsFilters[$publicKey];
                    $value = call_user_func(array($this, $callback), $value);
                }
                if (is_float($value)) {
                    $value = $this->_filterAmount($value);
                }
                $request[sprintf($privateFormat, $i)] = $value;
            }
            $i++;
        }
        return $result;
    }

    /**
     * Prepare shipping options request
     * Returns false if there are no shipping options
     *
     * @param array &$request
     * @param int $i
     * @return bool
     */
    protected function _exportShippingOptions(array &$request, $i = 0)
    {
        $options = $this->getShippingOptions();
        if (empty($options)) {
            return false;
        }
        foreach ($options as $option) {
            foreach ($this->_shippingOptionsExportItemsFormat as $publicKey => $privateFormat) {
                $value = $option->getDataUsingMethod($publicKey);
                if (is_float($value)) {
                    $value = $this->_filterAmount($value);
                }
                if (is_bool($value)) {
                    $value = $this->_filterBool($value);
                }
                $request[sprintf($privateFormat, $i)] = $value;
            }
            $i++;
        }
        return true;
    }

    /**
     * Filter amounts in API calls
     * @param float|string $value
     * @return string
     */
    protected function _filterAmount($value)
    {
        return sprintf('%.2F', $value);
    }

    /**
     * Filter boolean values in API calls
     *
     * @param mixed $value
     * @return string
     */
    protected function _filterBool($value)
    {
        return ($value) ? 'true' : 'false';
    }

    /**
     * Filter int values in API calls
     *
     * @param mixed $value
     * @return int
     */
    protected function _filterInt($value)
    {
        return (int)$value;
    }

    /**
     * Unified getter that looks in data or falls back to config
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getDataOrConfig($key, $default = null)
    {
        if ($this->hasData($key)) {
            return $this->getData($key);
        }
        return $this->_config->$key ? $this->_config->$key : $default;
    }


    /**
     * region_id workaround: PayPal requires state code, try to find one in the address
     *
     * @param Varien_Object $address
     * @return string
     */
    protected function _lookupRegionCodeFromAddress(Varien_Object $address)
    {
        if ($regionId = $address->getData('region_id')) {
            $region = Mage::getModel('directory/region')->load($regionId);
            if ($region->getId()) {
                return $region->getCode();
            }
        }
        return '';
    }

    /**
     * Street address workaround: divides address lines into parts by specified keys
     * (keys should go as 3rd, 4th[...] parameters)
     *
     * @param Varien_Object $address
     * @param array $request
     */
    protected function _importStreetFromAddress(Varien_Object $address, array &$to)
    {
        $keys = func_get_args(); array_shift($keys); array_shift($keys);
        $street = $address->getStreet();
        if (!$keys || !$street || !is_array($street)) {
            return;
        }

        $street = Mage::helper('customer/address')
            ->convertStreetLines($address->getStreet(), count($keys));

        $i = 0;
        foreach ($keys as $key) {
            $to[$key] = isset($street[$i]) ? $street[$i]: '';
            $i++;
        }
    }

    /**
     * Build query string from request
     *
     * @param array $request
     * @return string
     */
    protected function _buildQuery($request)
    {
        return http_build_query($request);
    }

    /**
     * Filter qty in API calls
     * Paypal note: The value for quantity must be a positive integer. Null, zero, or negative numbers are not allowed.
     *
     * @param float|string|int $value
     * @return string
     */
    protected function _filterQty($value)
    {
        return intval($value);
    }

    /**
     * Log debug data to file
     *
     * @param mixed $debugData
     */
    protected function _debug($debugData)
    {
        if ($this->getDebugFlag()) {
            Mage::getModel('core/log_adapter', 'payment_' . $this->_config->getMethodCode() . '.log')
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
        return $this->_config->debug;
    }

    /**
     * Check whether API certificate authentication should be used
     *
     * @return bool
     */
    public function getUseCertAuthentication()
    {
        return (bool)$this->_config->apiAuthentication;
    }
}
