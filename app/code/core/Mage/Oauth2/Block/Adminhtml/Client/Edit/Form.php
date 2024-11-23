<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

 declare(strict_types=1);

class Mage_Oauth2_Block_Adminhtml_Client_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_model;

    /**
     * Prepares the form for the admin edit client block.
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            [
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
        );

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => $this->__('Client Information'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addType('text', Mage::getConfig()->getBlockClassName('oauth2/adminhtml_text'));

        $fieldset->addField('name', 'text', [
            'label' => $this->__('Client Name'),
            'name' => 'name',
            'required' => true,
            'value' => $this->getModel()->getName(),
        ]);
        $fieldset->addField('secret', 'text', [
            'label' => $this->__('Client Secret'),
            'name' => 'secret',
            'required' => true,
            'disabled' => true,
            'data-copy-text' => $this->getModel()->getSecret(),
            'value' => $this->getModel()->getSecret(),
        ]);

        $fieldset->addField('redirect_uri', 'text', [
            'label' => $this->__('Redirect URI'),
            'name' => 'redirect_uri',
            'required' => true,
            'value' => $this->getModel()->getRedirectUri(),
        ]);
        $fieldset->addField('grant_types', 'multiselect', [
            'label' => $this->__('Grant Types'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'grant_types[]',
            'values' => [
                ['value' => 'authorization_code', 'label' => $this->__('Authorization Code')],
                ['value' => 'refresh_token', 'label' => $this->__('Refresh Token')],
            ],
            'value' => $this->getModel()->getGrantTypes(),
        ]);

        $fieldset->addField('current_password', 'obscure', [
            'name' => 'current_password',
            'label' => $this->__('Current Admin Password'),
            'required' => true
        ]);

        $form->setAction($this->getUrl('*/*/save', ['id' => $this->getModel()->getId()]));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Retrieves the model object from the registry if it is not already set.
     *
     * @return Mage_Oauth2_Model_Client The model object from the registry.
     */
    protected function getModel()
    {
        if (null === $this->_model) {
            $this->_model = Mage::registry('current_oauth2_client');
        }
        return $this->_model;
    }
}
