<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;

/** @var Mage_Customer_Helper_Address $addressHelper */
$addressHelper = Mage::helper('customer/address');
$store         = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);

/** @var Mage_Eav_Model_Config $eavConfig */
$eavConfig = Mage::getSingleton('eav/config');

// update customer system attributes data
$attributes = [
    'confirmation'      => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 0,
        'sort_order'        => 0,
    ],
    'default_billing'   => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 0,
        'sort_order'        => 0,
    ],
    'default_shipping'  => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 0,
        'sort_order'        => 0,
    ],
    'password_hash'     => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 0,
        'sort_order'        => 0,
    ],
    'website_id'        => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 10,
        'adminhtml_only'    => 1,
    ],
    'created_in'        => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 20,
        'is_required'       => 0,
        'adminhtml_only'    => 1,
    ],
    'store_id'          => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 0,
        'sort_order'        => 0,
    ],
    'group_id'          => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 25,
        'adminhtml_only'    => 1,
        'admin_checkout'    => 1,
    ],
    'prefix'            => [
        'is_user_defined'   => 0,
        'is_system'         => 0,
        'is_visible'        => $addressHelper->getConfig('prefix_show', $store) == '' ? 0 : 1,
        'sort_order'        => 30,
        'is_required'       => $addressHelper->getConfig('prefix_show', $store) == 'req' ? 1 : 0,
    ],
    'firstname'         => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 40,
        'is_required'       => 1,
        'validate_rules'    => [
            'max_text_length'   => 255,
            'min_text_length'   => 1,
        ],
    ],
    'middlename'        => [
        'is_user_defined'   => 0,
        'is_system'         => 0,
        'is_visible'        => $addressHelper->getConfig('middlename_show', $store) == '' ? 0 : 1,
        'sort_order'        => 50,
        'is_required'       => $addressHelper->getConfig('middlename_show', $store) == 'req' ? 1 : 0,
    ],
    'lastname'          => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 60,
        'is_required'       => 1,
        'validate_rules'    => [
            'max_text_length'   => 255,
            'min_text_length'   => 1,
        ],
    ],
    'suffix'            => [
        'is_user_defined'   => 0,
        'is_system'         => 0,
        'is_visible'        => $addressHelper->getConfig('suffix_show', $store) == '' ? 0 : 1,
        'sort_order'        => 70,
        'is_required'       => $addressHelper->getConfig('suffix_show', $store) == 'req' ? 1 : 0,
    ],
    'email'             => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 80,
        'is_required'       => 1,
        'validate_rules'    => [
            'input_validation'  => 'email',
        ],
        'admin_checkout'    => 1,
    ],
    'dob'               => [
        'is_user_defined'   => 0,
        'is_system'         => 0,
        'is_visible'        => $addressHelper->getConfig('dob_show', $store) == '' ? 0 : 1,
        'sort_order'        => 90,
        'is_required'       => $addressHelper->getConfig('dob_show', $store) == 'req' ? 1 : 0,
        'validate_rules'    => [
            'input_validation'  => 'date',
        ],
        'input_filter'      => 'date',
        'admin_checkout'    => 1,
    ],
    'taxvat'            => [
        'is_user_defined'   => 0,
        'is_system'         => 0,
        'is_visible'        => $addressHelper->getConfig('dob_show', $store) == '' ? 0 : 1,
        'sort_order'        => 100,
        'is_required'       => $addressHelper->getConfig('dob_show', $store) == 'req' ? 1 : 0,
        'validate_rules'    => [
            'max_text_length'   => 255,
        ],
        'admin_checkout'    => 1,
    ],
    'gender'            => [
        'is_user_defined'   => 0,
        'is_system'         => 0,
        'is_visible'        => $addressHelper->getConfig('gender_show', $store) == '' ? 0 : 1,
        'sort_order'        => 110,
        'is_required'       => $addressHelper->getConfig('gender_show', $store) == 'req' ? 1 : 0,
        'validate_rules'    => [],
        'admin_checkout'    => 1,
    ],
];

