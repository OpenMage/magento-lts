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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$connection = $installer->getConnection()->dropForeignKey(
    $installer->getTable('admin/rule'),
    'FK_ADMIN_RULE'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('admin/role'),
    'PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('admin/role'),
    'TREE_LEVEL'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('admin/rule'),
    'RESOURCE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('admin/rule'),
    'ROLE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('admin/user'),
    'UNQ_ADMIN_USER_USERNAME'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('admin/user') => [
        'columns' => [
            'user_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'User ID'
            ],
            'firstname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'User First Name'
            ],
            'lastname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'User Last Name'
            ],
            'email' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'comment'   => 'User Email'
            ],
            'username' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'User Login'
            ],
            'password' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'comment'   => 'User Password'
            ],
            'created' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'User Created Time'
            ],
            'modified' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'User Modified Time'
            ],
            'logdate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'User Last Login Time'
            ],
            'lognum' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'User Login Number'
            ],
            'reload_acl_flag' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Reload ACL'
            ],
            'is_active' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'User Is Active'
            ],
            'extra' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'User Extra Data'
            ]
        ],
        'comment' => 'Admin User Table'
    ],
    $installer->getTable('admin/role') => [
        'columns' => [
            'role_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Role ID'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Parent Role ID'
            ],
            'tree_level' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role Tree Level'
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role Sort Order'
            ],
            'role_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role Type'
            ],
            'user_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'User ID'
            ],
            'role_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Role Name'
            ]
        ],
        'comment' => 'Admin Role Table'
    ],
    $installer->getTable('admin/rule') => [
        'columns' => [
            'rule_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rule ID'
            ],
            'role_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role ID'
            ],
            'resource_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Resource ID'
            ],
            'privileges' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Privileges'
            ],
            'assert_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Assert ID'
            ],
            'role_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'comment'   => 'Role Type'
            ],
            'permission' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 10,
                'comment'   => 'Permission'
            ]
        ],
        'comment' => 'Admin Rule Table'
    ],
    $installer->getTable('admin/assert') => [
        'columns' => [
            'assert_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Assert ID'
            ],
            'assert_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'comment'   => 'Assert Type'
            ],
            'assert_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Assert Data'
            ]
        ],
        'comment' => 'Admin Assert Table'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('admin/role'),
    $installer->getIdxName('admin/role', ['parent_id', 'sort_order']),
    ['parent_id', 'sort_order']
);

$installer->getConnection()->addIndex(
    $installer->getTable('admin/role'),
    $installer->getIdxName('admin/role', ['tree_level']),
    ['tree_level']
);

$installer->getConnection()->addIndex(
    $installer->getTable('admin/rule'),
    $installer->getIdxName('admin/rule', ['resource_id', 'role_id']),
    ['resource_id', 'role_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('admin/rule'),
    $installer->getIdxName('admin/rule', ['role_id', 'resource_id']),
    ['role_id', 'resource_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('admin/user'),
    $installer->getIdxName(
        'admin/user',
        ['username'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['username'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('admin/rule', 'role_id', 'admin/role', 'role_id'),
    $installer->getTable('admin/rule'),
    'role_id',
    $installer->getTable('admin/role'),
    'role_id'
);

$installer->endSetup();
