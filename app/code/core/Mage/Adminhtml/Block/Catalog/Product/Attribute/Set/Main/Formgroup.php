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
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formgroup extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('set_fieldset', ['legend' => Mage::helper('catalog')->__('Add New Group')]);

        $fieldset->addField(
            'attribute_group_name',
            'text',
            [
                'label' => Mage::helper('catalog')->__('Name'),
                'name' => 'attribute_group_name',
                'required' => true,
            ],
        );

        $fieldset->addField(
            'submit',
            'note',
            [
                'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                            ->setData([
                                'label'     => Mage::helper('catalog')->__('Add Group'),
                                'onclick'   => 'this.form.submit();',
                                'class' => 'add',
                            ])
                            ->toHtml(),
            ],
        );

        $fieldset->addField(
            'attribute_set_id',
            'hidden',
            [
                'name' => 'attribute_set_id',
                'value' => $this->_getSetId(),
            ],
        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/catalog_product_group/save'));
        $this->setForm($form);
        return $this;
    }

    protected function _getSetId()
    {
        return ((int) $this->getRequest()->getParam('id') > 0)
                    ? (int) $this->getRequest()->getParam('id')
                    : Mage::getSingleton('eav/config')->getEntityType(Mage::registry('entityType'))
                        ->getDefaultAttributeSetId();
    }
}
