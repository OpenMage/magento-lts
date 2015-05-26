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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cms page edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Api_User_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('api_user');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('user_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Account Information')));

        if ($model->getUserId()) {
            $fieldset->addField('user_id', 'hidden', array(
                'name' => 'user_id',
            ));
        } else {
            if (! $model->hasData('is_active')) {
                $model->setIsActive(1);
            }
        }

        $fieldset->addField('username', 'text', array(
            'name'  => 'username',
            'label' => Mage::helper('adminhtml')->__('User Name'),
            'id'    => 'username',
            'title' => Mage::helper('adminhtml')->__('User Name'),
            'required' => true,
        ));

        $fieldset->addField('firstname', 'text', array(
            'name'  => 'firstname',
            'label' => Mage::helper('adminhtml')->__('First Name'),
            'id'    => 'firstname',
            'title' => Mage::helper('adminhtml')->__('First Name'),
            'required' => true,
        ));

        $fieldset->addField('lastname', 'text', array(
            'name'  => 'lastname',
            'label' => Mage::helper('adminhtml')->__('Last Name'),
            'id'    => 'lastname',
            'title' => Mage::helper('adminhtml')->__('Last Name'),
            'required' => true,
        ));

        $fieldset->addField('email', 'text', array(
            'name'  => 'email',
            'label' => Mage::helper('adminhtml')->__('Email'),
            'id'    => 'customer_email',
            'title' => Mage::helper('adminhtml')->__('User Email'),
            'class' => 'required-entry validate-email',
            'required' => true,
        ));

        $fieldset->addField('current_password', 'obscure', array(
                'name'  => 'current_password',
                'label' => Mage::helper('adminhtml')->__('Current Admin Password'),
                'title' => Mage::helper('adminhtml')->__('Current Admin Password'),
                'required' => true,
            )
        );

        if ($model->getUserId()) {
            $fieldset->addField('password', 'password', array(
                'name'  => 'new_api_key',
                'label' => Mage::helper('adminhtml')->__('New API Key'),
                'id'    => 'new_pass',
                'title' => Mage::helper('adminhtml')->__('New API Key'),
                'class' => 'input-text validate-password',
            ));

            $fieldset->addField('confirmation', 'password', array(
                'name'  => 'api_key_confirmation',
                'label' => Mage::helper('adminhtml')->__('API Key Confirmation'),
                'id'    => 'confirmation',
                'class' => 'input-text validate-cpassword',
            ));
        }
        else {
           $fieldset->addField('password', 'password', array(
                'name'  => 'api_key',
                'label' => Mage::helper('adminhtml')->__('API Key'),
                'id'    => 'customer_pass',
                'title' => Mage::helper('adminhtml')->__('API Key'),
                'class' => 'input-text required-entry validate-password',
                'required' => true,
            ));
           $fieldset->addField('confirmation', 'password', array(
                'name'  => 'api_key_confirmation',
                'label' => Mage::helper('adminhtml')->__('API Key Confirmation'),
                'id'    => 'confirmation',
                'title' => Mage::helper('adminhtml')->__('API Key Confirmation'),
                'class' => 'input-text required-entry validate-cpassword',
                'required' => true,
            ));
        }

        if (Mage::getSingleton('admin/session')->getUser()->getId() != $model->getUserId()) {
            $fieldset->addField('is_active', 'select', array(
                'name'      => 'is_active',
                'label'     => Mage::helper('adminhtml')->__('This account is'),
                'id'        => 'is_active',
                'title'     => Mage::helper('adminhtml')->__('Account status'),
                'class'     => 'input-select',
                'style'        => 'width: 80px',
                'options'    => array('1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')),
            ));
        }

        $fieldset->addField('user_roles', 'hidden', array(
            'name' => 'user_roles',
            'id'   => '_user_roles',
        ));

        $data = $model->getData();

        unset($data['password']);

        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
