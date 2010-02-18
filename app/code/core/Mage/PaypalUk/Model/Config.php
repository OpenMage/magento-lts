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
 * @package     Mage_PaypalUk
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config model that is aware of all Mage_PaypalUk payment methods
 * Works with PayPal-specific system configuration
 */
class Mage_PaypalUk_Model_Config extends Mage_Paypal_Model_Config
{
    /**
     * PayPal Website Payments Pro (Payflow Edition) - Direct Payments
     *
     * @var string
     */
    const METHOD_WPP_PE_DIRECT  = 'paypaluk_direct';
    const METHOD_WPP_PE_EXPRESS  = 'paypaluk_express';

    /**
     * Instructions for generating proper BN code (Payflow Edition)
     *
     * @var array
     */
    protected $_buildNotationPPMap = array(
        'paypaluk_direct'   => 'DP',
    );

    /**
     * Payment actions
     *
     * @var string
     */
    const PAYMENT_ACTION_SALE  = 'Sale';
    const PAYMENT_ACTION_ORDER = 'Order';
    const PAYMENT_ACTION_AUTH  = 'Authorization';

    /**
     * Map any supported payment method into a config path by specified field name
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        $path = null;
        if (self::METHOD_WPP_PE_DIRECT === $this->_methodCode) {
            $path = $this->_mapDirectFieldset($fieldName);
        }
        if (self::METHOD_WPP_PE_EXPRESS === $this->_methodCode) {
            $path = $this->_mapExpressFieldset($fieldName);
        }

        if (!$path) {
            $path = $this->_mapWpukFieldset($fieldName);
        }
        if (!$path) {
            $path = $this->_mapWppStyleFieldset($fieldName);
        }
        return $path;
    }

    /**
     * Map PayPal Direct (Payflow Edition) config fields
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
            case 'centinel_is_mode_strict':
            case 'centinel_api_url':
            case 'fraud_filter':
            case 'line_items_enabled':
            case 'order_status':
            case 'payment_action':
            case 'sort_order':
            case 'specificcountry':
            case 'title':
                return 'payment/' . self::METHOD_WPP_PE_DIRECT . "/{$fieldName}";
        }
    }

    /**
     * Mapper from PayPalUk-specific payment actions to Magento payment actions
     *
     * @return string|null
     */
    public function getPaymentAction()
    {
        switch ($this->paymentAction) {
            case self::PAYMENT_ACTION_AUTH:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
            case self::PAYMENT_ACTION_SALE:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
            case self::PAYMENT_ACTION_ORDER:
                return;
        }
    }

    /**
     * Payment actions source getter
     *
     * @return array
     */
    public function getPaymentActions()
    {
        return array(
            self::PAYMENT_ACTION_AUTH  => Mage::helper('paypal')->__('Authorization'),
            self::PAYMENT_ACTION_SALE  => Mage::helper('paypal')->__('Sale'),
        );
    }

    /**
     * PayPal Direct cc types source getter
     *
     * @return array
     */
    public function getDirectCcTypesAsOptionArray()
    {
        $model = Mage::getModel('payment/source_cctype')->setAllowedTypes(array('VI', 'MC', 'AE', 'DI', 'SS', 'OT'));
        return $model->toOptionArray();
    }

    /**
     * Map PayPal Website Payments Pro common config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapWpukFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'url':
            case 'partner':
            case 'user':
            case 'vendor':
            case 'pwd':
            case 'debug_flag':
            case 'sandbox_flag':
                return "paypal/wpuk/{$fieldName}";
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
            case 'line_items_enabled':
            case 'order_status':
            case 'payment_action':
            case 'solution_type':
            case 'sort_order':
            case 'specificcountry':
            case 'title':
            case 'visible_on_cart':
                return 'payment/' . self::METHOD_WPP_PE_EXPRESS . "/{$fieldName}";
        }
    }
}
