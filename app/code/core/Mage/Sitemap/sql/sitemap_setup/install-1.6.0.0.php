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
 * @package     Mage_Sitemap
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName('sitemap/sitemap', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Google Sitemap');

$installer->getConnection()->createTable($table);

$installer->endSetup();
