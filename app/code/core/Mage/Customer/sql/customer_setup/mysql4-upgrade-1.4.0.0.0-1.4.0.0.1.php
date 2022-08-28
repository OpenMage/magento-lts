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

$installer = $this;

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer->startSetup();

$installer->addAttribute('customer', 'gender', [
    'label'        => 'Gender',
    'visible'      => true,
    'required'     => false,
    'type'         => 'int',
    'input'        => 'select',
    'source'        => 'eav/entity_attribute_source_table',
]);

$tableOptions        = $installer->getTable('eav_attribute_option');
$tableOptionValues   = $installer->getTable('eav_attribute_option_value');

// add options for level of politeness
$attributeId = (int)$installer->getAttribute('customer', 'gender', 'attribute_id');
foreach (['Male', 'Female'] as $sortOrder => $label) {
    // add option
    $data = [
        'attribute_id' => $attributeId,
        'sort_order'   => $sortOrder,
    ];
    $installer->getConnection()->insert($tableOptions, $data);

    // add option label
    $optionId = (int)$installer->getConnection()->lastInsertId($tableOptions, 'option_id');
    $data = [
        'option_id' => $optionId,
        'store_id'  => 0,
        'value'     => $label,
    ];
    $installer->getConnection()->insert($tableOptionValues, $data);
}

$installer->endSetup();
