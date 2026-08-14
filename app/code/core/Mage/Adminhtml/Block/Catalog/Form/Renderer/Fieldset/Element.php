<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Catalog fieldset element renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function _construct()
    {
        $this->setTemplate('catalog/form/renderer/fieldset/element.phtml');
    }

    /**
     * Retrieve data object related with form
     *
     * @return Mage_Catalog_Model_Category|Mage_Catalog_Model_Product
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
        return ($attribute = $this->getAttribute())
            && (
                !$attribute->isScopeGlobal()
                && $this->getDataObject()
                && $this->getDataObject()->getId()
                && $this->getDataObject()->getStoreId()
            );
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
        }

        if ($this->getElement()->getValue() == $defaultValue
            && $this->getDataObject()->getStoreId() != $this->_getDefaultStoreId()) {
            return false;
        }

        if ($defaultValue === false && !$this->getAttribute()->getIsRequired() && $this->getElement()->getValue()) {
            return false;
        }

        return $defaultValue === false;
    }

    /**
     * Check whether the associated attribute is globally-scoped and this
     * field is being rendered for a specific store view (not the
     * default/all-store-views scope).
     *
     * Editing a global attribute from a store view scope writes a
     * store-scoped row for it, corrupting attribute data since a global
     * attribute should never carry a per-store value. This mirrors
     * canDisplayUseDefault() (which already excludes global-scope
     * attributes from the "use default" checkbox), covering the case that
     * mechanism leaves open: the field itself was still editable and
     * savable from a store view.
     *
     * @return bool
     */
    public function isGlobalAttributeOnStoreScope()
    {
        $attribute = $this->getAttribute();
        if ($attribute === null || !$attribute->isScopeGlobal()) {
            return false;
        }

        $dataObject = $this->getDataObject();

        return $dataObject !== null && (bool) $dataObject->getStoreId();
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

        if ($this->isGlobalAttributeOnStoreScope()) {
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
