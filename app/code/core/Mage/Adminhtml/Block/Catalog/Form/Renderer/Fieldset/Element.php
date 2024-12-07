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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog fieldset element renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    /**
     * Initialize block template
     */
    protected function _construct()
    {
        $this->setTemplate('catalog/form/renderer/fieldset/element.phtml');
    }

    /**
     * Retrieve data object related with form
     *
     * @return Mage_Catalog_Model_Product | Mage_Catalog_Model_Category
     */
    public function getDataObject()
    {
        return $this->getElement()->getForm()->getDataObject();
    }

    /**
     * Retireve associated with element attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttribute()
    {
        return $this->getElement()->getEntityAttribute();
    }

    /**
     * Retrieve associated attribute code
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->getAttribute()->getAttributeCode();
    }

    /**
     * Check "Use default" checkbox display availability
     *
     * @return bool
     */
    public function canDisplayUseDefault()
    {
        if ($attribute = $this->getAttribute()) {
            if (!$attribute->isScopeGlobal()
                && $this->getDataObject()
                && $this->getDataObject()->getId()
                && $this->getDataObject()->getStoreId()
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check default value usage fact
     *
     * @return bool
     */
    public function usedDefault()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $defaultValue = $this->getDataObject()->getAttributeDefaultValue($attributeCode);

        if (!$this->getDataObject()->getExistsStoreValueFlag($attributeCode)) {
            return true;
        } elseif ($this->getElement()->getValue() == $defaultValue &&
            $this->getDataObject()->getStoreId() != $this->_getDefaultStoreId()
        ) {
            return false;
        }
        if ($defaultValue === false && !$this->getAttribute()->getIsRequired() && $this->getElement()->getValue()) {
            return false;
        }
        return $defaultValue === false;
    }

    /**
     * Disable field in default value using case
     *
     * @return $this
     */
    public function checkFieldDisable()
    {
        if ($this->canDisplayUseDefault() && $this->usedDefault()) {
            $this->getElement()->setDisabled(true);
        }
        return $this;
    }

    /**
     * Retrieve label of attribute scope
     *
     * GLOBAL | WEBSITE | STORE
     *
     * @return string
     */
    public function getScopeLabel()
    {
        $html = '';
        $attribute = $this->getElement()->getEntityAttribute();
        if (!$attribute || Mage::app()->isSingleStoreMode() || $attribute->getFrontendInput() == 'gallery') {
            return $html;
        }

        /*
         * Check if the current attribute is a 'price' attribute. If yes, check
         * the config setting 'Catalog Price Scope' and modify the scope label.
         */
        $isGlobalPriceScope = false;
        if ($attribute->getFrontendInput() == 'price') {
            $priceScope = Mage::getStoreConfig('catalog/price/scope');
            if ($priceScope == 0) {
                $isGlobalPriceScope = true;
            }
        }

        if ($attribute->isScopeGlobal() || $isGlobalPriceScope) {
            $html .= Mage::helper('adminhtml')->__('[GLOBAL]');
        } elseif ($attribute->isScopeWebsite()) {
            $html .= Mage::helper('adminhtml')->__('[WEBSITE]');
        } elseif ($attribute->isScopeStore()) {
            $html .= Mage::helper('adminhtml')->__('[STORE VIEW]');
        }

        return $html;
    }

    /**
     * Retrieve element label html
     *
     * @return string
     */
    public function getElementLabelHtml()
    {
        $element = $this->getElement();
        $label = $element->getLabel();
        if (!empty($label)) {
            $element->setLabel($this->__($label));
        }
        return $element->getLabelHtml();
    }

    /**
     * Retrieve element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        return $this->getElement()->getElementHtml();
    }

    /**
     * Default sore ID getter
     *
     * @return int
     */
    protected function _getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }
}
