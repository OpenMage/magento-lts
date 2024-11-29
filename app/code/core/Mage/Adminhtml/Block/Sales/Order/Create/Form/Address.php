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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order create address form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Form_Address extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract
{
    /**
     * Customer Address Form instance
     *
     * @var Mage_Customer_Model_Form|null
     */
    protected $_addressForm;

    /**
     * Return Customer Address Collection as array
     * Ignore addresses when the country is not allowed
     *
     * @return array
     */
    public function getAddressCollection()
    {
        $addresses = [];
        $countries = explode(',', Mage::getStoreConfig('general/country/allow', Mage::getSingleton('adminhtml/session_quote')->getStoreId()));

        foreach ($this->getCustomer()->getAddresses() as $address) {
            if (in_array($address->getData('country_id'), $countries)) {
                $addresses[$address->getId()] = $address;
            }
        }

        return $addresses;
    }

    /**
     * Return customer address form instance
     *
     * @return Mage_Customer_Model_Form
     */
    protected function _getAddressForm()
    {
        if (is_null($this->_addressForm)) {
            $this->_addressForm = Mage::getModel('customer/form')
                ->setFormCode('adminhtml_customer_address')
                ->setStore($this->getStore());
        }
        return $this->_addressForm;
    }

    /**
     * Return Customer Address Collection as JSON
     *
     * @return string
     */
    public function getAddressCollectionJson()
    {
        $addressForm = $this->_getAddressForm();
        $data = [];

        $emptyAddress = $this->getCustomer()
            ->getAddressById(null)
            ->setCountryId(Mage::helper('core')->getDefaultCountry($this->getStore()));
        $data[0] = $addressForm->setEntity($emptyAddress)
            ->outputData(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_JSON);

        foreach ($this->getAddressCollection() as $address) {
            $addressForm->setEntity($address);
            $data[$address->getId()] = $addressForm->outputData(
                Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_JSON
            );
        }
        return Mage::helper('core')->jsonEncode($data);
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $fieldset = $this->_form->addFieldset('main', [
            'no_container' => true
        ]);

        /** @var Mage_Customer_Model_Address $addressModel */
        $addressModel = Mage::getModel('customer/address');

        $addressForm = $this->_getAddressForm()
            ->setEntity($addressModel);

        $attributes = $addressForm->getAttributes();
        if (isset($attributes['street'])) {
            Mage::helper('adminhtml/addresses')
                ->processStreetAttribute($attributes['street']);
        }
        $this->_addAttributesToForm($attributes, $fieldset);

        $prefixElement = $this->_form->getElement('prefix');
        if ($prefixElement) {
            /** @var Mage_Customer_Helper_Data $helper */
            $helper = $this->helper('customer');
            $prefixOptions = $helper->getNamePrefixOptions($this->getStore());
            if (!empty($prefixOptions)) {
                $fieldset->removeField($prefixElement->getId());
                $prefixField = $fieldset->addField(
                    $prefixElement->getId(),
                    'select',
                    $prefixElement->getData(),
                    '^'
                );
                $prefixField->setValues($prefixOptions);
                if ($this->getAddressId()) {
                    $prefixField->addElementValues($this->getAddress()->getPrefix());
                }
            }
        }

        $suffixElement = $this->_form->getElement('suffix');
        if ($suffixElement) {
            /** @var Mage_Customer_Helper_Data $helper */
            $helper = $this->helper('customer');
            $suffixOptions = $helper->getNameSuffixOptions($this->getStore());
            if (!empty($suffixOptions)) {
                $fieldset->removeField($suffixElement->getId());
                $suffixField = $fieldset->addField(
                    $suffixElement->getId(),
                    'select',
                    $suffixElement->getData(),
                    $this->_form->getElement('lastname')->getId()
                );
                $suffixField->setValues($suffixOptions);
                if ($this->getAddressId()) {
                    $suffixField->addElementValues($this->getAddress()->getSuffix());
                }
            }
        }

        $regionElement = $this->_form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }

        $this->_form->setValues($this->getFormValues());

        if ($this->_form->getElement('country_id')->getValue()) {
            $countryId = $this->_form->getElement('country_id')->getValue();
            $this->_form->getElement('country_id')->setValue(null);
            foreach ($this->_form->getElement('country_id')->getValues() as $country) {
                if ($country['value'] == $countryId) {
                    $this->_form->getElement('country_id')->setValue($countryId);
                }
            }
        }
        if (is_null($this->_form->getElement('country_id')->getValue())) {
            $this->_form->getElement('country_id')->setValue(
                Mage::helper('core')->getDefaultCountry($this->getStore())
            );
        }

        // Set custom renderer for VAT field if needed
        $vatIdElement = $this->_form->getElement('vat_id');
        if ($vatIdElement && $this->getDisplayVatValidationButton() !== false) {
            $vatIdElement->setRenderer(
                $this->getLayout()->createBlock('adminhtml/customer_sales_order_address_form_renderer_vat')
                    ->setJsVariablePrefix($this->getJsVariablePrefix())
            );
        }

        return $this;
    }

    /**
     * Add additional data to form element
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract
     */
    protected function _addAdditionalFormElementData(Varien_Data_Form_Element_Abstract $element)
    {
        if ($element->getId() === 'region_id') {
            $element->setNoDisplay(true);
        }
        return $this;
    }

    /**
     * Return customer address id
     *
     * @return false
     */
    public function getAddressId()
    {
        return false;
    }

    /**
     * Return customer address formatted as one-line string
     *
     * @param Mage_Customer_Model_Address $address
     * @return string
     */
    public function getAddressAsString($address)
    {
        return $this->escapeHtml($address->format('oneline'));
    }
}
