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
class Mage_Adminhtml_Block_Permissions_Tab_Useredit extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $user = Mage::registry('user_data');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('adminhtml')->__('Account Information')]);

        $fieldset->addField(
            'username',
            'text',
            [
                'name'  => 'username',
                'label' => Mage::helper('adminhtml')->__('User Name'),
                'id'    => 'username',
                'title' => Mage::helper('adminhtml')->__('User Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'firstname',
            'text',
            [
                'name'  => 'firstname',
                'label' => Mage::helper('adminhtml')->__('First Name'),
                'id'    => 'firstname',
                'title' => Mage::helper('adminhtml')->__('First Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'lastname',
            'text',
            [
                'name'  => 'lastname',
                'label' => Mage::helper('adminhtml')->__('Last Name'),
                'id'    => 'lastname',
                'title' => Mage::helper('adminhtml')->__('Last Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'user_id',
            'hidden',
            [
                'name'  => 'user_id',
                'id'    => 'user_id',
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name'  => 'email',
                'label' => Mage::helper('adminhtml')->__('Email'),
                'id'    => 'customer_email',
                'title' => Mage::helper('adminhtml')->__('User Email'),
                'class' => 'required-entry validate-email',
                'required' => true,
            ]
        );

        $minPasswordLength = Mage::getModel('customer/customer')->getMinPasswordLength();
        if ($user->getUserId()) {
            $fieldset->addField(
                'password',
                'password',
                [
                    'name'  => 'new_password',
                    'label' => Mage::helper('adminhtml')->__('New Password'),
                    'id'    => 'new_pass',
                    'title' => Mage::helper('adminhtml')->__('New Password'),
                    'class' => 'input-text validate-password min-pass-length-' . $minPasswordLength,
                    'note' => Mage::helper('adminhtml')
                        ->__('Password must be at least of %d characters.', $minPasswordLength),
                ]
            );

            $fieldset->addField(
                'confirmation',
                'password',
                [
                    'name'  => 'password_confirmation',
                    'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                    'id'    => 'confirmation',
                    'class' => 'input-text validate-cpassword',
                ]
            );
        } else {
            $fieldset->addField(
                'password',
                'password',
                [
                    'name'  => 'password',
                    'label' => Mage::helper('adminhtml')->__('Password'),
                    'id'    => 'customer_pass',
                    'title' => Mage::helper('adminhtml')->__('Password'),
                    'class' => 'input-text required-entry validate-password min-pass-length-' . $minPasswordLength,
                    'required' => true,
                    'note' => Mage::helper('adminhtml')
                        ->__('Password must be at least of %d characters.', $minPasswordLength),
                ]
            );
            $fieldset->addField(
                'confirmation',
                'password',
                [
                    'name'  => 'password_confirmation',
                    'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                    'id'    => 'confirmation',
                    'title' => Mage::helper('adminhtml')->__('Password Confirmation'),
                    'class' => 'input-text required-entry validate-cpassword',
                    'required' => true,
                ]
            );
        }

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name'      => 'is_active',
                'label'     => Mage::helper('adminhtml')->__('This Account is'),
                'id'        => 'is_active',
                'title'     => Mage::helper('adminhtml')->__('Account Status'),
                'class'     => 'input-select',
                'required'  => false,
                'style'     => 'width: 80px',
                'value'     => '1',
                'values'    => [
                    [
                        'label' => Mage::helper('adminhtml')->__('Active'),
                        'value' => '1',
                    ],
                    [
                        'label' => Mage::helper('adminhtml')->__('Inactive'),
                        'value' => '0',
                    ],
                ],
            ]
        );

        $data = $user->getData();

        unset($data['password']);

        $form->setValues($data);

        $this->setForm($form);
        return $this;
    }
}
