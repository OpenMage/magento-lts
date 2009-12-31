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
 * Config model that is aware of all Mage_Paypal payment methods
 * Works with PayPal-specific system configuration
 */
class Mage_Paypal_Model_Config
{
    /**
     * PayPal Standard
     * @var string
     */
    const METHOD_WPS         = 'paypal_standard';

    /**
     * PayPal Website Payments Pro - Express Checkout
     * @var string
     */
    const METHOD_WPP_EXPRESS = 'paypal_express';

    /**
     * PayPal Website Payments Pro - Direct Payments
     * @var string
     */
    const METHOD_WPP_DIRECT  = 'paypal_direct';

    /**
     * Current payment method code
     * @var string
     */
    protected $_methodCode = null;

    /**
     * Current store id
     * @var int
     */
    protected $_storeId = null;

    /**
     * Set method and store id, if specified
     * @param array $params
     */
    public function __construct($params = array())
    {
        if ($params) {
            $method = array_shift($params);
            $this->setMethod($method);
            if ($params) {
                $storeId = array_shift($params);
                $this->setStoreId($storeId);
            }
        }
    }

    /**
     * Method code setter
     *
     * @param string|Mage_Payment_Model_Method_Abstract $method
     * @return Mage_Paypal_Model_Config
     */
    public function setMethod($method)
    {
        if ($method instanceof Mage_Payment_Model_Method_Abstract) {
            $this->_methodCode = $method->getCode();
        } elseif (is_string($method)) {
            $this->_methodCode = $method;
        }
        return $this;
    }

    /**
     * Store ID setter
     * @param int $storeId
     * @return Mage_Paypal_Model_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Config field magic getter
     * The specified key can be either in camelCase or under_score format
     * Tries to map specified value according to set payment method code, into the configuration value
     * Sets the values into public class parameters, to avoid redundant calls of this method
     *
     * @param string $key
     * @return string|null
     */
    public function __get($key)
    {
        $underscored = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $key));
        $value = Mage::getStoreConfig($this->_getSpecificConfigPath($underscored), $this->_storeId);
        $this->$key = $value;
        $this->$underscored = $value;
        return $value;
    }

    /**
     * Map any supported payment method into a config path by specified field name
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        if (self::METHOD_WPS === $this->_methodCode) {
            return $this->_mapStandardFieldset($fieldName);
        } elseif (self::METHOD_WPP_EXPRESS === $this->_methodCode ||  self::METHOD_WPP_DIRECT === $this->_methodCode) {
            $path = self::METHOD_WPP_EXPRESS ? $this->_mapExpressFieldset($fieldName)
                : $this->_mapDirectFieldset($fieldName);
            if (!$path) {
                $path = $this->_mapWppFieldset($fieldName);
            }
            if (!$path) {
                $path = $this->_mapWppStyleFieldset($fieldName);
            }
            return $path;
        }
    }

    /**
     * Map PayPal Standard config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapStandardFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'business_account':
            case 'business_name':
            case 'debug_flag':
            case 'sandbox_flag':
                return "paypal/wps/{$fieldName}";
            case 'active':
            case 'title':
            case 'payment_action':
            case 'types':
            case 'order_status':
            case 'transaction_type':
            case 'sort_order':
            case 'allowspecific':
            case 'specificcountry':
                return 'payment/' . self::METHOD_WPS . "/{$fieldName}";
        }
    }

    /**
     * Map PayPal Website Payments Pro common style config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapWppStyleFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'button_flavor':
            case 'button_type':
            case 'logo_url':
            case 'page_style':
            case 'paypal_hdrbackcolor':
            case 'paypal_hdrbordercolor':
            case 'paypal_hdrimg':
            case 'paypal_payflowcolor':
                return "paypal/style/{$fieldName}";
        }
    }

    /**
     * Map PayPal Website Payments Pro common config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapWppFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'api_password':
            case 'api_signature':
            case 'api_username':
            case 'business_account':
            case 'debug_flag':
            case 'paypal_url':
            case 'proxy_host':
            case 'proxy_port':
            case 'sandbox_flag':
            case 'use_proxy':
                return "paypal/wpp/{$fieldName}";
        }
    }

    /**
     * Map PayPal Express config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapExpressFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'active':
            case 'allowspecific':
            case 'fraud_filter':
            case 'invoice_email_copy':
            case 'line_item':
            case 'order_status':
            case 'payment_action':
            case 'solution_type':
            case 'sort_order':
            case 'specificcountry':
            case 'title':
            case 'visible_on_cart':
                return 'payment/' . self::METHOD_WPP_EXPRESS . "/{$fieldName}";
        }
    }

    /**
     * Map PayPal Direct config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapDirectFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'active':
            case 'allowspecific':
            case 'cctypes':
            case 'centinel':
            case 'centinel_maps_url':
            case 'centinel_merchant_id':
            case 'centinel_password':
            case 'centinel_processor_id':
            case 'centinel_timeout_connect':
            case 'centinel_timeout_read':
            case 'fraud_filter':
            case 'line_item':
            case 'order_status':
            case 'payment_action':
            case 'sort_order':
            case 'specificcountry':
            case 'title':
                return 'payment/' . self::METHOD_WPP_DIRECT . "/{$fieldName}";
        }
    }

    /**
     * PayPal gateway submission URL getter
     *
     * @return string
     */
    public function getPaypalUrl()
    {
         if ($this->sandboxFlag) {
             return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
         }
         return 'https://www.paypal.com/cgi-bin/webscr';
    }
}
