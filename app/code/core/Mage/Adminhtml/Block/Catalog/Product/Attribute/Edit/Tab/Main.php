<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Product attribute add/edit form main tab
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    /**
     * Adding product form elements for editing attribute
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        $form = $this->getForm();
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $fieldset->getElements()
            ->searchById('attribute_code')
            ->setData(
                'class',
                'validate-code-event ' . $fieldset->getElements()->searchById('attribute_code')->getData('class'),
            )->setData(
                'note',
                $fieldset->getElements()->searchById('attribute_code')->getData('note')
                . Mage::helper('eav')->__('. Do not use "event" for an attribute code, it is a reserved keyword.'),
            );

        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = [
            [
                'value' => 'price',
                'label' => Mage::helper('catalog')->__('Price'),
            ],
            [
                'value' => 'media_image',
                'label' => Mage::helper('catalog')->__('Media Image'),
            ],
        ];
        if ($attributeObject->getFrontendInput() == 'gallery') {
            $additionalTypes[] = [
                'value' => 'gallery',
                'label' => Mage::helper('catalog')->__('Gallery'),
            ];
        }

        $response = new Varien_Object();
        $response->setTypes([]);
        Mage::dispatchEvent('adminhtml_product_attribute_types', ['response' => $response]);
        $_disabledTypes = [];
        $_hiddenFields = [];
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }

            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }

        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $scopes = [
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE => Mage::helper('catalog')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE => Mage::helper('catalog')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL => Mage::helper('catalog')->__('Global'),
        ];

        if ($attributeObject->getAttributeCode() === 'status'
            || $attributeObject->getAttributeCode() === 'tax_class_id'
        ) {
            unset($scopes[Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE]);
        }

        $fieldset->addField('is_global', 'select', [
            'name'  => 'is_global',
            'label' => Mage::helper('catalog')->__('Scope'),
            'title' => Mage::helper('catalog')->__('Scope'),
            'note'  => Mage::helper('catalog')->__('Declare attribute value saving scope'),
            'values' => $scopes,
        ], 'attribute_code');

        $fieldset->addField('apply_to', 'apply', [
            'name'        => 'apply_to[]',
            'label'       => Mage::helper('catalog')->__('Apply To'),
            'values'      => Mage_Catalog_Model_Product_Type::getOptions(),
            'mode_labels' => [
                'all'     => Mage::helper('catalog')->__('All Product Types'),
                'custom'  => Mage::helper('catalog')->__('Selected Product Types'),
            ],
            'required'    => true,
        ], 'frontend_class');

        $fieldset->addField('is_configurable', 'select', [
            'name' => 'is_configurable',
            'label' => Mage::helper('catalog')->__('Use To Create Configurable Product'),
            'values' => $yesnoSource,
        ], 'apply_to');

        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset', ['legend' => Mage::helper('catalog')->__('Frontend Properties')]);

        $fieldset->addField('is_searchable', 'select', [
            'name'     => 'is_searchable',
            'label'    => Mage::helper('catalog')->__('Use in Quick Search'),
            'title'    => Mage::helper('catalog')->__('Use in Quick Search'),
            'values'   => $yesnoSource,
        ]);

        $fieldset->addField('is_visible_in_advanced_search', 'select', [
            'name' => 'is_visible_in_advanced_search',
            'label' => Mage::helper('catalog')->__('Use in Advanced Search'),
            'title' => Mage::helper('catalog')->__('Use in Advanced Search'),
            'values' => $yesnoSource,
        ]);

        $fieldset->addField('is_comparable', 'select', [
            'name' => 'is_comparable',
            'label' => Mage::helper('catalog')->__('Comparable on Front-end'),
            'title' => Mage::helper('catalog')->__('Comparable on Front-end'),
            'values' => $yesnoSource,
        ]);

        $fieldset->addField('is_filterable', 'select', [
            'name' => 'is_filterable',
            'label' => Mage::helper('catalog')->__('Use In Layered Navigation'),
            'title' => Mage::helper('catalog')->__('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'note' => Mage::helper('catalog')->__('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'values' => [
                ['value' => '0', 'label' => Mage::helper('catalog')->__('No')],
                ['value' => '1', 'label' => Mage::helper('catalog')->__('Filterable (with results)')],
                ['value' => '2', 'label' => Mage::helper('catalog')->__('Filterable (no results)')],
            ],
        ]);

        $fieldset->addField('is_filterable_in_search', 'select', [
            'name' => 'is_filterable_in_search',
            'label' => Mage::helper('catalog')->__('Use In Search Results Layered Navigation'),
            'title' => Mage::helper('catalog')->__('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'note' => Mage::helper('catalog')->__('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'values' => $yesnoSource,
        ]);

        $fieldset->addField('is_used_for_promo_rules', 'select', [
            'name' => 'is_used_for_promo_rules',
            'label' => Mage::helper('catalog')->__('Use for Promo Rule Conditions'),
            'title' => Mage::helper('catalog')->__('Use for Promo Rule Conditions'),
            'values' => $yesnoSource,
        ]);

        $fieldset->addField('position', 'text', [
            'name' => 'position',
            'label' => Mage::helper('catalog')->__('Position'),
            'title' => Mage::helper('catalog')->__('Position in Layered Navigation'),
            'note' => Mage::helper('catalog')->__('Position of attribute in layered navigation block'),
            'class' => 'validate-digits',
        ]);

        $fieldset->addField('is_wysiwyg_enabled', 'select', [
            'name' => 'is_wysiwyg_enabled',
            'label' => Mage::helper('catalog')->__('Enable WYSIWYG'),
            'title' => Mage::helper('catalog')->__('Enable WYSIWYG'),
            'values' => $yesnoSource,
        ]);

        $htmlAllowed = $fieldset->addField('is_html_allowed_on_front', 'select', [
            'name' => 'is_html_allowed_on_front',
            'label' => Mage::helper('catalog')->__('Allow HTML Tags on Frontend'),
            'title' => Mage::helper('catalog')->__('Allow HTML Tags on Frontend'),
            'values' => $yesnoSource,
        ]);
        if (!$attributeObject->getId() || $attributeObject->getIsWysiwygEnabled()) {
            $attributeObject->setIsHtmlAllowedOnFront(1);
        }

        $fieldset->addField('is_visible_on_front', 'select', [
            'name'      => 'is_visible_on_front',
            'label'     => Mage::helper('catalog')->__('Visible on Product View Page on Front-end'),
            'title'     => Mage::helper('catalog')->__('Visible on Product View Page on Front-end'),
            'values'    => $yesnoSource,
        ]);

        $fieldset->addField('used_in_product_listing', 'select', [
            'name'      => 'used_in_product_listing',
            'label'     => Mage::helper('catalog')->__('Used in Product Listing'),
            'title'     => Mage::helper('catalog')->__('Used in Product Listing'),
            'note'      => Mage::helper('catalog')->__('Depends on design theme'),
            'values'    => $yesnoSource,
        ]);
        $fieldset->addField('used_for_sort_by', 'select', [
            'name'      => 'used_for_sort_by',
            'label'     => Mage::helper('catalog')->__('Used for Sorting in Product Listing'),
            'title'     => Mage::helper('catalog')->__('Used for Sorting in Product Listing'),
            'note'      => Mage::helper('catalog')->__('Depends on design theme'),
            'values'    => $yesnoSource,
        ]);

        $form->getElement('apply_to')->setSize(5);

        if ($applyTo = $attributeObject->getApplyTo()) {
            $applyTo = is_array($applyTo) ? $applyTo : explode(',', $applyTo);
            $form->getElement('apply_to')->setValue($applyTo);
        } else {
            $form->getElement('apply_to')->addClass('no-display ignore-validate');
        }

        // define field dependencies
        /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $block */
        $block = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        $this->setChild('form_after', $block
            ->addFieldMap('is_wysiwyg_enabled', 'wysiwyg_enabled')
            ->addFieldMap('is_html_allowed_on_front', 'html_allowed_on_front')
            ->addFieldMap('frontend_input', 'frontend_input_type')
            ->addFieldDependence('wysiwyg_enabled', 'frontend_input_type', 'textarea')
            ->addFieldDependence('html_allowed_on_front', 'wysiwyg_enabled', '0'));

        Mage::dispatchEvent('adminhtml_catalog_product_attribute_edit_prepare_form', [
            'form'      => $form,
            'attribute' => $attributeObject,
        ]);

        return $this;
    }

    /**
     * Retrieve additional element types for product attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return [
            'apply'         => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_apply'),
        ];
    }
}
