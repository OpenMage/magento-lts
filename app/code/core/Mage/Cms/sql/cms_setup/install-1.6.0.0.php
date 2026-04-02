<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

use Mage_Cms_Api_Data_BlockInterface as BlockInterface;
use Mage_Cms_Api_Data_PageInterface as PageInterface;

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'cms/block'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cms/block'))
    ->addColumn(BlockInterface::DATA_ID, Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Block ID')
    ->addColumn(BlockInterface::DATA_TITLE, Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Block Title')
    ->addColumn(BlockInterface::DATA_IDENTIFIER, Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Block String Identifier')
    ->addColumn(BlockInterface::DATA_CONTENT, Varien_Db_Ddl_Table::TYPE_TEXT, '2M', [
    ], 'Block Content')
    ->addColumn(BlockInterface::DATA_CREATION_TIME, Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Block Creation Time')
    ->addColumn(BlockInterface::DATA_UPDATE_TIME, Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Block Modification Time')
    ->addColumn(BlockInterface::DATA_IS_ACTIVE, Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Block Active')
    ->setComment('CMS Block Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'cms/block_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cms/block_store'))
    ->addColumn(BlockInterface::DATA_ID, Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Block ID')
    ->addColumn(BlockInterface::DATA_STORE_ID, Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store ID')
    ->addIndex(
        $installer->getIdxName('cms/block_store', [BlockInterface::DATA_STORE_ID]),
        [BlockInterface::DATA_STORE_ID],
    )
    ->addForeignKey(
        $installer->getFkName(
            'cms/block_store',
            BlockInterface::DATA_ID,
            'cms/block',
            BlockInterface::DATA_ID,
        ),
        BlockInterface::DATA_ID,
        $installer->getTable('cms/block'),
        BlockInterface::DATA_ID,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'cms/block_store',
            BlockInterface::DATA_STORE_ID,
            'core/store',
            BlockInterface::DATA_STORE_ID,
        ),
        BlockInterface::DATA_STORE_ID,
        $installer->getTable('core/store'),
        BlockInterface::DATA_STORE_ID,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('CMS Block To Store Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'cms/page'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cms/page'))
    ->addColumn(PageInterface::DATA_ID, Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Page ID')
    ->addColumn(PageInterface::DATA_TITLE, Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
    ], 'Page Title')
    ->addColumn(PageInterface::DATA_ROOT_TEMPLATE, Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
    ], 'Page Template')
    ->addColumn(PageInterface::DATA_META_KEYWORDS, Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => true,
    ], 'Page Meta Keywords')
    ->addColumn(PageInterface::DATA_META_DESCRIPTION, Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => true,
    ], 'Page Meta Description')
    ->addColumn(PageInterface::DATA_IDENTIFIER, Varien_Db_Ddl_Table::TYPE_TEXT, 100, [
        'nullable'  => true,
        'default'   => null,
    ], 'Page String Identifier')
    ->addColumn(PageInterface::DATA_CONTENT_HEADING, Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
    ], 'Page Content Heading')
    ->addColumn(PageInterface::DATA_CONTENT, Varien_Db_Ddl_Table::TYPE_TEXT, '2M', [
    ], 'Page Content')
    ->addColumn(PageInterface::DATA_CREATION_TIME, Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Page Creation Time')
    ->addColumn(PageInterface::DATA_UPDATE_TIME, Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Page Modification Time')
    ->addColumn(PageInterface::DATA_IS_ACTIVE, Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Page Active')
    ->addColumn(PageInterface::DATA_SORT_ORDER, Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Page Sort Order')
    ->addColumn(PageInterface::DATA_LAYOUT_UPDATE_XML, Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => true,
    ], 'Page Layout Update Content')
    ->addColumn(PageInterface::DATA_CUSTOM_THEME, Varien_Db_Ddl_Table::TYPE_TEXT, 100, [
        'nullable'  => true,
    ], 'Page Custom Theme')
    ->addColumn(PageInterface::DATA_CUSTOM_ROOT_TEMPLATE, Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
    ], 'Page Custom Template')
    ->addColumn(PageInterface::DATA_CUSTOM_LAYOUT_UPDATE_XML, Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => true,
    ], 'Page Custom Layout Update Content')
    ->addColumn(PageInterface::DATA_CUSTOM_THEME_FROM, Varien_Db_Ddl_Table::TYPE_DATE, null, [
        'nullable'  => true,
    ], 'Page Custom Theme Active From Date')
    ->addColumn(PageInterface::DATA_CUSTOM_THEME_TO, Varien_Db_Ddl_Table::TYPE_DATE, null, [
        'nullable'  => true,
    ], 'Page Custom Theme Active To Date')
    ->addIndex(
        $installer->getIdxName('cms/page', [PageInterface::DATA_IDENTIFIER]),
        [PageInterface::DATA_IDENTIFIER],
    )
    ->setComment('CMS Page Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'cms/page_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cms/page_store'))
    ->addColumn(PageInterface::DATA_ID, Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Page ID')
    ->addColumn(PageInterface::DATA_STORE_ID, Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store ID')
    ->addIndex(
        $installer->getIdxName('cms/page_store', [PageInterface::DATA_STORE_ID]),
        [PageInterface::DATA_STORE_ID],
    )
    ->addForeignKey(
        $installer->getFkName('cms/page_store', PageInterface::DATA_ID, 'cms/page', PageInterface::DATA_ID),
        PageInterface::DATA_ID,
        $installer->getTable('cms/page'),
        PageInterface::DATA_ID,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('cms/page_store', PageInterface::DATA_STORE_ID, 'core/store', PageInterface::DATA_STORE_ID),
        PageInterface::DATA_STORE_ID,
        $installer->getTable('core/store'),
        PageInterface::DATA_STORE_ID,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('CMS Page To Store Linkage Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
