<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

// Setup data to configure
$frequencies = [
    Mage_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_DAILY,
    Mage_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_MONTHLY,
    Mage_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_YEARLY,
];

$foreignKeys = [
    [
        'name' => 'FK_PRODUCT_ORDERED_AGGREGATED_%s_STORE_ID',
        'column' => 'store_id',
        'refTable' => 'core/store',
        'refColumn' => 'store_id',
    ],
    [
        'name' => 'FK_PRODUCT_ORDERED_AGGREGATED_%s_PRODUCT_ID',
        'column' => 'product_id',
        'refTable' => 'catalog/product',
        'refColumn' => 'entity_id',
    ],
];

/*
 * Alter foreign keys to add 'CASCADE' instead of 'SET_NULL' action
 * Also remove all wrong report records with NULL in 'product_id' field
 */
$connection = $installer->getConnection();
foreach ($frequencies as $frequency) {
    $tableName = $installer->getTable('sales/bestsellers_aggregated_' . $frequency);

    foreach ($foreignKeys as $fkInfo) {
        $connection->addConstraint(
            sprintf($fkInfo['name'], strtoupper($frequency)),
            $tableName,
            $fkInfo['column'],
            $installer->getTable($fkInfo['refTable']),
            $fkInfo['refColumn'],
        );
    }

    $connection->delete($tableName, 'product_id IS NULL');
}
