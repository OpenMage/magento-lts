<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml edit admin user account form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Account_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        $user = Mage::getModel('admin/user')
            ->load($userId);
        $user->unsetData('password');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('adminhtml')->__('Account Information')]);

        $fieldset->addField('username', 'text', [
            'name' => 'username',
            'label' => Mage::helper('adminhtml')->__('User Name'),
            'title' => Mage::helper('adminhtml')->__('User Name'),
            'required' => true,
        ]);

        $fieldset->addField('firstname', 'text', [
            'name' => 'firstname',
            'label' => Mage::helper('adminhtml')->__('First Name'),
            'title' => Mage::helper('adminhtml')->__('First Name'),
            'required' => true,
        ]);

        $fieldset->addField('lastname', 'text', [
            'name' => 'lastname',
            'label' => Mage::helper('adminhtml')->__('Last Name'),
            'title' => Mage::helper('adminhtml')->__('Last Name'),
            'required' => true,
        ]);

        $fieldset->addField('user_id', 'hidden', [
            'name' => 'user_id',
        ]);

        $fieldset->addField('email', 'text', [
            'name' => 'email',
            'label' => Mage::helper('adminhtml')->__('Email'),
            'title' => Mage::helper('adminhtml')->__('User Email'),
            'required' => true,
        ]);

        $fieldset->addField('current_password', 'obscure', [
            'name' => 'current_password',
            'label' => Mage::helper('adminhtml')->__('Current Admin Password'),
            'title' => Mage::helper('adminhtml')->__('Current Admin Password'),
            'required' => true,
        ]);

        $minAdminPasswordLength = Mage::getModel('admin/user')->getMinAdminPasswordLength();
        $fieldset->addField('password', 'password', [
            'name' => 'new_password',
            'label' => Mage::helper('adminhtml')->__('New Password'),
            'title' => Mage::helper('adminhtml')->__('New Password'),
            'class' => 'input-text validate-admin-password min-admin-pass-length-' . $minAdminPasswordLength,
            'note' => Mage::helper('adminhtml')
                ->__('Password must be at least of %d characters.', $minAdminPasswordLength),
        ]);

        $fieldset->addField('confirmation', 'password', [
            'name' => 'password_confirmation',
            'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
            'class' => 'input-text validate-cpassword',
        ]);

        $locales = Mage::app()->getLocale()->getTranslatedOptionLocales();
        $locales = array_column($locales, 'label', 'value');
        array_unshift($locales, '');
        $fieldset->addField('backend_locale', 'select', [
            'name' => 'backend_locale',
            'label' => Mage::helper('adminhtml')->__('Backend Locale'),
            'class' => 'input-select',
            'options' => $locales,
            'required' => false,
        ]);

        $form->setValues($user->getData());
        $form->setAction($this->getUrl('*/system_account/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
