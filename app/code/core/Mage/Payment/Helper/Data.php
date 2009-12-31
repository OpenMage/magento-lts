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
 * @package     Mage_Payment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment module base helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payment_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_PAYMENT_METHODS = 'payment';

    /**
     * Retrieve method model object
     *
     * @param   string $code
     * @return  Mage_Payment_Model_Method_Abstract
     */
    public function getMethodInstance($code)
    {
        $key = self::XML_PATH_PAYMENT_METHODS.'/'.$code.'/model';
        $class = Mage::getStoreConfig($key);
        if (!$class) {
            Mage::throwException($this->__('Cannot load configuration for payment method "%s"', $code));
        }
        return Mage::getModel($class);
    }

    /**
     * Get and sort available payment methods for specified or current store
     *
     * array structure:
     *  $index => Varien_Simplexml_Element
     *
     * @param   mixed $store
     * @return  array
     */
    public function getStoreMethods($store = null, $quote = null)
    {
        $methods = Mage::getStoreConfig(self::XML_PATH_PAYMENT_METHODS, $store);
        $res = array();
        foreach ($methods as $code => $methodConfig) {
            $prefix = self::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
            if (!$model = Mage::getStoreConfig($prefix . 'model', $store)) {
                continue;
            }
            $methodInstance = Mage::getModel($model);
            if (!$methodInstance->isAvailable($quote)) {
                /* if the payment method can not be used at this time */
                continue;
            }

            $sortOrder = (int)Mage::getStoreConfig($prefix . 'sort_order', $store);
            $methodInstance->setSortOrder($sortOrder);
            $methodInstance->setStore($store);
            $res[] = $methodInstance;
        }

        usort($res, array($this, '_sortMethods'));
        return $res;
    }

    protected function _sortMethods($a, $b)
    {
        if (is_object($a)) {
            return (int)$a->sort_order < (int)$b->sort_order ? -1 : ((int)$a->sort_order > (int)$b->sort_order ? 1 : 0);
        }
        return 0;
    }

    /**
     * Retreive payment method form html
     *
     * @param   Mage_Payment_Model_Abstract $method
     * @return  Mage_Payment_Block_Form
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
     * @return  Mage_Core_Block_Template
     */
    public function getInfoBlock(Mage_Payment_Model_Info $info)
    {
        $blockType = $info->getMethodInstance()->getInfoBlockType();
        if ($this->getLayout()) {
            $block = $this->getLayout()->createBlock($blockType);
        }
        else {
            $className = Mage::getConfig()->getBlockClassName($blockType);
            $block = new $className;
        }
        $block->setInfo($info);
        return $block;
    }
}
