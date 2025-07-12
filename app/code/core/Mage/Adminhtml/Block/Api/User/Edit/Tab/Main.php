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
class Mage_Adminhtml_Block_Api_User_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('api_user');

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
            'title' => Mage::helper('adminhtml')->__('Current Admin Password'),
            'required' => true,
        ]);

        $minPasswordLength = Mage::getModel('customer/customer')->getMinPasswordLength();
        if ($model->getUserId()) {
            $fieldset->addField('password', 'password', [
                'name'  => 'new_api_key',
                'label' => Mage::helper('adminhtml')->__('New API Key'),
                'id'    => 'new_pass',
                'title' => Mage::helper('adminhtml')->__('New API Key'),
                'class' => 'input-text validate-password min-pass-length-' . $minPasswordLength,
                'note' => Mage::helper('adminhtml')
                    ->__('API Key must be at least of %d characters.', $minPasswordLength),
            ]);

            $fieldset->addField('confirmation', 'password', [
                'name'  => 'api_key_confirmation',
                'label' => Mage::helper('adminhtml')->__('API Key Confirmation'),
                'id'    => 'confirmation',
                'class' => 'input-text validate-cpassword',
            ]);
        } else {
            $fieldset->addField('password', 'password', [
                'name'  => 'api_key',
                'label' => Mage::helper('adminhtml')->__('API Key'),
                'id'    => 'customer_pass',
                'title' => Mage::helper('adminhtml')->__('API Key'),
                'class' => 'input-text required-entry validate-password min-pass-length-' . $minPasswordLength,
                'required' => true,
                'note' => Mage::helper('adminhtml')
                    ->__('API Key must be at least of %d characters.', $minPasswordLength),
            ]);
            $fieldset->addField('confirmation', 'password', [
                'name'  => 'api_key_confirmation',
                'label' => Mage::helper('adminhtml')->__('API Key Confirmation'),
                'id'    => 'confirmation',
                'title' => Mage::helper('adminhtml')->__('API Key Confirmation'),
                'class' => 'input-text required-entry validate-cpassword',
                'required' => true,
            ]);
        }

        if (Mage::getSingleton('admin/session')->getUser()->getId() != $model->getUserId()) {
            $fieldset->addField('is_active', 'select', [
                'name'      => 'is_active',
                'label'     => Mage::helper('adminhtml')->__('This account is'),
                'id'        => 'is_active',
                'title'     => Mage::helper('adminhtml')->__('Account status'),
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
