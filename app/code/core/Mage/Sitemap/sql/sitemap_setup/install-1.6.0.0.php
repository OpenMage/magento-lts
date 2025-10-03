<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sitemap
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'sitemap'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sitemap/sitemap'))
    ->addColumn('sitemap_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Sitemap Id')
    ->addColumn('sitemap_type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Sitemap Type')
    ->addColumn('sitemap_filename', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Sitemap Filename')
    ->addColumn('sitemap_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Sitemap Path')
    ->addColumn('sitemap_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => true,
    ], 'Sitemap Time')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store id')
    ->addIndex(
        $installer->getIdxName('sitemap/sitemap', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('sitemap/sitemap', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Google Sitemap');

$installer->getConnection()->createTable($table);

$installer->endSetup();
