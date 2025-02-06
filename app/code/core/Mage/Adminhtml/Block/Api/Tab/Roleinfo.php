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
class Mage_Adminhtml_Block_Api_Tab_Roleinfo extends Mage_Adminhtml_Block_Widget_Form
{
    public function _beforeToHtml()
    {
        $this->_initForm();

        return parent::_beforeToHtml();
    }

    protected function _initForm()
    {
        $roleId = $this->getRequest()->getParam('rid');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('adminhtml')->__('Role Information')]);

        $fieldset->addField(
            'role_name',
            'text',
            [
                'name'  => 'rolename',
                'label' => Mage::helper('adminhtml')->__('Role Name'),
                'id'    => 'role_name',
                'class' => 'required-entry',
                'required' => true,
            ],
        );

        $fieldset->addField(
            'current_password',
            'obscure',
            [
                'name'  => 'current_password',
                'label' => Mage::helper('adminhtml')->__('Current Admin Password'),
                'title' => Mage::helper('adminhtml')->__('Current Admin Password'),
                'required' => true,
            ],
        );

        $fieldset->addField(
            'role_id',
            'hidden',
            [
                'name'  => 'role_id',
                'id'    => 'role_id',
            ],
        );

        $fieldset->addField(
            'in_role_user',
            'hidden',
            [
                'name'  => 'in_role_user',
                'id'    => 'in_role_userz',
            ],
        );

        $fieldset->addField('in_role_user_old', 'hidden', ['name' => 'in_role_user_old']);

        $form->setValues($this->getRole()->getData());
        $this->setForm($form);
    }
}
