<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('api/rule'),
    'FK_API_RULE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('api/session'),
    'FK_API_SESSION_USER'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('api/role'),
    'PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('api/role'),
    'TREE_LEVEL'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('api/rule'),
    'RESOURCE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('api/rule'),
    'ROLE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('api/session'),
    'API_SESSION_USER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('api/session'),
    'API_SESSION_SESSID'
);

/*
 * Change columns
 */
$tables = [
    $installer->getTable('api/assert') => [
        'columns' => [
            'assert_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Assert id'
            ],
            'assert_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Assert type'
            ],
            'assert_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Assert additional data'
            ]
        ],
        'comment' => 'Api ACL Asserts'
    ],
    $installer->getTable('api/role') => [
        'columns' => [
            'role_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Role id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Parent role id'
            ],
            'tree_level' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role level in tree'
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort order to display on admin area'
            ],
            'role_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role type'
            ],
            'user_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'User id'
            ],
            'role_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Role name'
            ]
        ],
        'comment' => 'Api ACL Roles'
    ],
    $installer->getTable('api/rule') => [
        'columns' => [
            'rule_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Api rule Id'
            ],
            'role_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Api role Id'
            ],
            'resource_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Module code'
            ],
            'assert_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Assert id'
            ],
            'role_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'comment'   => 'Role type'
            ]
        ],
        'comment' => 'Api ACL Rules'
    ],
    $installer->getTable('api/user') => [
        'columns' => [
            'user_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'User id'
            ],
            'firstname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'First name'
            ],
            'lastname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Last name'
            ],
            'email' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'comment'   => 'Email'
            ],
            'username' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Nickname'
            ],
            'api_key' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Api key'
            ],
            'created' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'User record create date'
            ],
            'modified' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'User record modify date'
            ],
            'lognum' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Quantity of log ins'
            ],
            'reload_acl_flag' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Refresh ACL flag'
            ],
            'is_active' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Account status'
            ]
        ],
        'comment' => 'Api Users'
    ],
    $installer->getTable('api/session') => [
        'columns' => [
            'user_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'User id'
            ],
            'logdate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Login date'
            ],
            'sessid' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Sessioin id'
            ]
        ],
        'comment' => 'Api Sessions'
    ]
];

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->changeColumn(
    $installer->getTable('api/rule'),
    'privileges',
    'api_privileges',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 20,
        'comment'   => 'Privileges'
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('api/rule'),
    'permission',
    'api_permission',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 10,
        'comment'   => 'Permission'
    ]
);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('api/rule'),
    $installer->getIdxName('api/rule', ['resource_id', 'role_id']),
    ['resource_id', 'role_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->getConnection()->addIndex(
    $installer->getTable('api/rule'),
    $installer->getIdxName('api/rule', ['role_id', 'resource_id']),
    ['role_id', 'resource_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->getConnection()->addIndex(
    $installer->getTable('api/session'),
    $installer->getIdxName('api/session', ['user_id']),
    ['user_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->getConnection()->addIndex(
    $installer->getTable('api/session'),
    $installer->getIdxName('api/session', ['sessid']),
    ['sessid'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->getConnection()->addIndex(
    $installer->getTable('api/role'),
    $installer->getIdxName('api/role', ['parent_id', 'sort_order']),
    ['parent_id', 'sort_order'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->getConnection()->addIndex(
    $installer->getTable('api/role'),
    $installer->getIdxName('api/role', ['tree_level']),
    ['tree_level'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('api/rule', 'role_id', 'api/role', 'role_id'),
    $installer->getTable('api/rule'),
    'role_id',
    $installer->getTable('api/role'),
    'role_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('api/session', 'user_id', 'api/user', 'user_id'),
    $installer->getTable('api/session'),
    'user_id',
    $installer->getTable('api/user'),
    'user_id'
);

$installer->endSetup();
