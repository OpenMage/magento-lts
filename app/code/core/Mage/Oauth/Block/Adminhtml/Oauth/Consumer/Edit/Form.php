<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth consumer edit form block
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Consumer model
     *
     * @var Mage_Oauth_Model_Consumer
     */
    protected $_model;

    /**
     * Get consumer model
     *
     * @return Mage_Oauth_Model_Consumer
     */
    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = Mage::registry('current_consumer');
        }
        return $this->_model;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();
        $form = new Varien_Data_Form([
            'id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'
        ]);

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => Mage::helper('oauth')->__('Consumer Information'), 'class' => 'fieldset-wide'
        ]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getId()]);
        }
        $fieldset->addField('name', 'text', [
            'name'      => 'name',
            'label'     => Mage::helper('oauth')->__('Name'),
            'title'     => Mage::helper('oauth')->__('Name'),
            'required'  => true,
            'value'     => $model->getName(),
        ]);

        $fieldset->addField('key', 'text', [
            'name'      => 'key',
            'label'     => Mage::helper('oauth')->__('Key'),
            'title'     => Mage::helper('oauth')->__('Key'),
            'disabled'  => true,
            'required'  => true,
            'value'     => $model->getKey(),
        ]);

        $fieldset->addField('secret', 'text', [
            'name'      => 'secret',
            'label'     => Mage::helper('oauth')->__('Secret'),
            'title'     => Mage::helper('oauth')->__('Secret'),
            'disabled'  => true,
            'required'  => true,
            'value'     => $model->getSecret(),
        ]);

        $fieldset->addField('callback_url', 'text', [
            'name'      => 'callback_url',
            'label'     => Mage::helper('oauth')->__('Callback URL'),
            'title'     => Mage::helper('oauth')->__('Callback URL'),
            'required'  => false,
            'value'     => $model->getCallbackUrl(),
            'class'     => 'validate-url',
        ]);

        $fieldset->addField('rejected_callback_url', 'text', [
            'name'      => 'rejected_callback_url',
            'label'     => Mage::helper('oauth')->__('Rejected Callback URL'),
            'title'     => Mage::helper('oauth')->__('Rejected Callback URL'),
            'required'  => false,
            'value'     => $model->getRejectedCallbackUrl(),
            'class'     => 'validate-url',
        ]);

        $fieldset->addField(
            'current_password',
            'obscure',
            [
                'name'  => 'current_password',
                'label' => Mage::helper('oauth')->__('Current Admin Password'),
                'title' => Mage::helper('oauth')->__('Current Admin Password'),
                'required' => true
            ]
        );

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
