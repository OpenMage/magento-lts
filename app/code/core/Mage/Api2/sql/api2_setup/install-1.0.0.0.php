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
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Api2_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/** @var Varien_Db_Adapter_Pdo_Mysql $adapter */
$adapter = $installer->getConnection();

/**
 * Create table 'api2/acl_role'
 */
$table = $adapter->newTable($installer->getTable('api2/acl_role'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
    ], 'Entity ID')
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        [
            'nullable' => false,
            'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ],
        'Created At'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        [
            'nullable'  => true
        ],
        'Updated At'
    )
    ->addColumn(
        'role_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        ['nullable'  => false],
        'Name of role'
    )
    ->addIndex($installer->getIdxName('api2/acl_role', ['created_at']), ['created_at'])
    ->addIndex($installer->getIdxName('api2/acl_role', ['updated_at']), ['updated_at'])
    ->setComment('Api2 Global ACL Roles');
$adapter->createTable($table);

// Create Guest and Customer User Roles
$adapter->insertMultiple(
    $installer->getTable('api2/acl_role'),
    [
        ['role_name' => 'Guest'],
        ['role_name' => 'Customer']
    ]
);

/**
 * Create table 'api2/acl_user'
 */
$table = $adapter->newTable($installer->getTable('api2/acl_user'))
    ->addColumn('admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => false,
    ], 'Admin ID')
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => false,
    ], 'Role ID')
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('api2/acl_user'),
            ['admin_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['admin_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $installer->getFkName('api2/acl_user', 'admin_id', 'admin/user', 'user_id'),
        'admin_id',
        $installer->getTable('admin/user'),
        'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('api2/acl_user', 'role_id', 'api2/acl_role', 'entity_id'),
        'role_id',
        $installer->getTable('api2/acl_role'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Api2 Global ACL Users');
$adapter->createTable($table);

/**
 * Create table 'api2/acl_rule'
 */
$table = $adapter->newTable($installer->getTable('api2/acl_rule'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'primary'  => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
    ], 'Entity ID')
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => false,
    ], 'Role ID')
    ->addColumn('resource_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
            'nullable' => false
    ], 'Resource ID')
    ->addColumn('privilege', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
            'nullable' => true
    ], 'ACL Privilege')
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('api2/acl_rule'),
            ['role_id', 'resource_id', 'privilege'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['role_id', 'resource_id', 'privilege'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $installer->getFkName('api2/acl_rule', 'role_id', 'api2/acl_role', 'entity_id'),
        'role_id',
        $installer->getTable('api2/acl_role'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Api2 Global ACL Rules');
$adapter->createTable($table);

/**
* Create table 'api2/acl_attribute'
*/
$table = $adapter->newTable($installer->getTable('api2/acl_attribute'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
    ], 'Entity ID')
    ->addColumn('user_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, [
            'nullable' => false
    ], 'Type of user')
    ->addColumn('resource_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
            'nullable' => false
    ], 'Resource ID')
    ->addColumn('operation', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, [
            'nullable' => false
    ], 'Operation')
    ->addColumn('allowed_attributes', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
            'nullable' => true
    ], 'Allowed attributes')
    ->addIndex(
        $installer->getIdxName('api2/acl_attribute', ['user_type']),
        ['user_type']
    )
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('api2/acl_attribute'),
            ['user_type', 'resource_id', 'operation'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['user_type', 'resource_id', 'operation'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('Api2 Filter ACL Attributes');
$adapter->createTable($table);

$installer->endSetup();
