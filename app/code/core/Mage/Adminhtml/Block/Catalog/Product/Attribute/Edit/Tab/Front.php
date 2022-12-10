<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product attribute add/edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
                'label' => Mage::helper('catalog')->__('No')
            ],
            [
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
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
