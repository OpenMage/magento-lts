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
 * Adminhtml permissions user edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Block_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     * @throws Exception
     */
    protected function _prepareForm()
    {
        $block = Mage::getModel('admin/block')->load((int) $this->getRequest()->getParam('block_id'));

        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', ['block_id' => (int) $this->getRequest()->getParam('block_id')]),
            'method' => 'post',
        ]);
        $fieldset = $form->addFieldset(
            'block_details',
            ['legend' => $this->__('Block Details')],
        );

        $fieldset->addField('block_name', 'text', [
            'label' => $this->__('Block Name'),
            'required' => true,
            'name' => 'block_name',
        ]);

        $yesno = [
            [
                'value' => 0,
                'label' => $this->__('No'),
            ],
            [
                'value' => 1,
                'label' => $this->__('Yes'),
            ]];

        $fieldset->addField('is_allowed', 'select', [
            'name' => 'is_allowed',
            'label' => $this->__('Is Allowed'),
            'title' => $this->__('Is Allowed'),
            'values' => $yesno,
        ]);

        $form->setUseContainer(true);
        $form->setValues($block->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
