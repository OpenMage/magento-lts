
$tables = array(
    
    'api_assert' => array(
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

    'api_role' => array(
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

    'api_rule' => array(
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
            'api_privileges' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Privileges'
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
            ),
            'api_permission' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 10,
                'comment'   => 'Permission'
            )
        ),
        'comment' => 'Api ACL Rules'
    ),

    'api_user' => array(
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
                'length'    => 100,
                'comment'   => 'Api key'
            ),
            'created' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'default'   => 'CURRENT_TIMESTAMP',
                'comment'   => 'User record create date'
            ),
            'modified' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
,
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

    'api_session' => array(
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
                'default'   => 'CURRENT_TIMESTAMP',
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

foreach ($tables as $table => $tableData) {
    foreach ($tableData['columns'] as $column =>$columnDefinition) {
        $installer->getConnection()->modifyColumn($installer->getTable($table), $column, $columnDefinition);
    }
    $installer->getConnection()->changeTableComment($installer->getTable($table), $tableData['comment']);
}
