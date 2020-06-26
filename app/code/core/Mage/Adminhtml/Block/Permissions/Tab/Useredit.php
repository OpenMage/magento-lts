<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Adminhtml_Block_Permissions_Tab_Useredit extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $user = Mage::registry('user_data');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Account Information')));

        $fieldset->addField('username', 'text',
            array(
                'name'  => 'username',
                'label' => Mage::helper('adminhtml')->__('User Name'),
                'id'    => 'username',
                'title' => Mage::helper('adminhtml')->__('User Name'),
                'class' => 'required-entry',
                'required' => true,
            )
        );

        $fieldset->addField('firstname', 'text',
            array(
                'name'  => 'firstname',
                'label' => Mage::helper('adminhtml')->__('First Name'),
                'id'    => 'firstname',
                'title' => Mage::helper('adminhtml')->__('First Name'),
                'class' => 'required-entry',
                'required' => true,
            )
        );

        $fieldset->addField('lastname', 'text',
            array(
                'name'  => 'lastname',
                'label' => Mage::helper('adminhtml')->__('Last Name'),
                'id'    => 'lastname',
                'title' => Mage::helper('adminhtml')->__('Last Name'),
                'class' => 'required-entry',
                'required' => true,
            )
        );

        $fieldset->addField('user_id', 'hidden',
            array(
                'name'  => 'user_id',
                'id'    => 'user_id',
            )
        );

        $fieldset->addField('email', 'text',
            array(
                'name'  => 'email',
                'label' => Mage::helper('adminhtml')->__('Email'),
                'id'    => 'customer_email',
                'title' => Mage::helper('adminhtml')->__('User Email'),
                'class' => 'required-entry validate-email',
                'required' => true,
            )
        );

        $minPasswordLength = Mage::getModel('customer/customer')->getMinPasswordLength();
        if ($user->getUserId()) {
            $fieldset->addField('password', 'password',
                array(
                    'name'  => 'new_password',
                    'label' => Mage::helper('adminhtml')->__('New Password'),
                    'id'    => 'new_pass',
                    'title' => Mage::helper('adminhtml')->__('New Password'),
                    'class' => 'input-text validate-password min-pass-length-' . $minPasswordLength,
                    'note' => Mage::helper('adminhtml')
                        ->__('Password must be at least of %d characters.', $minPasswordLength),
                )
            );

            $fieldset->addField('confirmation', 'password',
                array(
                    'name'  => 'password_confirmation',
                    'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                    'id'    => 'confirmation',
                    'class' => 'input-text validate-cpassword',
                )
            );
        }
        else {
           $fieldset->addField('password', 'password',
                array(
                    'name'  => 'password',
                    'label' => Mage::helper('adminhtml')->__('Password'),
                    'id'    => 'customer_pass',
                    'title' => Mage::helper('adminhtml')->__('Password'),
                    'class' => 'input-text required-entry validate-password min-pass-length-' . $minPasswordLength,
                    'required' => true,
                    'note' => Mage::helper('adminhtml')
                        ->__('Password must be at least of %d characters.', $minPasswordLength),
                )
            );
           $fieldset->addField('confirmation', 'password',
                array(
                    'name'  => 'password_confirmation',
                    'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                    'id'    => 'confirmation',
                    'title' => Mage::helper('adminhtml')->__('Password Confirmation'),
                    'class' => 'input-text required-entry validate-cpassword',
                    'required' => true,
                )
            );
        }

        $fieldset->addField('is_active', 'select',
            array(
                'name'  	=> 'is_active',
                'label' 	=> Mage::helper('adminhtml')->__('This Account is'),
                'id'    	=> 'is_active',
                'title' 	=> Mage::helper('adminhtml')->__('Account Status'),
                'class' 	=> 'input-select',
                'required' 	=> false,
                'style'		=> 'width: 80px',
                'value'		=> '1',
                'values'	=> array(
                    array(
                        'label' => Mage::helper('adminhtml')->__('Active'),
                        'value'	=> '1',
                    ),
                    array(
                        'label' => Mage::helper('adminhtml')->__('Inactive'),
                        'value' => '0',
                    ),
                ),
            )
        );

        $data = $user->getData();

        unset($data['password']);

        $form->setValues($data);

        $this->setForm($form);
    }

}

