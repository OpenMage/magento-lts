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
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'admin/assert'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('admin/assert'))
    ->addColumn('assert_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Assert ID')
    ->addColumn('assert_type', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
        'nullable'  => true,
        'default'   => null,
    ], 'Assert Type')
    ->addColumn('assert_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Assert Data')
    ->setComment('Admin Assert Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'admin/role'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('admin/role'))
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Role ID')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Parent Role ID')
    ->addColumn('tree_level', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Role Tree Level')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Role Sort Order')
    ->addColumn('role_type', Varien_Db_Ddl_Table::TYPE_TEXT, 1, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Role Type')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'User ID')
    ->addColumn('role_name', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
        'nullable'  => true,
        'default'   => null,
    ], 'Role Name')
    ->addIndex(
        $installer->getIdxName('admin/role', ['parent_id', 'sort_order']),
        ['parent_id', 'sort_order']
    )
    ->addIndex(
        $installer->getIdxName('admin/role', ['tree_level']),
        ['tree_level']
    )
    ->setComment('Admin Role Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'admin/rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('admin/rule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Rule ID')
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Role ID')
    ->addColumn('resource_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Resource ID')
    ->addColumn('privileges', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
        'nullable'  => true,
    ], 'Privileges')
    ->addColumn('assert_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Assert ID')
    ->addColumn('role_type', Varien_Db_Ddl_Table::TYPE_TEXT, 1, [
    ], 'Role Type')
    ->addColumn('permission', Varien_Db_Ddl_Table::TYPE_TEXT, 10, [
    ], 'Permission')
    ->addIndex(
        $installer->getIdxName('admin/rule', ['resource_id', 'role_id']),
        ['resource_id', 'role_id']
    )
    ->addIndex(
        $installer->getIdxName('admin/rule', ['role_id', 'resource_id']),
        ['role_id', 'resource_id']
    )
    ->addForeignKey(
        $installer->getFkName('admin/rule', 'role_id', 'admin/role', 'role_id'),
        'role_id',
        $installer->getTable('admin/role'),
        'role_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Admin Rule Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'admin/user'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('admin/user'))
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'User ID')
    ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => true,
    ], 'User First Name')
    ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => true,
    ], 'User Last Name')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 128, [
        'nullable'  => true,
    ], 'User Email')
    ->addColumn('username', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
        'nullable'  => true,
    ], 'User Login')
    ->addColumn('password', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
        'nullable'  => true,
    ], 'User Password')
    ->addColumn('created', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'User Created Time')
    ->addColumn('modified', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'User Modified Time')
    ->addColumn('logdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'User Last Login Time')
    ->addColumn('lognum', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'User Login Number')
    ->addColumn('reload_acl_flag', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Reload ACL')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '1',
    ], 'User Is Active')
    ->addColumn('extra', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'User Extra Data')
    ->addIndex(
        $installer->getIdxName('admin/user', ['username'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['username'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('Admin User Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
