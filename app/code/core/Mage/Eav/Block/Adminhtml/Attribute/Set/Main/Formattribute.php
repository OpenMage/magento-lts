<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Block_Adminhtml_Attribute_Set_Main_Formattribute extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('set_fieldset', ['legend' => Mage::helper('eav')->__('Add New Attribute')]);

        $fieldset->addField(
            'new_attribute',
            'text',
            [
                'label' => Mage::helper('eav')->__('Name'),
                'name' => 'new_attribute',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'submit',
            'note',
            [
                'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData([
                        'label'     => Mage::helper('eav')->__('Add Attribute'),
                        'onclick'   => 'this.form.submit();',
                        'class' => 'add'
                    ])
                    ->toHtml(),
            ]
        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
