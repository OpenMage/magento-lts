<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$connection = $installer->getConnection();

$priceIndexerTables = [
    'bundle/price_indexer_idx',
    'bundle/price_indexer_tmp',
];

$optionsPriceIndexerTables = [
    'bundle/option_indexer_idx',
    'bundle/option_indexer_tmp',
];

$selectionPriceIndexerTables = [
    'bundle/selection_indexer_idx',
    'bundle/selection_indexer_tmp',
];

foreach ($priceIndexerTables as $table) {
    $connection->addColumn($installer->getTable($table), 'group_price', [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Group price',
    ]);
    $connection->addColumn($installer->getTable($table), 'base_group_price', [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Base Group Price',
    ]);
    $connection->addColumn($installer->getTable($table), 'group_price_percent', [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Group Price Percent',
    ]);
}

foreach (array_merge($optionsPriceIndexerTables, $selectionPriceIndexerTables) as $table) {
    $connection->addColumn($installer->getTable($table), 'group_price', [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Group price',
    ]);
}

foreach ($optionsPriceIndexerTables as $table) {
    $connection->addColumn($installer->getTable($table), 'alt_group_price', [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Alt Group Price',
    ]);
}

$applyTo = explode(',', $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'group_price', 'apply_to'));
if (!in_array('bundle', $applyTo)) {
    $applyTo[] = 'bundle';
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'group_price', 'apply_to', implode(',', $applyTo));
}
