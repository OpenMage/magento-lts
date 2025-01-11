<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Checkout_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$setup = $installer->getConnection();

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/prefix_show')
    ->where('value!=?', '0');
$showPrefix = (bool) Mage::helper('customer/address')->getConfig('prefix_show')
    || ($setup->fetchOne($select) > 0);

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/middlename_show')
    ->where('value!=?', '0');
$showMiddlename = (bool) Mage::helper('customer/address')->getConfig('middlename_show')
    || ($setup->fetchOne($select) > 0);

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/suffix_show')
    ->where('value!=?', '0');
$showSuffix = (bool) Mage::helper('customer/address')->getConfig('suffix_show')
    || ($setup->fetchOne($select) > 0);

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/dob_show')
    ->where('value!=?', '0');
$showDob = (bool) Mage::helper('customer/address')->getConfig('dob_show')
    || ($setup->fetchOne($select) > 0);

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/taxvat_show')
    ->where('value!=?', '0');
$showTaxVat = (bool) Mage::helper('customer/address')->getConfig('taxvat_show')
    || ($setup->fetchOne($select) > 0);

$customerEntityTypeId = $installer->getEntityTypeId('customer');
$addressEntityTypeId  = $installer->getEntityTypeId('customer_address');

/**
 *****************************************************************************
 * checkout/onepage/register
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), [
    'code'      => 'checkout_onepage_register',
    'label'     => 'checkout_onepage_register',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0,
]);
$formTypeId   = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $customerEntityTypeId,
]);
$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $addressEntityTypeId,
]);

$elementSort = 0;
if ($showPrefix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'prefix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'firstname'),
    'sort_order'    => $elementSort++,
]);
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'middlename'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'lastname'),
    'sort_order'    => $elementSort++,
]);
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'suffix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'company'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'email'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'street'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'city'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'region'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'postcode'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'country_id'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'telephone'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'fax'),
    'sort_order'    => $elementSort++,
]);
if ($showDob) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'dob'),
        'sort_order'    => $elementSort++,
    ]);
}
if ($showTaxVat) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'taxvat'),
        'sort_order'    => $elementSort++,
    ]);
}

/**
 *****************************************************************************
 * checkout/onepage/register_guest
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), [
    'code'      => 'checkout_onepage_register_guest',
    'label'     => 'checkout_onepage_register_guest',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0,
]);
$formTypeId   = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $customerEntityTypeId,
]);
$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $addressEntityTypeId,
]);

$elementSort = 0;
if ($showPrefix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'prefix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'firstname'),
    'sort_order'    => $elementSort++,
]);
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'middlename'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'lastname'),
    'sort_order'    => $elementSort++,
]);
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'suffix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'company'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'email'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'street'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'city'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'region'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'postcode'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'country_id'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'telephone'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'fax'),
    'sort_order'    => $elementSort++,
]);
if ($showDob) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'dob'),
        'sort_order'    => $elementSort++,
    ]);
}
if ($showTaxVat) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'taxvat'),
        'sort_order'    => $elementSort++,
    ]);
}

/**
 *****************************************************************************
 * checkout/onepage/billing_address
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), [
    'code'      => 'checkout_onepage_billing_address',
    'label'     => 'checkout_onepage_billing_address',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0,
]);
$formTypeId   = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $addressEntityTypeId,
]);

$elementSort = 0;
if ($showPrefix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'prefix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'firstname'),
    'sort_order'    => $elementSort++,
]);
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'middlename'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'lastname'),
    'sort_order'    => $elementSort++,
]);
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'suffix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'company'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'street'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'city'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'region'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'postcode'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'country_id'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'telephone'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'fax'),
    'sort_order'    => $elementSort++,
]);

/**
 *****************************************************************************
 * checkout/onepage/shipping_address
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), [
    'code'      => 'checkout_onepage_shipping_address',
    'label'     => 'checkout_onepage_shipping_address',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0,
]);
$formTypeId   = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $addressEntityTypeId,
]);

$elementSort = 0;
if ($showPrefix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'prefix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'firstname'),
    'sort_order'    => $elementSort++,
]);
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'middlename'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'lastname'),
    'sort_order'    => $elementSort++,
]);
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => null,
        'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'suffix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'company'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'street'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'city'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'region'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'postcode'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'country_id'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'telephone'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => null,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'fax'),
    'sort_order'    => $elementSort++,
]);

/**
 *****************************************************************************
 * checkout/multishipping/register/
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), [
    'code'      => 'checkout_multishipping_register',
    'label'     => 'checkout_multishipping_register',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0,
]);
$formTypeId   = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $customerEntityTypeId,
]);
$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $addressEntityTypeId,
]);

$setup->insert($installer->getTable('eav/form_fieldset'), [
    'type_id'    => $formTypeId,
    'code'       => 'general',
    'sort_order' => 1,
]);
$fieldsetId = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_fieldset_label'), [
    'fieldset_id' => $fieldsetId,
    'store_id'    => 0,
    'label'       => 'Personal Information',
]);

$elementSort = 0;
if ($showPrefix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'prefix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'firstname'),
    'sort_order'    => $elementSort++,
]);
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'middlename'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'lastname'),
    'sort_order'    => $elementSort++,
]);
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'suffix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'email'),
    'sort_order'    => $elementSort++,
]);
if ($showDob) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'dob'),
        'sort_order'    => $elementSort++,
    ]);
}
if ($showTaxVat) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($customerEntityTypeId, 'taxvat'),
        'sort_order'    => $elementSort++,
    ]);
}

$setup->insert($installer->getTable('eav/form_fieldset'), [
    'type_id'    => $formTypeId,
    'code'       => 'address',
    'sort_order' => 2,
]);
$fieldsetId = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_fieldset_label'), [
    'fieldset_id' => $fieldsetId,
    'store_id'    => 0,
    'label'       => 'Address Information',
]);

$elementSort = 0;
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'company'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'telephone'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'street'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'city'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'region'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'postcode'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($addressEntityTypeId, 'country_id'),
    'sort_order'    => $elementSort++,
]);

$installer->endSetup();
