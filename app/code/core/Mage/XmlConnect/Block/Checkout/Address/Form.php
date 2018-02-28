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
 * Customer address form xml renderer for onepage checkout
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Address_Form extends Mage_Core_Block_Abstract
{
    /**
     * Render customer address form xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $addressType = $this->getType() ? $this->getType() : 'billing';

        $action = Mage::helper('xmlconnect')->getActionUrl('xmlconnect/checkout/savebillingaddress');

        /** @var Mage_XmlConnect_Model_Simplexml_Form $fromXmlObj */
        $fromXmlObj = Mage::getModel('xmlconnect/simplexml_form', array(
            'xml_id' => 'address_form', 'action' => $action, 'use_container' => true
        ))->setFieldNameSuffix($addressType);

        $isAllowedGuestCheckout = Mage::getSingleton('checkout/session')->getQuote()->isAllowedGuestCheckout();

        $fromXmlObj->addField('firstname', 'text', array(
            'name' => 'firstname', 'label' => $this->__('First Name'), 'required' => 'true'
        ));

        $fromXmlObj->addField('lastname', 'text', array(
            'name' => 'lastname', 'label' => $this->__('Last Name'), 'required' => 'true'
        ));

        $fromXmlObj->addField('company', 'text', array('name' => 'company', 'label' => $this->__('Company')));

        if ($isAllowedGuestCheckout && !Mage::getSingleton('customer/session')->isLoggedIn()
            && $addressType == 'billing'
        ) {
            $fromXmlObj->addField('email', 'text', array(
                'name' => 'email', 'label' => $this->__('Email Address'), 'required' => 'true'
            ))->addValidator()->addRule(array('type' => 'email', 'message' => 'Wrong email format'));
        }

        $fromXmlObj->addField('street', 'text', array(
            'name' => 'street[]', 'label' => $this->__('Address'), 'required' => 'true'
        ));

        $fromXmlObj->addField('street_2', 'text', array('name' => 'street[]', 'label' => $this->__('Address 2')));

        $fromXmlObj->addField('city', 'text', array(
            'name' => 'city', 'label' => $this->__('City'), 'required' => 'true'
        ));

        $fromXmlObj->addField('country_id', 'countryListSelect', array(
            'name' => 'country_id', 'label' => $this->__('Country'), 'required' => 'true', 'old_format' => true
        ));

        $fromXmlObj->addField('region', 'text', array('name' => 'region', 'label' => $this->__('State/Province')));

        $fromXmlObj->addField('region_id', 'select', array(
            'name' => 'region_id', 'label' => $this->__('State/Province'), 'required' => 'true'
        ));

        $fromXmlObj->addField('postcode', 'text', array(
            'name' => 'postcode', 'label' => $this->__('Zip/Postal Code'), 'required' => 'true'
        ));

        $fromXmlObj->addField('telephone', 'text', array(
            'name' => 'telephone', 'label' => $this->__('Telephone'), 'required' => 'true'
        ));

        $fromXmlObj->addField('fax', 'text', array('name' => 'fax', 'label' => $this->__('Fax')));

        $fromXmlObj->addField('save_in_address_book', 'checkbox', array(
            'name' => 'save_in_address_book','label' => $this->__('Save in address book')
        ));

        // Add custom address attributes
        Mage::helper('xmlconnect/customer_form_renderer')
            ->setAttributesBlockName('customer_form_billing_address_user_defined_attributes')
            ->setFormCode('customer_register_address')->setBlockEntity(Mage::getModel('customer/address'))
            ->addCustomAttributes($fromXmlObj, $this->getLayout(), $addressType);

        return $fromXmlObj->getXml();
    }

    /**
     * Retrieve regions by country
     *
     * @deprecated will delete in the next version
     * @param string $countryId
     * @return array
     */
    protected function _getRegionOptions($countryId)
    {
        $cacheKey = 'DIRECTORY_REGION_SELECT_STORE' . Mage::app()->getStore()->getId() . $countryId;
        $cache = Mage::app()->loadCache($cacheKey);
        if (Mage::app()->useCache('config') && $cache) {
            $options = unserialize($cache);
        } else {
            $collection = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter($countryId)
                ->load();
            $options = $collection->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        return $options;
    }

    /**
     * Retrieve countries
     *
     * @deprecated will delete in the next version
     * @return array
     */
    protected function _getCountryOptions()
    {
        $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
        $cache = Mage::app()->loadCache($cacheKey);
        if (Mage::app()->useCache('config') && $cache) {
            $options = unserialize($cache);
        } else {
            /** @var $collection Mage_Directory_Model_Mysql4_Country_Collection */
            $collection = Mage::getModel('directory/country')->getResourceCollection()->loadByStore();
            $options = $collection->toOptionArray(false);
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        return $options;
    }
}
