<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$setup = $installer->getConnection();

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/prefix_show')
    ->where('value!=?', '0');
$showPrefix = (bool) Mage::helper('customer/address')->getConfig('prefix_show')
    || $setup->fetchOne($select) > 0;

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/middlename_show')
    ->where('value!=?', '0');
$showMiddlename = (bool) Mage::helper('customer/address')->getConfig('middlename_show')
    || $setup->fetchOne($select) > 0;

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/suffix_show')
    ->where('value!=?', '0');
$showSuffix = (bool) Mage::helper('customer/address')->getConfig('suffix_show')
    || $setup->fetchOne($select) > 0;

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/dob_show')
    ->where('value!=?', '0');
$showDob = (bool) Mage::helper('customer/address')->getConfig('dob_show')
    || $setup->fetchOne($select) > 0;

$select = $setup->select()
    ->from($installer->getTable('core/config_data'), 'COUNT(*)')
    ->where('path=?', 'customer/address/taxvat_show')
    ->where('value!=?', '0');
$showTaxVat = (bool) Mage::helper('customer/address')->getConfig('taxvat_show')
    || $setup->fetchOne($select) > 0;

/**
 *****************************************************************************
 * customer/account/create/
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), [
    'code'      => 'customer_account_create',
    'label'     => 'customer_account_create',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0,
]);
$formTypeId   = $setup->lastInsertId();
$entityTypeId = $installer->getEntityTypeId('customer');

$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $entityTypeId,
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
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'prefix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'firstname'),
    'sort_order'    => $elementSort++,
]);
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'middlename'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'lastname'),
    'sort_order'    => $elementSort++,
]);
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'suffix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'email'),
    'sort_order'    => $elementSort++,
]);
if ($showDob) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'dob'),
        'sort_order'    => $elementSort++,
    ]);
}
if ($showTaxVat) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'taxvat'),
        'sort_order'    => $elementSort++,
    ]);
}

/**
 *****************************************************************************
 * customer/account/edit/
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), [
    'code'      => 'customer_account_edit',
    'label'     => 'customer_account_edit',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0,
]);
$formTypeId   = $setup->lastInsertId();
$entityTypeId = $installer->getEntityTypeId('customer');

$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $entityTypeId,
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
    'label'       => 'Account Information',
]);

$elementSort = 0;
if ($showPrefix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'prefix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'firstname'),
    'sort_order'    => $elementSort++,
]);
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'middlename'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'lastname'),
    'sort_order'    => $elementSort++,
]);
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'suffix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'email'),
    'sort_order'    => $elementSort++,
]);
if ($showDob) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'dob'),
        'sort_order'    => $elementSort++,
    ]);
}
if ($showTaxVat) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'taxvat'),
        'sort_order'    => $elementSort++,
    ]);
}

/**
 *****************************************************************************
 * customer/address/edit
 *****************************************************************************
 */

$setup->insert($installer->getTable('eav/form_type'), [
    'code'      => 'customer_address_edit',
    'label'     => 'customer_address_edit',
    'is_system' => 1,
    'theme'     => '',
    'store_id'  => 0,
]);
$formTypeId   = $setup->lastInsertId();
$entityTypeId = $installer->getEntityTypeId('customer_address');

$setup->insert($installer->getTable('eav/form_type_entity'), [
    'type_id'        => $formTypeId,
    'entity_type_id' => $entityTypeId,
]);

$setup->insert($installer->getTable('eav/form_fieldset'), [
    'type_id'    => $formTypeId,
    'code'       => 'contact',
    'sort_order' => 1,
]);
$fieldsetId = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_fieldset_label'), [
    'fieldset_id' => $fieldsetId,
    'store_id'    => 0,
    'label'       => 'Contact Information',
]);

$elementSort = 0;
if ($showPrefix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'prefix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'firstname'),
    'sort_order'    => $elementSort++,
]);
if ($showMiddlename) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'middlename'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'lastname'),
    'sort_order'    => $elementSort++,
]);
if ($showSuffix) {
    $setup->insert($installer->getTable('eav/form_element'), [
        'type_id'       => $formTypeId,
        'fieldset_id'   => $fieldsetId,
        'attribute_id'  => $installer->getAttributeId($entityTypeId, 'suffix'),
        'sort_order'    => $elementSort++,
    ]);
}
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'company'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'telephone'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'fax'),
    'sort_order'    => $elementSort++,
]);

$setup->insert($installer->getTable('eav/form_fieldset'), [
    'type_id'    => $formTypeId,
    'code'       => 'address',
    'sort_order' => 2,
]);
$fieldsetId = $setup->lastInsertId();

$setup->insert($installer->getTable('eav/form_fieldset_label'), [
    'fieldset_id' => $fieldsetId,
    'store_id'    => 0,
    'label'       => 'Address',
]);

$elementSort = 0;
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'street'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'city'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'region'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'postcode'),
    'sort_order'    => $elementSort++,
]);
$setup->insert($installer->getTable('eav/form_element'), [
    'type_id'       => $formTypeId,
    'fieldset_id'   => $fieldsetId,
    'attribute_id'  => $installer->getAttributeId($entityTypeId, 'country_id'),
    'sort_order'    => $elementSort++,
]);

$installer->endSetup();
