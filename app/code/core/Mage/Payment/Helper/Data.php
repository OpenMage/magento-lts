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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment module base helper
 *
 * @category   Mage
 * @package    Mage_Payment
 */
class Mage_Payment_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_PAYMENT_METHODS = 'payment';
    public const XML_PATH_PAYMENT_GROUPS = 'global/payment/groups';

    protected $_moduleName = 'Mage_Payment';

    /**
     * Retrieve method model object
     *
     * @param   string $code
     * @return  Mage_Payment_Model_Method_Abstract|false
     */
    public function getMethodInstance($code)
    {
        $key = self::XML_PATH_PAYMENT_METHODS . '/' . $code . '/model';
        $class = Mage::getStoreConfig($key);
        if (is_null($class)) {
            Mage::logException(new Exception(sprintf('Unknown payment method with code "%s"', $code)));
            return false;
        }
        return Mage::getModel($class);
    }

    /**
     * Get and sort available payment methods for specified or current store
     *
     * array structure:
     *  $index => Varien_Simplexml_Element
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Payment_Model_Method_Abstract[]
     */
    public function getStoreMethods($store = null, $quote = null)
    {
        $res = [];
        foreach ($this->getPaymentMethods($store) as $code => $methodConfig) {
            $prefix = self::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
            if (!$model = Mage::getStoreConfig($prefix . 'model', $store)) {
                continue;
            }
            $methodInstance = Mage::getModel($model);
            if (!$methodInstance) {
                continue;
            }
            $methodInstance->setStore($store);
            if (!$methodInstance->isAvailable($quote)) {
                /* if the payment method cannot be used at this time */
                continue;
            }
            $sortOrder = (int)$methodInstance->getConfigData('sort_order', $store);
            $methodInstance->setSortOrder($sortOrder);
            $res[] = $methodInstance;
        }

        usort($res, [$this, '_sortMethods']);
        return $res;
    }

    /**
     * @param object $a
     * @param object $b
     * @return int
     */
    protected function _sortMethods($a, $b)
    {
        if (is_object($a)) {
            return (int)$a->sort_order < (int)$b->sort_order ? -1 : ((int)$a->sort_order > (int)$b->sort_order ? 1 : 0);
        }
        return 0;
    }

    /**
     * Retrieve payment method form html
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return  Mage_Payment_Block_Form|Mage_Core_Block_Abstract
     */
    public function getMethodFormBlock(Mage_Payment_Model_Method_Abstract $method)
    {
        $block = false;
        $blockType = $method->getFormBlockType();
        if ($this->getLayout()) {
            $block = $this->getLayout()->createBlock($blockType);
            $block->setMethod($method);
        }
        return $block;
    }

    /**
     * Retrieve payment information block
     *
     * @param   Mage_Payment_Model_Info $info
     * @return  Mage_Core_Block_Template|Mage_Core_Block_Abstract
     */
    public function getInfoBlock(Mage_Payment_Model_Info $info)
    {
        $blockType = $info->getMethodInstance()->getInfoBlockType();
        if ($this->getLayout()) {
            $block = $this->getLayout()->createBlock($blockType);
        } else {
            $className = Mage::getConfig()->getBlockClassName($blockType);
            $block = new $className();
        }
        $block->setInfo($info);
        return $block;
    }

    /**
     * Retrieve available billing agreement methods
     *
     * @param mixed $store
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getBillingAgreementMethods($store = null, $quote = null)
    {
        $result = [];
        foreach ($this->getStoreMethods($store, $quote) as $method) {
            if ($method->canManageBillingAgreements()) {
                $result[] = $method;
            }
        }
        return $result;
    }

    /**
     * Get payment methods that implement recurring profilez management
     *
     * @param mixed $store
     * @return array
     */
    public function getRecurringProfileMethods($store = null)
    {
        $result = [];
        foreach ($this->getPaymentMethods($store) as $code => $data) {
            $method = $this->getMethodInstance($code);
            if ($method && $method->canManageRecurringProfiles()) {
                $result[] = $method;
            }
        }
        return $result;
    }

    /**
     * Retrieve all payment methods
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     * @return array
     */
    public function getPaymentMethods($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_METHODS, $store);
    }

    /**
     * Retrieve all payment methods list as an array
     *
     * Possible output:
     * 1) assoc array as <code> => <title>
     * 2) array of array('label' => <title>, 'value' => <code>)
     * 3) array of array(
     *                 array('value' => <code>, 'label' => <title>),
     *                 array('value' => array(
     *                     'value' => array(array(<code1> => <title1>, <code2> =>...),
     *                     'label' => <group name>
     *                 )),
     *                 array('value' => <code>, 'label' => <title>),
     *                 ...
     *             )
     *
     * @param bool $sorted
     * @param bool $asLabelValue
     * @param bool $withGroups
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     * @return array
     */
    public function getPaymentMethodList($sorted = true, $asLabelValue = false, $withGroups = false, $store = null)
    {
        $methods = [];
        $groups = [];
        $groupRelations = [];

        foreach ($this->getPaymentMethods($store) as $code => $data) {
            if ((isset($data['title']))) {
                $methods[$code] = $data['title'];
            } else {
                if ($this->getMethodInstance($code)) {
                    $methods[$code] = $this->getMethodInstance($code)->getConfigData('title', $store);
                }
            }
            if ($asLabelValue && $withGroups && isset($data['group'])) {
                $groupRelations[$code] = $data['group'];
            }
        }
        if ($asLabelValue && $withGroups) {
            $groups = Mage::app()->getConfig()->getNode(self::XML_PATH_PAYMENT_GROUPS)->asCanonicalArray();
            foreach ($groups as $code => $title) {
                $methods[$code] = $title; // for sorting, see below
            }
        }
        if ($sorted) {
            asort($methods);
        }
        if ($asLabelValue) {
            $labelValues = [];
            foreach ($methods as $code => $title) {
                $labelValues[$code] = [];
            }
            foreach ($methods as $code => $title) {
                if (isset($groups[$code])) {
                    $labelValues[$code]['label'] = $title;
                } elseif (isset($groupRelations[$code])) {
                    unset($labelValues[$code]);
                    $labelValues[$groupRelations[$code]]['value'][$code] = ['value' => $code, 'label' => $title . ' (' . $code . ')'];
                } else {
                    $labelValues[$code] = ['value' => $code, 'label' => $title . ' (' . $code . ')'];
                }
            }
            return $labelValues;
        }

        return $methods;
    }

    /**
     * Retrieve all billing agreement methods (code and label)
     *
     * @return array
     */
    public function getAllBillingAgreementMethods()
    {
        $result = [];
        $interface = 'Mage_Payment_Model_Billing_Agreement_MethodInterface';
        foreach ($this->getPaymentMethods() as $code => $data) {
            if (!isset($data['model'])) {
                continue;
            }
            $method = Mage::app()->getConfig()->getModelClassName($data['model']);
            if (in_array($interface, class_implements($method))) {
                $result[$code] = $data['title'];
            }
        }
        return $result;
    }

    /**
     * Returns value of Zero Subtotal Checkout / Enabled
     *
     * @param mixed $store
     * @return bool
     */
    public function isZeroSubTotal($store = null)
    {
        return Mage::getStoreConfig(Mage_Payment_Model_Method_Free::XML_PATH_PAYMENT_FREE_ACTIVE, $store);
    }

    /**
     * Returns value of Zero Subtotal Checkout / New Order Status
     *
     * @param mixed $store
     * @return string
     */
    public function getZeroSubTotalOrderStatus($store = null)
    {
        return Mage::getStoreConfig(Mage_Payment_Model_Method_Free::XML_PATH_PAYMENT_FREE_ORDER_STATUS, $store);
    }

    /**
     * Returns value of Zero Subtotal Checkout / Automatically Invoice All Items
     *
     * @param mixed $store
     * @return string
     */
    public function getZeroSubTotalPaymentAutomaticInvoice($store = null)
    {
        return Mage::getStoreConfig(Mage_Payment_Model_Method_Free::XML_PATH_PAYMENT_FREE_PAYMENT_ACTION, $store);
    }
}
