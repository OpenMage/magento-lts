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
 * Catalog product gallery attribute
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        return $this->getContentHtml();
    }

    /**
     * Prepares content block
     *
     * @return string
     */
    public function getContentHtml()
    {
        /** @var Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content $content */
        $content = Mage::getSingleton('core/layout')
            ->createBlock('adminhtml/catalog_product_helper_form_gallery_content');

        $content
            ->setId($this->getHtmlId() . '_content')
            ->setElement($this);
        return $content->toHtml();
    }

    public function getLabel()
    {
        return '';
    }

    /**
     * Check "Use default" checkbox display availability
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return bool
     */
    public function canDisplayUseDefault($attribute)
    {
        if (!$attribute->isScopeGlobal() && $this->getDataObject()->getStoreId()) {
            return true;
        }

        return false;
    }

    /**
     * Check default value usage fact
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute|string $attribute
     * @return bool
     */
    public function usedDefault($attribute)
    {
        if (is_string($attribute)) {
            $attributeCode = $attribute;
        } else {
            $attributeCode = $attribute->getAttributeCode();
        }

        // special management for "label" and "position" since they're columns of the
        // catalog_product_entity_media_gallery_value database table
        if ($attributeCode == 'label' || $attributeCode == 'position') {
            $mediaGallery = $this->getDataObject()->getMediaGallery();
            if (!count($mediaGallery['images'])) {
                return true;
            }
            return $mediaGallery['images'][0]["{$attributeCode}_use_default"];
        }

        $defaultValue = $this->getDataObject()->getAttributeDefaultValue($attributeCode);
        if (!$this->getDataObject()->getExistsStoreValueFlag($attributeCode)) {
            return true;
        } elseif ($this->getValue() == $defaultValue && $this->getDataObject()->getStoreId() != $this->_getDefaultStoreId()) {
            return false;
        }
        if ($defaultValue === false && !$attribute->getIsRequired() && $this->getValue()) {
            return false;
        }
        return $defaultValue === false;
    }

    /**
     * Retrieve label of attribute scope
     *
     * GLOBAL | WEBSITE | STORE
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return string
     */
    public function getScopeLabel($attribute)
    {
        $html = '';
        if (Mage::app()->isSingleStoreMode()) {
            return $html;
        }

        if ($attribute->isScopeGlobal()) {
            $html .= '<br/>' . Mage::helper('adminhtml')->__('[GLOBAL]');
        } elseif ($attribute->isScopeWebsite()) {
            $html .= '<br/>' . Mage::helper('adminhtml')->__('[WEBSITE]');
        } elseif ($attribute->isScopeStore()) {
            $html .= '<br/>' . Mage::helper('adminhtml')->__('[STORE VIEW]');
        }
        return $html;
    }

    /**
     * Retrieve data object related with form
     *
     * @return Mage_Catalog_Model_Product|Mage_Catalog_Model_Category
     */
    public function getDataObject()
    {
        return $this->getForm()->getDataObject();
    }

    /**
     * Retrieve attribute field name
     *
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return string
     */
    public function getAttributeFieldName($attribute)
    {
        $name = $attribute->getAttributeCode();
        if ($suffix = $this->getForm()->getFieldNameSuffix()) {
            $name = $this->getForm()->addSuffixToName($name, $suffix);
        }
        return $name;
    }

    /**
     * Check readonly attribute
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute|string $attribute
     * @return bool
     */
    public function getAttributeReadonly($attribute)
    {
        if (is_object($attribute)) {
            $attribute = $attribute->getAttributeCode();
        }

        if ($this->getDataObject()->isLockedAttribute($attribute)) {
            return true;
        }

        return false;
    }

    public function toHtml()
    {
        return '<tr><td class="value" colspan="3">' . $this->getElementHtml() . '</td></tr>';
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
