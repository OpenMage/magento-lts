<?php

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
            'legend' => Mage::helper('oauth2')->__('Client Information'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addType('text', Mage::getConfig()->getBlockClassName('oauth2/adminhtml_text'));

        $fieldset->addField('name', 'text', [
            'label' => Mage::helper('oauth2')->__('Client Name'),
            'name' => 'name',
            'required' => true,
            'value' => $this->getModel()->getName(),
        ]);
        $fieldset->addField('secret', 'text', [
            'label' => Mage::helper('oauth2')->__('Client Secret'),
            'name' => 'secret',
            'required' => true,
            'disabled' => true,
            'data-copy-text' => $this->getModel()->getSecret(),
            'value' => $this->getModel()->getSecret(),
        ]);

        $fieldset->addField('redirect_uri', 'text', [
            'label' => Mage::helper('oauth2')->__('Redirect URI'),
            'name' => 'redirect_uri',
            'required' => true,
            'value' => $this->getModel()->getRedirectUri(),
        ]);
        $fieldset->addField('grant_types', 'multiselect', [
            'label' => Mage::helper('oauth2')->__('Grant Types'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'grant_types[]',
            'values' => [
                ['value' => 'authorization_code', 'label' => Mage::helper('oauth2')->__('Authorization Code')],
                ['value' => 'refresh_token', 'label' => Mage::helper('oauth2')->__('Refresh Token')],
            ],
            'value' => $this->getModel()->getGrantTypes(),
        ]);

        $fieldset->addField('current_password', 'obscure', [
            'name' => 'current_password',
            'label' => Mage::helper('oauth')->__('Current Admin Password'),
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
     * @return mixed The model object from the registry.
     */
    protected function getModel()
    {
        if (null === $this->_model) {
            $this->_model = Mage::registry('current_oauth2_client');
        }
        return $this->_model;
    }
}
