<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'api/assert'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('api/assert'))
    ->addColumn('assert_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Assert id')
    ->addColumn('assert_type', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
    ], 'Assert type')
    ->addColumn('assert_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Assert additional data')
    ->setComment('Api ACL Asserts');
$installer->getConnection()->createTable($table);

/**
 * Create table 'api/role'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('api/role'))
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Role id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Parent role id')
    ->addColumn('tree_level', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Role level in tree')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort order to display on admin area')
    ->addColumn('role_type', Varien_Db_Ddl_Table::TYPE_TEXT, 1, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Role type')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'User id')
    ->addColumn('role_name', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Role name')
    ->addIndex(
        $installer->getIdxName('api/role', ['parent_id', 'sort_order']),
        ['parent_id', 'sort_order'],
    )
    ->addIndex(
        $installer->getIdxName('api/role', ['tree_level']),
        ['tree_level'],
    )
    ->setComment('Api ACL Roles');
$installer->getConnection()->createTable($table);

/**
 * Create table 'api/rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('api/rule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Api rule Id')
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Api role Id')
    ->addColumn('resource_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Module code')
    ->addColumn('api_privileges', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
    ], 'Privileges')
    ->addColumn('assert_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Assert id')
    ->addColumn('role_type', Varien_Db_Ddl_Table::TYPE_TEXT, 1, [
    ], 'Role type')
    ->addColumn('api_permission', Varien_Db_Ddl_Table::TYPE_TEXT, 10, [
    ], 'Permission')
    ->addIndex(
        $installer->getIdxName('api/rule', ['resource_id', 'role_id']),
        ['resource_id', 'role_id'],
    )
    ->addIndex(
        $installer->getIdxName('api/rule', ['role_id', 'resource_id']),
        ['role_id', 'resource_id'],
    )
    ->addForeignKey(
        $installer->getFkName('api/rule', 'role_id', 'api/role', 'role_id'),
        'role_id',
        $installer->getTable('api/role'),
        'role_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Api ACL Rules');
$installer->getConnection()->createTable($table);

/**
 * Create table 'api/user'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('api/user'))
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'User id')
    ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'First name')
    ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Last name')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 128, [
    ], 'Email')
    ->addColumn('username', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
    ], 'Nickname')
    ->addColumn('api_key', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
    ], 'Api key')
    ->addColumn('created', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'User record create date')
    ->addColumn('modified', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'User record modify date')
    ->addColumn('lognum', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Quantity of log ins')
    ->addColumn('reload_acl_flag', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Refresh ACL flag')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '1',
    ], 'Account status')
    ->setComment('Api Users');
$installer->getConnection()->createTable($table);

/**
 * Create table 'api/session'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('api/session'))
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'User id')
    ->addColumn('logdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Login date')
    ->addColumn('sessid', Varien_Db_Ddl_Table::TYPE_TEXT, 40, [
    ], 'Sessioin id')
    ->addIndex(
        $installer->getIdxName('api/session', ['user_id']),
        ['user_id'],
    )
    ->addIndex(
        $installer->getIdxName('api/session', ['sessid']),
        ['sessid'],
    )
    ->addForeignKey(
        $installer->getFkName('api/session', 'user_id', 'api/user', 'user_id'),
        'user_id',
        $installer->getTable('api/user'),
        'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Api Sessions');
$installer->getConnection()->createTable($table);

$installer->endSetup();
