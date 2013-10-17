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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer saved addresses renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Onepage_Address_List extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * Render customer address list xml
     *
     * @return Mage_XmlConnect_Block_Checkout_Onepage_Address_List
     */
    protected function _toHtml()
    {
        /** @var $listChild Mage_XmlConnect_Model_Simplexml_Element */
        $listChild = $this->getXmlObj();

        $billingAddress = $this->getCustomer()->getPrimaryBillingAddress();
        $shippingAddress = $this->getCustomer()->getPrimaryShippingAddress();

        foreach ($this->getCustomer()->getAddresses() as $address) {
            $childOptions = array();
            if ($billingAddress && ($billingAddress->getEntityId() == $address->getEntityId())) {
                $childOptions['default_billing'] = 1;
            }
            if ($shippingAddress && ($shippingAddress->getEntityId() == $address->getEntityId())) {
                $childOptions['default_shipping'] = 1;
            }
            $addressItem = $listChild->addCustomChild('item', null, $childOptions);
            $this->_prepareAddressData($address, $addressItem);
        }
        return $this;
    }

    /**
     * Collect address data to xml node
     * Remove objects from data array and escape data values
     *
     * @param Mage_Customer_Model_Address $address
     * @param Mage_XmlConnect_Model_Simplexml_Element $item
     * @return Mage_XmlConnect_Block_Checkout_Onepage_Address_List
     */
    protected function _prepareAddressData(
        Mage_Customer_Model_Address $address, Mage_XmlConnect_Model_Simplexml_Element $item
    ) {
        $attributes = Mage::helper('customer/address')->getAttributes();
        $data = array('entity_id' => $address->getId());

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Customer_Model_Attribute */
            if (!$attribute->getIsVisible()) {
                continue;
            }
            if ($attribute->getAttributeCode() == 'country_id') {
                $data['country'] = $address->getCountryModel()->getName();
                $data['country_id'] = $address->getCountryId();
            } else if ($attribute->getAttributeCode() == 'region') {
                $data['region'] = $address->getRegion();
            } else {
                $dataModel = Mage_Customer_Model_Attribute_Data::factory($attribute, $address);
                $attributeValue     = $dataModel->outputValue(
                    Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ONELINE
                );
                if ($attribute->getFrontendInput() == 'multiline') {
                    $values = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ARRAY);
                    // explode lines
                    foreach ($values as $attributeIndex => $attributeVal) {
                        $key = sprintf('%s%d', $attribute->getAttributeCode(), $attributeIndex + 1);
                        $data[$key] = $attributeVal;
                    }
                }
                $data[$attribute->getAttributeCode()] = $attributeValue;
            }
        }

        foreach ($data as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $item->addCustomChild($key, $value);
        }
        return $this;
    }
}
