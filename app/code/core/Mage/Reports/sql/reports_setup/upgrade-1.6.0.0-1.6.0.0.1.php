<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

$aggregationTables = [
    Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY,
    Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY,
    Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY,
];
$aggregationTableComments = [
    'Most Viewed Products Aggregated Daily',
    'Most Viewed Products Aggregated Monthly',
    'Most Viewed Products Aggregated Yearly',
];

for ($i = 0; $i < 3; ++$i) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable($aggregationTables[$i]))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ], 'Id')
        ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, [
        ], 'Period')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
            'unsigned'  => true,
        ], 'Store Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
        ], 'Product Id')
        ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
            'nullable'  => true,
        ], 'Product Name')
        ->addColumn('product_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
            'nullable'  => false,
            'default'   => '0.0000',
        ], 'Product Price')
        ->addColumn('views_num', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'nullable'  => false,
            'default'   => '0',
        ], 'Number of Views')
        ->addColumn('rating_pos', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ], 'Rating Pos')
        ->addIndex(
            $installer->getIdxName(
                $aggregationTables[$i],
                ['period', 'store_id', 'product_id'],
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
            ),
            ['period', 'store_id', 'product_id'],
            ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
        )
        ->addIndex($installer->getIdxName($aggregationTables[$i], ['store_id']), ['store_id'])
        ->addIndex($installer->getIdxName($aggregationTables[$i], ['product_id']), ['product_id'])
        ->addForeignKey(
            $installer->getFkName($aggregationTables[$i], 'store_id', 'core/store', 'store_id'),
            'store_id',
            $installer->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE,
        )
        ->addForeignKey(
            $installer->getFkName($aggregationTables[$i], 'product_id', 'catalog/product', 'entity_id'),
            'product_id',
            $installer->getTable('catalog/product'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE,
        )
        ->setComment($aggregationTableComments[$i]);
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
