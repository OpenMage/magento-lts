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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page address form renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Onepage_Address_Form extends Mage_Core_Block_Abstract
{
    /**
     * Render customer address form
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected  function _toHtml()
    {
        $addressType = $this->getType() ? $this->getType() : 'billing';
        $isGuest = $this->getIsGuest();

        /** @var $formXmlObj Mage_XmlConnect_Model_Simplexml_Form */
        $formXmlObj = Mage::getModel('xmlconnect/simplexml_form', array(
            'xml_id' => $addressType, 'action' => '', 'use_container' => true
        ))->setFieldNameSuffix($addressType);

        /** @var $formFieldset Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset */
        $formFieldset = $formXmlObj->addFieldset($addressType);

        $formFieldset->addField('firstname', 'text', array(
            'name' => 'firstname', 'label' => $this->__('First Name'), 'required' => '1'
        ));

        $formFieldset->addField('lastname', 'text', array(
            'name' => 'lastname', 'label' => $this->__('Last Name'), 'required' => '1'
        ));

        $formFieldset->addField('company', 'text', array(
            'name' => 'company', 'label' => $this->__('Company'), 'required' => '1'
        ));

        if ($isGuest) {
            $emailField = $formFieldset->addField('email', 'text', array(
                'name' => 'email', 'label' => $this->__('Email Address'), 'required' => '1'
            ));
            $emailValidator = $emailField->addValidator();
            $emailValidator->addRule(array('type' => 'email'));
        }

        $formFieldset->addField('street', 'text', array(
            'name' => 'street[]',
            'label' => $this->__('Address'),
            'required' => '1',
        ));

        $formFieldset->addField('street_2', 'text', array(
            'name' => 'street[]', 'label' => $this->__('Address 2')
        ));

        $formFieldset->addField('city', 'text', array(
            'name' => 'city', 'label' => $this->__('City'), 'required' => '1'
        ));

        $formFieldset->addField('country_id', 'countryListSelect', array(
            'name' => 'country_id', 'label' => $this->__('Country'), 'required' => '1',
        ));

        $formFieldset->addField('postcode', 'text', array(
            'name' => 'postcode', 'label' => $this->__('Zip/Postal Code'), 'required' => '1'
        ));

        $formFieldset->addField('telephone', 'text', array(
            'name' => 'telephone', 'label' => $this->__('Telephone'), 'required' => '1'
        ));

        $formFieldset->addField('fax', 'text', array('name' => 'fax', 'label' => $this->__('Fax')));

        // Add custom address attributes
        Mage::helper('xmlconnect/customer_form_renderer')->setAttributesBlockName('customer_address')
            ->setFormCode('customer_register_address')->setBlockEntity(Mage::getModel('sales/quote_address'))
            ->setBlockEntityType('customer_address')
            ->addCustomAttributes($formFieldset, $this->getLayout(), $addressType);

        $formFieldset->addField('save_in_address_book', 'checkbox', array(
            'name' => 'save_in_address_book', 'label' => $this->__('Save in address book')
        ));

        return $formXmlObj->toXmlObject();
    }
}
