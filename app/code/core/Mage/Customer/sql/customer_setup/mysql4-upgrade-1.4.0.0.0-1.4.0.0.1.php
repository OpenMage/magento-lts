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
$attributeId = (int) $installer->getAttribute('customer', 'gender', 'attribute_id');
foreach (['Male', 'Female'] as $sortOrder => $label) {
    // add option
    $data = [
        'attribute_id' => $attributeId,
        'sort_order'   => $sortOrder,
    ];
    $installer->getConnection()->insert($tableOptions, $data);

    // add option label
    $optionId = (int) $installer->getConnection()->lastInsertId($tableOptions, 'option_id');
    $data = [
        'option_id' => $optionId,
        'store_id'  => 0,
        'value'     => $label,
    ];
    $installer->getConnection()->insert($tableOptionValues, $data);
}

$installer->endSetup();
