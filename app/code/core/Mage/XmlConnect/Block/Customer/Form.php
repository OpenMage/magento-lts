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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Form extends Mage_Core_Block_Abstract
{
    /**
     * Render customer form xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_setCustomerFields();
        $this->_prepareFormData();

        $action = Mage::helper('xmlconnect')->getActionUrl($this->getFormAction());

        /** @var Mage_XmlConnect_Model_Simplexml_Form $fromXmlObj */
        $fromXmlObj = Mage::getModel('xmlconnect/simplexml_form', array(
            'xml_id' => 'account_form',
            'action' => $action,
            'use_container' => true
        ));

        $customerFieldset = $fromXmlObj->addFieldset('account_info', array(
            'legend' => $this->__('Account Information')
        ))->setCustomAttributes(array('legend'));

        if ($this->getIsCheckoutRegistrationPage()) {
            $customerFieldset->addField('checkout_page_registration', 'text', array(
                'label' => $this->__('Checkout Page Registration'),
                'value' => true,
                'visible' => 0
            ));
        }

        $this->_addCustomerData($customerFieldset);

        /** Add custom attributes for customer */
        Mage::helper('xmlconnect/customer_form_renderer')->setAttributesBlockName($this->getAttributesBlockName())
            ->setFormCode($this->getCustomerFormCode())->setBlockEntity($this->getCustomer())
            ->addCustomAttributes($customerFieldset, $this->getLayout());

        if ($this->getIsEditPage()) {
            $customerFieldset->addField('change_password', 'checkbox', array('label' => $this->__('Change Password')));

            $customerPasswordFieldset = $fromXmlObj->addFieldset('password_edit', array(
                'legend' => $this->__('Change Password')
            ))->setCustomAttributes(array('legend'));

            $this->_addPasswordFields($customerPasswordFieldset);
        } else {
            $this->_addPasswordFields($customerFieldset);
        }

        return $fromXmlObj->getXml();
    }

    /**
     * Set customers fields for edit form
     *
     * @return Mage_XmlConnect_Block_Customer_Form
     */
    protected function _setCustomerFields()
    {
        $customer = $this->getCustomer();
        if ($this->getIsEditPage() && $customer && $customer->getId()) {
            $this->setFirstname($customer->getFirstname());
            $this->setLastname($customer->getLastname());
            $this->setEmail($customer->getEmail());
        }
        return $this;
    }

    /**
     * Set form data
     *
     * @return Mage_XmlConnect_Block_Customer_Form
     */
    protected function _prepareFormData()
    {
        if ($this->getIsEditPage()) {
            $this->setFormAction('xmlconnect/customer/edit');
            $this->setPasswordLabel($this->__('New Password'));
            $this->setConfirmLabel($this->__('Confirm New Password'));
        } else {
            $this->setFormAction('xmlconnect/customer/save');
            $this->setPasswordLabel($this->__('Password'));
            $this->setConfirmLabel($this->__('Confirm Password'));
            $this->setIsPasswordRequired(true);
        }

        return $this;
    }


    /**
     * Add customer fields - first name, last name and email
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $customerFieldset
     * @return Mage_XmlConnect_Block_Customer_Form
     */
    protected function _addCustomerData(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $customerFieldset)
    {
        $customerFieldset->addField('firstname', 'text', array(
            'label' => $this->__('First Name'),
            'required' => 'true',
            'value' => $this->getFirstname()
        ));
        $customerFieldset->addField('lastname', 'text', array(
            'label' => $this->__('Last Name'),
            'required' => 'true',
            'value' => $this->getLastname()
        ));
        $customerFieldset->addField('email', 'text', array(
            'label' => $this->__('Email'), 'required' => 'true', 'value' => $this->getEmail()
        ))->addValidator()->addRule(array('type' => 'email', 'message' => $this->__('Wrong email format')));

        return $this;
    }

    /**
     * Add password fields
     *
     * Add to form current password, password and password confirmation fields
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $formFieldset
     * @return Mage_XmlConnect_Block_Customer_Form
     */
    protected function _addPasswordFields(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $formFieldset)
    {
        /**
         * Return password confirmation validator in old format
         */
        if ($this->getIsEditPage()) {
            $formFieldset->addField('current_password', 'password', array(
                'label' => $this->__('Current Password'),
                'required' => 'true'
            ));
        }
        $formFieldset->addField('password', 'password', array(
            'label' => $this->getPasswordLabel()
        ) + $this->_getRequiredParam());

        $field = $formFieldset->addField('confirmation', 'password', array(
            'label' => $this->getConfirmLabel()
        ) + $this->_getRequiredParam())->addValidator();

        $field->getXmlObject()->addCustomChild('validator', 'password', array(
            'type' => 'confirmation',
            'message' => $this->__('Regular and confirmation passwords must be equal')
        ));

        return $this;
    }

    /**
     * Get is password required param
     *
     * @return array
     */
    protected function _getRequiredParam()
    {
        if ($this->getIsPasswordRequired()) {
            return array('required' => 'true');
        }
        return array();
    }
}