foreach ($attributes as $attributeCode => $data) {
    /** @var Mage_Customer_Model_Attribute $attribute */
    $attribute = $eavConfig->getAttribute('customer', $attributeCode);
    $website = $store->getWebsite();
    if ($website !== false) {
        $attribute->setWebsite($website);
    }

    $attribute->addData($data);
    if (($data['is_system'] == 1 && $data['is_visible'] == 0) === false) {
        $usedInForms = [
            'customer_account_create',
            'customer_account_edit',
            'checkout_register',
        ];
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

// update customer address system attributes data
$attributes = [
    'prefix'            => [
        'is_user_defined'   => 0,
        'is_system'         => 0,
        'is_visible'        => $addressHelper->getConfig('prefix_show', $store) == '' ? 0 : 1,
        'sort_order'        => 10,
        'is_required'       => $addressHelper->getConfig('prefix_show', $store) == 'req' ? 1 : 0,
    ],
    'firstname'         => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 20,
        'is_required'       => 1,
        'validate_rules'    => [
            'max_text_length'   => 255,
            'min_text_length'   => 1,
        ],
    ],
    'middlename'        => [
        'is_user_defined'   => 0,
        'is_system'         => 0,
        'is_visible'        => $addressHelper->getConfig('middlename_show', $store) == '' ? 0 : 1,
        'sort_order'        => 30,
        'is_required'       => $addressHelper->getConfig('middlename_show', $store) == 'req' ? 1 : 0,
    ],
    'lastname'          => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 40,
        'is_required'       => 1,
        'validate_rules'    => [
            'max_text_length'   => 255,
            'min_text_length'   => 1,
        ],
    ],
    'suffix'            => [
        'is_user_defined'   => 0,
        'is_system'         => 0,
        'is_visible'        => $addressHelper->getConfig('suffix_show', $store) == '' ? 0 : 1,
        'sort_order'        => 50,
        'is_required'       => $addressHelper->getConfig('suffix_show', $store) == 'req' ? 1 : 0,
    ],
    'company'           => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 60,
        'is_required'       => 0,
        'validate_rules'    => [
            'max_text_length'   => 255,
            'min_text_length'   => 1,
        ],
    ],
    'street'           => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 70,
        'multiline_count'   => $addressHelper->getConfig('street_lines', $store),
        'is_required'       => 1,
        'validate_rules'    => [
            'max_text_length'   => 255,
            'min_text_length'   => 1,
        ],
    ],
    'city'              => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 80,
        'is_required'       => 1,
        'validate_rules'    => [
            'max_text_length'   => 255,
            'min_text_length'   => 1,
        ],
    ],
    'country_id'        => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 90,
        'is_required'       => 1,
    ],
    'region'            => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 100,
        'is_required'       => 0,
    ],
    'region_id'         => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 100,
        'is_required'       => 0,
    ],
    'postcode'          => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 110,
        'is_required'       => 1,
        'validate_rules'    => [
        ],
    ],
    'telephone'         => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 120,
        'is_required'       => 1,
        'validate_rules'    => [
            'max_text_length'   => 255,
            'min_text_length'   => 1,
        ],
    ],
    'fax'               => [
        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 130,
        'is_required'       => 0,
        'validate_rules'    => [
            'max_text_length'   => 255,
            'min_text_length'   => 1,
        ],
    ],
];

foreach ($attributes as $attributeCode => $data) {
    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
    $website = $store->getWebsite();
    if ($website !== false) {
        $attribute->setWebsite($website);
    }

    $attribute->addData($data);
    if (($data['is_system'] == 1 && $data['is_visible'] == 0) === false) {
        $usedInForms = [
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address',
        ];
        $attribute->setData('used_in_forms', $usedInForms);
    }

    $attribute->save();
}
