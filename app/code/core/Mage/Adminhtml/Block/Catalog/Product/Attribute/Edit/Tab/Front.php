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
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Front extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('entity_attribute');

        $form = new Varien_Data_Form(['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('catalog')->__('Frontend Properties')]);

        $yesno = [
            [
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No'),
            ],
            [
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes'),
            ]];

        $fieldset->addField('is_searchable', 'select', [
            'name' => 'is_searchable',
            'label' => Mage::helper('catalog')->__('Use in Quick Search'),
            'title' => Mage::helper('catalog')->__('Use in Quick Search'),
            'values' => $yesno,
        ]);

        $fieldset->addField('is_visible_in_advanced_search', 'select', [
            'name' => 'is_visible_in_advanced_search',
            'label' => Mage::helper('catalog')->__('Use in Advanced Search'),
            'title' => Mage::helper('catalog')->__('Use in Advanced Search'),
            'values' => $yesno,
        ]);

        $fieldset->addField('is_comparable', 'select', [
            'name' => 'is_comparable',
            'label' => Mage::helper('catalog')->__('Comparable on the Frontend'),
            'title' => Mage::helper('catalog')->__('Comparable on the Frontend'),
            'values' => $yesno,
        ]);

        $fieldset->addField('is_filterable', 'select', [
            'name' => 'is_filterable',
            'label' => Mage::helper('catalog')->__("Use in Layered Navigation<br/>(Can be used only with catalog input type 'Dropdown')"),
            'title' => Mage::helper('catalog')->__('Can be used only with catalog input type Dropdown'),
            'values' => [
                ['value' => '0', 'label' => Mage::helper('catalog')->__('No')],
                ['value' => '1', 'label' => Mage::helper('catalog')->__('Filterable (with results)')],
                ['value' => '2', 'label' => Mage::helper('catalog')->__('Filterable (no results)')],
            ],
        ]);

        $fieldset->addField('is_visible_on_front', 'select', [
            'name' => 'is_visible_on_front',
            'label' => Mage::helper('catalog')->__('Visible on Catalog Pages on Front-end'),
            'title' => Mage::helper('catalog')->__('Visible on Catalog Pages on Front-end'),
            'values' => $yesno,
        ]);

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
