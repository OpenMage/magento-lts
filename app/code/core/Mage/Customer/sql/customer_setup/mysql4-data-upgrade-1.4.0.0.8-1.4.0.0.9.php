<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
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
