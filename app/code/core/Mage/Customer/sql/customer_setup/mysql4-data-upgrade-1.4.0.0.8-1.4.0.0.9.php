<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $this */
$installer = $this;

/** @var Mage_Customer_Helper_Address $addressHelper */
$addressHelper = Mage::helper('customer/address');

/** @var Mage_Eav_Model_Config $eavConfig */
$eavConfig = Mage::getSingleton('eav/config');

$websites  = Mage::app()->getWebsites(false);
foreach ($websites as $website) {
    $store = $website->getDefaultStore();
    if (!$store) {
        continue;
    }

    // customer attributes
    $attributes = [
        'prefix',
        'middlename',
        'suffix',
        'dob',
        'taxvat',
        'gender',
    ];

    foreach ($attributes as $attributeCode) {
        /** @var Mage_Customer_Model_Attribute $attribute */
        $attribute      = $eavConfig->getAttribute('customer', $attributeCode);
        $configValue    = $addressHelper->getConfig($attributeCode . '_show', $store);
        $isVisible      = $attribute->getData('is_visible');
        $isRequired     = $attribute->getData('is_required');

        if ($configValue == 'opt' || $configValue == '1') {
            $scopeIsVisible     = '1';
            $scopeIsRequired    = '0';
        } elseif ($configValue == 'req') {
            $scopeIsVisible     = '1';
            $scopeIsRequired    = '1';
        } else {
            $scopeIsVisible     = '0';
            $scopeIsRequired    = '0';
        }

        if ($isVisible != $scopeIsVisible || $isRequired != $scopeIsRequired) {
            $attribute->setWebsite($website);
            $attribute->setScopeIsVisible($scopeIsVisible);
            $attribute->setScopeIsRequired($scopeIsRequired);
            $attribute->save();
        }
    }

    // customer address attributes
    $attributes = [
        'prefix',
        'middlename',
        'suffix',
    ];

    foreach ($attributes as $attributeCode) {
        $attribute      = $eavConfig->getAttribute('customer_address', $attributeCode);
        $configValue    = $addressHelper->getConfig($attributeCode . '_show', $store);
        $isVisible      = $attribute->getData('is_visible');
        $isRequired     = $attribute->getData('is_required');

        if ($configValue == 'opt' || $configValue == '1') {
            $scopeIsVisible     = '1';
            $scopeIsRequired    = '0';
        } elseif ($configValue == 'req') {
            $scopeIsVisible     = '1';
            $scopeIsRequired    = '1';
        } else {
            $scopeIsVisible     = '0';
            $scopeIsRequired    = '0';
        }

        if ($isVisible != $scopeIsVisible || $isRequired != $scopeIsRequired) {
            $attribute->setWebsite($website);
            $attribute->setScopeIsVisible($scopeIsVisible);
            $attribute->setScopeIsRequired($scopeIsRequired);
            $attribute->save();
        }
    }

    $attribute = $eavConfig->getAttribute('customer_address', 'street');
    $value     = $addressHelper->getConfig('street_lines', $store);
    if ($attribute->getData('multiline_count') != $value) {
        $attribute->setWebsite($website);
        $attribute->setScopeMultilineCount($value);
        $attribute->save();
    }
}
