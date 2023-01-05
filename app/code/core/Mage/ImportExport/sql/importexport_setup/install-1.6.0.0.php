<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_ImportExport_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'importexport/importdata'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('importexport/importdata'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('entity', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
        'nullable'  => false,
    ], 'Entity')
    ->addColumn('behavior', Varien_Db_Ddl_Table::TYPE_TEXT, 10, [
        'nullable'  => false,
        'default'   => Mage_ImportExport_Model_Import::BEHAVIOR_APPEND,
    ], 'Behavior')
    ->addColumn('data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'default'   => '',
    ], 'Data')
    ->setComment('Import Data Table');
$installer->getConnection()->createTable($table);

/**
 * Add unique key for parent-child pairs which makes easier configurable products import
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_link'),
    $installer->getIdxName(
        'catalog/product_super_link',
        ['product_id', 'parent_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['product_id', 'parent_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

/**
 * Add unique key for 'catalog/product_super_attribute' table
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute'),
    $installer->getIdxName(
        'catalog/product_super_attribute',
        ['product_id', 'attribute_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['product_id', 'attribute_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

/**
 * Add unique key for 'catalog/product_super_attribute_pricing' table
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    $installer->getIdxName(
        'catalog/product_super_attribute_pricing',
        ['product_super_attribute_id', 'value_index', 'website_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['product_super_attribute_id', 'value_index', 'website_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

/**
 * Add unique key for 'catalog/product_link_attribute_int' table
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_int'),
    $installer->getIdxName(
        'catalog/product_link_attribute_int',
        ['product_link_attribute_id', 'link_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['product_link_attribute_id', 'link_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

/**
 * Add foreign keys for 'catalog/product_link_attribute_int' table
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_link_attribute_int',
        'link_id',
        'catalog/product_link',
        'link_id'
    ),
    $installer->getTable('catalog/product_link_attribute_int'),
    'link_id',
    $installer->getTable('catalog/product_link'),
    'link_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_link_attribute_int',
        'product_link_attribute_id',
        'catalog/product_link_attribute',
        'product_link_attribute_id'
    ),
    $installer->getTable('catalog/product_link_attribute_int'),
    'product_link_attribute_id',
    $installer->getTable('catalog/product_link_attribute'),
    'product_link_attribute_id'
);

$installer->endSetup();
