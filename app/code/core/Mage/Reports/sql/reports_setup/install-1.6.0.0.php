<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
/*
 * Prepare database for tables install
 */
$installer->startSetup();
/**
 * Create table 'reports/event_type'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('reports/event_type'))
    ->addColumn('event_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Event Type Id')
    ->addColumn('event_name', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        ), 'Event Name')
    ->addColumn('customer_login', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Login')
    ->setComment('Reports Event Type Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'reports/event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('reports/event'))
    ->addColumn('event_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Event Id')
    ->addColumn('logged_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Logged At')
    ->addColumn('event_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Event Type Id')
    ->addColumn('object_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Object Id')
    ->addColumn('subject_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Subject Id')
    ->addColumn('subtype', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Subtype')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store Id')
    ->addIndex(
        $installer->getIdxName('reports/event', array('event_type_id')),
        array('event_type_id')
    )
    ->addIndex(
        $installer->getIdxName('reports/event', array('subject_id')),
        array('subject_id')
    )
    ->addIndex(
        $installer->getIdxName('reports/event', array('object_id')),
        array('object_id')
    )
    ->addIndex(
        $installer->getIdxName('reports/event', array('subtype')),
        array('subtype')
    )
    ->addIndex(
        $installer->getIdxName('reports/event', array('store_id')),
        array('store_id')
    )
    ->addForeignKey(
        $installer->getFkName('reports/event', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('reports/event', 'event_type_id', 'reports/event_type', 'event_type_id'),
        'event_type_id',
        $installer->getTable('reports/event_type'),
        'event_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Reports Event Table');
$installer->getConnection()->createTable($table);


/**
 * Create table 'reports/compared_product_index'.
 * MySQL table differs by having unique keys on (customer/visitor, product) columns and is created
 * in separate install.
 */
$tableName = $installer->getTable('reports/compared_product_index');
if (!$installer->tableExists($tableName)) {
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('index_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Index Id')
        ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            ), 'Visitor Id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            ), 'Customer Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Product Id')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            ), 'Store Id')
        ->addColumn('added_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            ), 'Added At')
        ->addIndex(
            $installer->getIdxName('reports/compared_product_index', array('visitor_id', 'product_id')),
            array('visitor_id', 'product_id')
        )
        ->addIndex(
            $installer->getIdxName('reports/compared_product_index', array('customer_id', 'product_id')),
            array('customer_id', 'product_id')
        )
        ->addIndex(
            $installer->getIdxName('reports/compared_product_index', array('store_id')),
            array('store_id')
        )
        ->addIndex(
            $installer->getIdxName('reports/compared_product_index', array('added_at')),
            array('added_at')
        )
        ->addIndex(
            $installer->getIdxName('reports/compared_product_index', array('product_id')),
            array('product_id')
        )
        ->addForeignKey(
            $installer->getFkName('reports/compared_product_index', 'customer_id', 'customer/entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer/entity'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $installer->getFkName('reports/compared_product_index', 'product_id', 'catalog/product', 'entity_id'),
            'product_id',
            $installer->getTable('catalog/product'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $installer->getFkName('reports/compared_product_index', 'store_id', 'core/store', 'store_id'),
            'store_id',
            $installer->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_SET_NULL,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Reports Compared Product Index Table');
    $installer->getConnection()->createTable($table);
}


/**
 * Create table 'reports/viewed_product_index'.
 * MySQL table differs by having unique keys on (customer/visitor, product) columns and is created
 * in separate install.
 */
$tableName = $installer->getTable('reports/viewed_product_index');
if (!$installer->tableExists($tableName)) {
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('index_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Index Id')
        ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            ), 'Visitor Id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            ), 'Customer Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Product Id')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            ), 'Store Id')
        ->addColumn('added_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            ), 'Added At')
        ->addIndex(
            $installer->getIdxName('reports/viewed_product_index', array('visitor_id', 'product_id')),
            array('visitor_id', 'product_id')
        )
        ->addIndex(
            $installer->getIdxName('reports/viewed_product_index', array('customer_id', 'product_id')),
            array('customer_id', 'product_id')
        )
        ->addIndex(
            $installer->getIdxName('reports/viewed_product_index', array('store_id')),
            array('store_id')
        )
        ->addIndex(
            $installer->getIdxName('reports/viewed_product_index', array('added_at')),
            array('added_at')
        )
        ->addIndex(
            $installer->getIdxName('reports/viewed_product_index', array('product_id')),
            array('product_id')
        )
        ->addForeignKey(
            $installer->getFkName('reports/viewed_product_index', 'customer_id', 'customer/entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer/entity'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $installer->getFkName('reports/viewed_product_index', 'product_id', 'catalog/product', 'entity_id'),
            'product_id',
            $installer->getTable('catalog/product'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $installer->getFkName('reports/viewed_product_index', 'store_id', 'core/store', 'store_id'),
            'store_id',
            $installer->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_SET_NULL,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Reports Viewed Product Index Table');
    $installer->getConnection()->createTable($table);
}

/*
 * Prepare database for tables install
 */
$installer->endSetup();
