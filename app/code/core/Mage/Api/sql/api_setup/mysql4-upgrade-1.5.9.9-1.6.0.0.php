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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
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
$tables = array(
    $installer->getTable('api/assert') => array(
        'columns' => array(
            'assert_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Assert id'
            ),
            'assert_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Assert type'
            ),
            'assert_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Assert additional data'
            )
        ),
        'comment' => 'Api ACL Asserts'
    ),
    $installer->getTable('api/role') => array(
        'columns' => array(
            'role_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Role id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Parent role id'
            ),
            'tree_level' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role level in tree'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort order to display on admin area'
            ),
            'role_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Role type'
            ),
            'user_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'User id'
            ),
            'role_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Role name'
            )
        ),
        'comment' => 'Api ACL Roles'
    ),
    $installer->getTable('api/rule') => array(
        'columns' => array(
            'rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Api rule Id'
            ),
            'role_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Api role Id'
            ),
            'resource_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Module code'
            ),
            'assert_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Assert id'
            ),
            'role_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'comment'   => 'Role type'
            )
        ),
        'comment' => 'Api ACL Rules'
    ),
    $installer->getTable('api/user') => array(
        'columns' => array(
            'user_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'User id'
            ),
            'firstname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'First name'
            ),
            'lastname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Last name'
            ),
            'email' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'comment'   => 'Email'
            ),
            'username' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Nickname'
            ),
            'api_key' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Api key'
            ),
            'created' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'User record create date'
            ),
            'modified' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'User record modify date'
            ),
            'lognum' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Quantity of log ins'
            ),
            'reload_acl_flag' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Refresh ACL flag'
            ),
            'is_active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Account status'
            )
        ),
        'comment' => 'Api Users'
    ),
    $installer->getTable('api/session') => array(
        'columns' => array(
            'user_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'User id'
            ),
            'logdate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Login date'
            ),
            'sessid' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 40,
                'comment'   => 'Sessioin id'
            )
        ),
        'comment' => 'Api Sessions'
    )
);

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->changeColumn(
    $installer->getTable('api/rule'),
    'privileges',
    'api_privileges',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 20,
        'comment'   => 'Privileges'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('api/rule'),
    'permission',
    'api_permission',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 10,
        'comment'   => 'Permission'
    )
);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('api/rule'),
    $installer->getIdxName('api/rule', array('resource_id', 'role_id')),
    array('resource_id', 'role_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->getConnection()->addIndex(
    $installer->getTable('api/rule'),
    $installer->getIdxName('api/rule', array('role_id', 'resource_id')),
    array('role_id', 'resource_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->getConnection()->addIndex(
    $installer->getTable('api/session'),
    $installer->getIdxName('api/session', array('user_id')),
    array('user_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->getConnection()->addIndex(
    $installer->getTable('api/session'),
    $installer->getIdxName('api/session', array('sessid')),
    array('sessid'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->getConnection()->addIndex(
    $installer->getTable('api/role'),
    $installer->getIdxName('api/role', array('parent_id', 'sort_order')),
    array('parent_id', 'sort_order'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->getConnection()->addIndex(
    $installer->getTable('api/role'),
    $installer->getIdxName('api/role', array('tree_level')),
    array('tree_level'),
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
