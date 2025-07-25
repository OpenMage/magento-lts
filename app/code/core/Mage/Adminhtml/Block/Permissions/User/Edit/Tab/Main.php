<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Cms page edit form main tab
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('permissions_user');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('user_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('adminhtml')->__('Account Information')]);

        if ($model->getUserId()) {
            $fieldset->addField('user_id', 'hidden', [
                'name' => 'user_id',
            ]);
        } elseif (!$model->hasData('is_active')) {
            $model->setIsActive(1);
        }

        $fieldset->addField('username', 'text', [
            'name'  => 'username',
            'label' => Mage::helper('adminhtml')->__('User Name'),
            'id'    => 'username',
            'title' => Mage::helper('adminhtml')->__('User Name'),
            'required' => true,
        ]);

        $fieldset->addField('firstname', 'text', [
            'name'  => 'firstname',
            'label' => Mage::helper('adminhtml')->__('First Name'),
            'id'    => 'firstname',
            'title' => Mage::helper('adminhtml')->__('First Name'),
            'required' => true,
        ]);

        $fieldset->addField('lastname', 'text', [
            'name'  => 'lastname',
            'label' => Mage::helper('adminhtml')->__('Last Name'),
            'id'    => 'lastname',
            'title' => Mage::helper('adminhtml')->__('Last Name'),
            'required' => true,
        ]);

        $fieldset->addField('email', 'text', [
            'name'  => 'email',
            'label' => Mage::helper('adminhtml')->__('Email'),
            'id'    => 'customer_email',
            'title' => Mage::helper('adminhtml')->__('User Email'),
            'class' => 'required-entry validate-email',
            'required' => true,
        ]);

        $fieldset->addField('current_password', 'obscure', [
            'name'  => 'current_password',
            'label' => Mage::helper('adminhtml')->__('Current Admin Password'),
            'id'    => 'current_password',
            'title' => Mage::helper('adminhtml')->__('Current Admin Password'),
            'class' => 'input-text',
            'required' => true,
        ]);

        $minAdminPasswordLength = Mage::getModel('admin/user')->getMinAdminPasswordLength();
        if ($model->getUserId()) {
            $fieldset->addField('password', 'password', [
                'name'  => 'new_password',
                'label' => Mage::helper('adminhtml')->__('New Password'),
                'id'    => 'new_pass',
                'title' => Mage::helper('adminhtml')->__('New Password'),
                'class' => 'input-text validate-admin-password min-admin-pass-length-' . $minAdminPasswordLength,
                'note' => Mage::helper('adminhtml')
                    ->__('Password must be at least of %d characters.', $minAdminPasswordLength),
            ]);

            $fieldset->addField('confirmation', 'password', [
                'name'  => 'password_confirmation',
                'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                'id'    => 'confirmation',
                'class' => 'input-text validate-cpassword',
            ]);
        } else {
            $fieldset->addField('password', 'password', [
                'name'  => 'password',
                'label' => Mage::helper('adminhtml')->__('Password'),
                'id'    => 'customer_pass',
                'title' => Mage::helper('adminhtml')->__('Password'),
                'class' => 'input-text required-entry validate-admin-password min-admin-pass-length-'
                    . $minAdminPasswordLength,
                'required' => true,
                'note' => Mage::helper('adminhtml')
                    ->__('Password must be at least of %d characters.', $minAdminPasswordLength),
            ]);
            $fieldset->addField('confirmation', 'password', [
                'name'  => 'password_confirmation',
                'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                'id'    => 'confirmation',
                'title' => Mage::helper('adminhtml')->__('Password Confirmation'),
                'class' => 'input-text required-entry validate-cpassword',
                'required' => true,
            ]);
        }

        if (Mage::getSingleton('admin/session')->getUser()->getId() != $model->getUserId()) {
            $fieldset->addField('is_active', 'select', [
                'name'      => 'is_active',
                'label'     => Mage::helper('adminhtml')->__('This account is'),
                'id'        => 'is_active',
                'title'     => Mage::helper('adminhtml')->__('Account Status'),
                'class'     => 'input-select',
                'style'        => 'width: 80px',
                'options'    => ['1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')],
            ]);
        }

        $fieldset->addField('user_roles', 'hidden', [
            'name' => 'user_roles',
            'id'   => '_user_roles',
        ]);

        $data = $model->getData();

        unset($data['password']);

        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
