<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formattribute extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('set_fieldset', ['legend' => Mage::helper('catalog')->__('Add New Attribute')]);

        $fieldset->addField(
            'new_attribute',
            'text',
            [
                'label' => Mage::helper('catalog')->__('Name'),
                'name' => 'new_attribute',
                'required' => true,
            ],
        );

        $fieldset->addField(
            'submit',
            'note',
            [
                'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                            ->setData([
                                'label'     => Mage::helper('catalog')->__('Add Attribute'),
                                'onclick'   => 'this.form.submit();',
                                'class' => 'add',
                            ])
                            ->toHtml(),
            ],
        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $this->setForm($form);
        return $this;
    }
}
