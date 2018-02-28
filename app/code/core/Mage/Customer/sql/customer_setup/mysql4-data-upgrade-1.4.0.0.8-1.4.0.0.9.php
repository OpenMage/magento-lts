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
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;

/* @var $addressHelper Mage_Customer_Helper_Address */
$addressHelper = Mage::helper('customer/address');

/* @var $eavConfig Mage_Eav_Model_Config */
$eavConfig = Mage::getSingleton('eav/config');

$websites  = Mage::app()->getWebsites(false);
foreach ($websites as $website) {
    /* @var $website Mage_Core_Model_Website */
    $store = $website->getDefaultStore();
    if (!$store) {
        continue;
    }

    // customer attributes
    $attributes = array(
        'prefix',
        'middlename',
        'suffix',
        'dob',
        'taxvat',
        'gender'
    );

    foreach ($attributes as $attributeCode) {
        $attribute      = $eavConfig->getAttribute('customer', $attributeCode);
        $configValue    = $addressHelper->getConfig($attributeCode . '_show', $store);
        $isVisible      = $attribute->getData('is_visible');
        $isRequired     = $attribute->getData('is_required');

        if ($configValue == 'opt' || $configValue == '1') {
            $scopeIsVisible     = '1';
            $scopeIsRequired    = '0';
        } else if ($configValue == 'req') {
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
    $attributes = array(
        'prefix',
        'middlename',
        'suffix',
    );

    foreach ($attributes as $attributeCode) {
        $attribute      = $eavConfig->getAttribute('customer_address', $attributeCode);
        $configValue    = $addressHelper->getConfig($attributeCode . '_show', $store);
        $isVisible      = $attribute->getData('is_visible');
        $isRequired     = $attribute->getData('is_required');

        if ($configValue == 'opt' || $configValue == '1') {
            $scopeIsVisible     = '1';
            $scopeIsRequired    = '0';
        } else if ($configValue == 'req') {
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
