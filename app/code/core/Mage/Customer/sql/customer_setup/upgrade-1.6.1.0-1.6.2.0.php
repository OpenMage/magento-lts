<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;

$disableAGCAttributeCode = 'disable_auto_group_change';

$installer->addAttribute('customer', $disableAGCAttributeCode, [
    'type'      => 'static',
    'label'     => 'Disable Automatic Group Change Based on VAT ID',
    'input'     => 'boolean',
    'backend'   => 'customer/attribute_backend_data_boolean',
    'position'  => 28,
    'required'  => false
]);

$disableAGCAttribute = Mage::getSingleton('eav/config')
    ->getAttribute('customer', $disableAGCAttributeCode);
$disableAGCAttribute->setData('used_in_forms', [
    'adminhtml_customer'
]);
$disableAGCAttribute->save();

$attributesInfo = [
    'vat_id' => [
        'label'     => 'VAT number',
        'type'      => 'varchar',
        'input'     => 'text',
        'position'  => 140,
        'visible'   => true,
        'required'  => false
    ],
    'vat_is_valid' => [
        'label'     => 'VAT number validity',
        'visible'   => false,
        'required'  => false,
        'type'      => 'int'
    ],
    'vat_request_id' => [
        'label'     => 'VAT number validation request ID',
        'type'      => 'varchar',
        'visible'   => false,
        'required'  => false
    ],
    'vat_request_date' => [
        'label'     => 'VAT number validation request date',
        'type'      => 'varchar',
        'visible'   => false,
        'required'  => false
    ],
    'vat_request_success' => [
        'label'     => 'VAT number validation request success',
        'visible'   => false,
        'required'  => false,
        'type'      => 'int'
    ]
];

foreach ($attributesInfo as $attributeCode => $attributeParams) {
    $installer->addAttribute('customer_address', $attributeCode, $attributeParams);
}

$vatAttribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'vat_id');
$vatAttribute->setData('used_in_forms', [
     'adminhtml_customer_address',
     'customer_address_edit',
     'customer_register_address'
]);
$vatAttribute->save();
