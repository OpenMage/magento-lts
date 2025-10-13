<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
/** @var Mage_Eav_Model_Config $eavConfig */
$eavConfig = Mage::getSingleton('eav/config');

// update customer system attributes used_in_forms data
$attributes = [
    'confirmation'      => [],
    'default_billing'   => [],
    'default_shipping'  => [],
    'password_hash'     => [],
    'website_id'        => ['adminhtml_only' => 1],
    'created_in'        => ['adminhtml_only' => 1],
    'store_id'          => [],
    'group_id'          => ['adminhtml_only' => 1, 'admin_checkout' => 1],
    'prefix'            => [],
    'firstname'         => [],
    'middlename'        => [],
    'lastname'          => [],
    'suffix'            => [],
    'email'             => ['admin_checkout' => 1],
    'dob'               => ['admin_checkout' => 1],
    'taxvat'            => ['admin_checkout' => 1],
    'gender'            => ['admin_checkout' => 1],
];

$defaultUsedInForms = [
    'customer_account_create',
    'customer_account_edit',
    'checkout_register',
];

foreach ($attributes as $attributeCode => $data) {
    $attribute = $eavConfig->getAttribute('customer', $attributeCode);
    if (!$attribute) {
        continue;
    }

    if (($attribute->getData('is_system') == 1 && $attribute->getData('is_visible') == 0) === false) {
        $usedInForms = $defaultUsedInForms;
        if (!empty($data['adminhtml_only'])) {
            $usedInForms = ['adminhtml_customer'];
        } else {
            $usedInForms[] = 'adminhtml_customer';
        }

        if (!empty($data['admin_checkout'])) {
            $usedInForms[] = 'adminhtml_checkout';
        }

        $attribute->setData('used_in_forms', $usedInForms);
    }

    $attribute->save();
}

// update customer address system attributes used_in_forms data
$attributes = [
    'prefix', 'firstname', 'middlename', 'lastname', 'suffix', 'company', 'street', 'city', 'country_id',
    'region', 'region_id', 'postcode', 'telephone', 'fax',
];

$defaultUsedInForms = [
    'adminhtml_customer_address',
    'customer_address_edit',
    'customer_register_address',
];

foreach ($attributes as $attributeCode) {
    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
    if (!$attribute) {
        continue;
    }

    if (($attribute->getData('is_system') == 1 && $attribute->getData('is_visible') == 0) === false) {
        $attribute->setData('used_in_forms', $defaultUsedInForms);
    }

    $attribute->save();
}
