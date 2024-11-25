<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product form price field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Price extends Varien_Data_Form_Element_Text
{
    /**
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->addClass('validate-zero-or-greater');
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        /**
         * getEntityAttribute - use __call
         */
        $addJsObserver = false;
        if ($attribute = $this->getEntityAttribute()) {
            if (!($storeId = $attribute->getStoreId())) {
                $storeId = $this->getForm()->getDataObject()->getStoreId();
            }
            $store = Mage::app()->getStore($storeId);
            $html .= '<strong>[' . (string) $store->getBaseCurrencyCode() . ']</strong>';
            if (Mage::helper('tax')->priceIncludesTax($store)) {
                if ($attribute->getAttributeCode() !== 'cost') {
                    $addJsObserver = true;
                    $html .= ' <strong>[' . Mage::helper('tax')->__('Inc. Tax') . '<span id="dynamic-tax-' . $attribute->getAttributeCode() . '"></span>]</strong>';
                }
            }
        }
        if ($addJsObserver) {
            $html .= $this->_getTaxObservingCode($attribute);
        }

        return $html;
    }

    /**
     * @param $attribute
     * @return string
     */
    protected function _getTaxObservingCode($attribute)
    {
        $spanId = "dynamic-tax-{$attribute->getAttributeCode()}";

        return "<script type='text/javascript'>if (dynamicTaxes == undefined) var dynamicTaxes = new Array(); dynamicTaxes[dynamicTaxes.length]='{$attribute->getAttributeCode()}'</script>";
    }

    /**
     * @param null $index deprecated
     * @return string|null
     */
    public function getEscapedValue($index = null)
    {
        $value = $this->getValue();

        if (!is_numeric($value)) {
            return null;
        }

        return number_format((float) $value, 2, null, '');
    }
}
