<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('core/variable_value'), 'plain_value', 'TEXT NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('core/variable_value'), 'html_value', 'TEXT NOT NULL');

$select = $installer->getConnection()->select()
    ->from(['main_table' => $installer->getTable('core/variable')], [])
    ->join(
        ['value_table' => $installer->getTable('core/variable_value')],
        'value_table.variable_id = main_table.variable_id',
        [],
    )
    ->columns(['main_table.variable_id', 'main_table.is_html', 'value_table.value']);

$data = [];
foreach ($installer->getConnection()->fetchAll($select) as $row) {
    if ($row['is_html']) {
        $value = ['html_value' => $row['value']];
    } else {
        $value = ['plain_value' => $row['value']];
    }
    $data[$row['variable_id']] = $value;
}

foreach ($data as $variableId => $value) {
    $installer->getConnection()->update(
        $installer->getTable('core/variable_value'),
        $value,
        ['variable_id = ?' => $variableId],
    );
}

$installer->getConnection()->dropColumn($installer->getTable('core/variable'), 'is_html');
$installer->getConnection()->dropColumn($installer->getTable('core/variable_value'), 'value');

$installer->endSetup();
