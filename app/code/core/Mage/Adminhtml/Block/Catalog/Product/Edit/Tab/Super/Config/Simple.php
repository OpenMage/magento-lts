<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Quiq simple product creation
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Simple extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
    /**
     * Link to currently editing product
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setFieldNameSuffix('simple_product');
        $form->setDataObject($this->_getProduct());

        $fieldset = $form->addFieldset('simple_product', [
            'legend' => Mage::helper('catalog')->__('Quick simple product creation'),
        ]);
        $this->_addElementTypes($fieldset);
        $attributesConfig = [
            'autogenerate' => ['name', 'sku'],
            'additional'   => ['name', 'sku', 'visibility', 'status'],
        ];

        $availableTypes = ['text', 'select', 'multiselect', 'textarea', 'price', 'weight'];

        $attributes = Mage::getModel('catalog/product')
            ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->setAttributeSetId($this->_getProduct()->getAttributeSetId())
            ->getAttributes();

        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $this->_getProduct()->getTypeInstance(true);
        $usedAttributes = $productType->getUsedProductAttributes($this->_getProduct());

        /* Standard attributes */
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if (($attribute->getIsRequired()
                    && $attribute->getApplyTo()
                    // If not applied to configurable
                    && !in_array(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE, $attribute->getApplyTo())
                    // If not used in configurable
                    && !array_key_exists($attribute->getId(), $usedAttributes))
                // Or in additional
                || in_array($attributeCode, $attributesConfig['additional'])
            ) {
                $inputType = $attribute->getFrontend()->getInputType();
                if (!in_array($inputType, $availableTypes)) {
                    continue;
                }
                $attribute->setAttributeCode('simple_product_' . $attributeCode);
                $element = $fieldset->addField(
                    'simple_product_' . $attributeCode,
                    $inputType,
                    [
                        'label'    => $attribute->getFrontend()->getLabel(),
                        'name'     => $attributeCode,
                        'required' => $attribute->getIsRequired(),
                    ],
                )->setEntityAttribute($attribute);

                if (in_array($attributeCode, $attributesConfig['autogenerate'])) {
                    $element->setDisabled('true');
                    $element->setValue($this->_getProduct()->getData($attributeCode));
                    $element->setAfterElementHtml(
                        '<input type="checkbox" id="simple_product_' . $attributeCode . '_autogenerate" '
                        . 'name="simple_product[' . $attributeCode . '_autogenerate]" value="1" '
                        . 'onclick="toggleValueElements(this, this.parentNode)" checked="checked" /> '
                        . '<label for="simple_product_' . $attributeCode . '_autogenerate" >'
                        . Mage::helper('catalog')->__('Autogenerate')
                        . '</label>',
                    );
                }

                if ($inputType == 'select' || $inputType == 'multiselect') {
                    $element->setValues($attribute->getFrontend()->getSelectOptions());
                }
            }
        }

        /* Configurable attributes */
        foreach ($usedAttributes as $attribute) {
            $attributeCode =  $attribute->getAttributeCode();
            $fieldset->addField('simple_product_' . $attributeCode, 'select', [
                'label' => $attribute->getFrontend()->getLabel(),
                'name'  => $attributeCode,
                'values' => $attribute->getSource()->getAllOptions(true, true),
                'required' => true,
                'class'    => 'validate-configurable',
                'onchange' => 'superProduct.showPricing(this, \'' . $attributeCode . '\')',
            ]);

            $fieldset->addField('simple_product_' . $attributeCode . '_pricing_value', 'hidden', [
                'name' => 'pricing[' . $attributeCode . '][value]',
            ]);

            $fieldset->addField('simple_product_' . $attributeCode . '_pricing_type', 'hidden', [
                'name' => 'pricing[' . $attributeCode . '][is_percent]',
            ]);
        }

        /* Inventory Data */
        $fieldset->addField('simple_product_inventory_qty', 'text', [
            'label' => Mage::helper('catalog')->__('Qty'),
            'name'  => 'stock_data[qty]',
            'class' => 'validate-number',
            'required' => true,
            'value'  => 0,
        ]);

        $fieldset->addField('simple_product_inventory_is_in_stock', 'select', [
            'label' => Mage::helper('catalog')->__('Stock Availability'),
            'name'  => 'stock_data[is_in_stock]',
            'values' => [
                ['value' => 1, 'label' => Mage::helper('catalog')->__('In Stock')],
                ['value' => 0, 'label' => Mage::helper('catalog')->__('Out of Stock')],
            ],
            'value' => 1,
        ]);

        $stockHiddenFields = [
            'use_config_min_qty'            => 1,
            'use_config_min_sale_qty'       => 1,
            'use_config_max_sale_qty'       => 1,
            'use_config_backorders'         => 1,
            'use_config_notify_stock_qty'   => 1,
            'is_qty_decimal'                => 0,
        ];

        foreach ($stockHiddenFields as $fieldName => $fieldValue) {
            $fieldset->addField('simple_product_inventory_' . $fieldName, 'hidden', [
                'name'  => 'stock_data[' . $fieldName . ']',
                'value' => $fieldValue,
            ]);
        }

        $fieldset->addField('create_button', 'note', [
            'text' => $this->getButtonHtml(
                Mage::helper('catalog')->__('Quick Create'),
                'superProduct.quickCreateNewProduct()',
                'save',
            ),
        ]);

        $this->setForm($form);

        return $this;
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('current_product');
        }
        return $this->_product;
    }
}
