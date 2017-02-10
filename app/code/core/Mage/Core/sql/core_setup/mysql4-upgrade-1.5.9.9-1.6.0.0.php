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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$usedDatabaseStorage = $installer->getConnection()->isTableExists(
    $installer->getTable('core/file_storage')
);

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/layout_link'),
    'FK_CORE_LAYOUT_LINK_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/layout_link'),
    'FK_CORE_LAYOUT_LINK_UPDATE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/store'),
    'FK_STORE_GROUP_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/store'),
    'FK_STORE_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/session'),
    'FK_SESSION_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/store_group'),
    'FK_STORE_GROUP_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/translate'),
    'FK_CORE_TRANSLATE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/url_rewrite'),
    'CORE_URL_REWRITE_IBFK_1'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_CATEGORY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/variable_value'),
    'FK_CORE_VARIABLE_VALUE_STORE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/variable_value'),
    'FK_CORE_VARIABLE_VALUE_VARIABLE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/design_change'),
    'FK_DESIGN_CHANGE_STORE'
);

if ($usedDatabaseStorage) {
    $installer->getConnection()->dropForeignKey(
        $installer->getTable('core/file_storage'),
        'FK_FILE_DIRECTORY'
    );

    $installer->getConnection()->dropForeignKey(
        $installer->getTable('core/directory_storage'),
        'FK_DIRECTORY_PARENT_ID'
    );
}


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('core/cache'),
    'IDX_EXPIRE_TIME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/cache_tag'),
    'IDX_CACHE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/config_data'),
    'CONFIG_SCOPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/email_template'),
    'TEMPLATE_CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/email_template'),
    'ADDED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/email_template'),
    'MODIFIED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/flag'),
    'LAST_UPDATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/layout_link'),
    'STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/layout_link'),
    'FK_CORE_LAYOUT_LINK_UPDATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/layout_update'),
    'HANDLE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store'),
    'CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store'),
    'FK_STORE_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store'),
    'IS_ACTIVE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store'),
    'FK_STORE_GROUP'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store_group'),
    'FK_STORE_GROUP_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store_group'),
    'DEFAULT_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/translate'),
    'IDX_CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/translate'),
    'FK_CORE_TRANSLATE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/session'),
    'FK_SESSION_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'UNQ_REQUEST_PATH'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'UNQ_PATH'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_CATEGORY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'IDX_ID_PATH'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'IDX_CATEGORY_REWRITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'IDX_TARGET_PATH'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/variable'),
    'IDX_CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/variable_value'),
    'IDX_VARIABLE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/variable_value'),
    'IDX_VARIABLE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/variable_value'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/website'),
    'CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/website'),
    'SORT_ORDER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/website'),
    'DEFAULT_GROUP_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/design_change'),
    'FK_DESIGN_CHANGE_STORE'
);

if ($usedDatabaseStorage) {
    $installer->getConnection()->dropIndex(
        $installer->getTable('core/file_storage'),
        'IDX_FILENAME'
    );

    $installer->getConnection()->dropIndex(
        $installer->getTable('core/file_storage'),
        'directory_id'
    );

    $installer->getConnection()->dropIndex(
        $installer->getTable('core/directory_storage'),
        'IDX_DIRECTORY_PATH'
    );

    $installer->getConnection()->dropIndex(
        $installer->getTable('core/directory_storage'),
        'parent_id'
    );
}


/*
 * Change columns
 */
$tables = array(
    $installer->getTable('core/config_data') => array(
        'columns' => array(
            'config_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Config Id'
            ),
            'scope' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 8,
                'nullable'  => false,
                'default'   => 'default',
                'comment'   => 'Config Scope'
            ),
            'scope_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Config Scope Id'
            ),
            'path' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'default'   => 'general',
                'comment'   => 'Config Path'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Config Value'
            )
        ),
        'comment' => 'Config Data'
    ),
    $installer->getTable('core/website') => array(
        'columns' => array(
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Code'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Website Name'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            ),
            'default_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Default Group Id'
            ),
            'is_default' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Defines Is Website Default'
            )
        ),
        'comment' => 'Websites'
    ),
    $installer->getTable('core/store') => array(
        'columns' => array(
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Store Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Code'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website Id'
            ),
            'group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Group Id'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Store Name'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Sort Order'
            ),
            'is_active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Activity'
            )
        ),
        'comment' => 'Stores'
    ),
    $installer->getTable('core/resource') => array(
        'columns' => array(
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Resource Code'
            ),
            'version' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Resource Version'
            ),
            'data_version' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Data Version'
            )
        ),
        'comment' => 'Resources'
    ),
    $installer->getTable('core/cache') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Cache Id'
            ),
            'data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BLOB,
                'length'    => '2M',
                'comment'   => 'Cache Data'
            ),
            'create_time' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Cache Creation Time'
            ),
            'update_time' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Time of Cache Updating'
            ),
            'expire_time' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Cache Expiration Time'
            )
        ),
        'comment' => 'Caches'
    ),
    $installer->getTable('core/cache_tag') => array(
        'columns' => array(
            'tag' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tag'
            ),
            'cache_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Cache Id'
            )
        ),
        'comment' => 'Tag Caches'
    ),
    $installer->getTable('core/cache_option') => array(
        'columns' => array(
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Code'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Cache Options'
    ),
    $installer->getTable('core/store_group') => array(
        'columns' => array(
            'group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Group Id'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website Id'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Store Group Name'
            ),
            'root_category_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Root Category Id'
            ),
            'default_store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Default Store Id'
            )
        ),
        'comment' => 'Store Groups'
    ),
    $installer->getTable('core/email_template') => array(
        'columns' => array(
            'template_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Template Id'
            ),
            'template_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 150,
                'nullable'  => false,
                'comment'   => 'Template Name'
            ),
            'template_text' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Template Content'
            ),
            'template_styles' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Templste Styles'
            ),
            'template_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Template Type'
            ),
            'template_subject' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'nullable'  => false,
                'comment'   => 'Template Subject'
            ),
            'template_sender_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'comment'   => 'Template Sender Name'
            ),
            'template_sender_email' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'comment'   => 'Template Sender Email'
            ),
            'added_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Date of Template Creation'
            ),
            'modified_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Date of Template Modification'
            ),
            'orig_template_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'comment'   => 'Original Template Code'
            ),
            'orig_template_variables' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Original Template Variables'
            )
        ),
        'comment' => 'Email Templates'
    ),
    $installer->getTable('core/variable') => array(
        'columns' => array(
            'variable_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Variable Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Variable Code'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Variable Name'
            )
        ),
        'comment' => 'Variables'
    ),
    $installer->getTable('core/variable_value') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Variable Value Id'
            ),
            'variable_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Variable Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'plain_value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Plain Text Value'
            ),
            'html_value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Html Value'
            )
        ),
        'comment' => 'Variable Value'
    ),
    $installer->getTable('core/translate') => array(
        'columns' => array(
            'key_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Key Id of Translation'
            ),
            'string' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'default'   => Mage_Core_Model_Translate::DEFAULT_STRING,
                'comment'   => 'Translation String'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'translate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Translate'
            ),
            'locale' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'default'   => 'en_US',
                'comment'   => 'Locale'
            )
        ),
        'comment' => 'Translations'
    ),
    $installer->getTable('core/session') => array(
        'columns' => array(
            'session_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Session Id'
            ),
            'session_expires' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Date of Session Expiration'
            ),
            'session_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BLOB,
                'length'    => '2M',
                'nullable'  => false,
                'comment'   => 'Session Data'
            )
        ),
        'comment' => 'Database Sessions Storage'
    ),
    $installer->getTable('core/layout_update') => array(
        'columns' => array(
            'layout_update_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Layout Update Id'
            ),
            'handle' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Handle'
            ),
            'xml' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Xml'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            )
        ),
        'comment' => 'Layout Updates'
    ),
    $installer->getTable('core/layout_link') => array(
        'columns' => array(
            'layout_link_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Link Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'area' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Area'
            ),
            'package' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Package'
            ),
            'theme' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Theme'
            ),
            'layout_update_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Layout Update Id'
            )
        ),
        'comment' => 'Layout Link'
    ),
    $installer->getTable('core/url_rewrite') => array(
        'columns' => array(
            'url_rewrite_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rewrite Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'id_path' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Id Path'
            ),
            'request_path' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Request Path'
            ),
            'target_path' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Target Path'
            ),
            'is_system' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '1',
                'comment'   => 'Defines is Rewrite System'
            ),
            'options' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Options'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Deascription'
            )
        ),
        'comment' => 'Url Rewrites'
    ),
    $installer->getTable('core/design_change') => array(
        'columns' => array(
            'design_change_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Design Change Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'design' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Design'
            ),
            'date_from' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'First Date of Design Activity'
            ),
            'date_to' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Last Date of Design Activity'
            )
        ),
        'comment' => 'Design Changes'
    ),
    $installer->getTable('core/flag') => array(
        'columns' => array(
            'flag_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Flag Id'
            ),
            'flag_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Flag Code'
            ),
            'state' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Flag State'
            ),
            'flag_data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Flag Data'
            ),
            'last_update' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
                'comment'   => 'Date of Last Flag Update'
            )
        ),
        'comment' => 'Flag'
    )
);

if ($usedDatabaseStorage) {
    $storageTables = array(
        $installer->getTable('core/file_storage') => array(
            'columns' => array(
                'file_id' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'identity'  => true,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'comment'   => 'File Id'
                ),
                'content' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_VARBINARY,
                    'length'    => Varien_Db_Ddl_Table::MAX_VARBINARY_SIZE,
                    'nullable'  => false,
                    'comment'   => 'File Content'
                ),
                'upload_time' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                    'nullable'  => false,
                    'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
                    'comment'   => 'Upload Timestamp'
                ),
                'filename' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => 100,
                    'nullable'  => false,
                    'comment'   => 'Filename'
                ),
                'directory_id' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'unsigned'  => true,
                    'default'   => null,
                    'comment'   => 'Identifier of Directory where File is Located'
                ),
                'directory' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => 255,
                    'default'   => null,
                    'comment'   => 'Directory Path'
                )
            ),
            'comment' => 'File Storage'
        ),
        $installer->getTable('core/directory_storage') => array(
            'columns' => array(
                'directory_id' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'identity'  => true,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'comment'   => 'Directory Id'
                ),
                'name' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => 100,
                    'nullable'  => false,
                    'comment'   => 'Directory Name'
                ),
                'path' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => 255,
                    'default'   => null,
                    'comment'   => 'Path to the Directory'
                ),
                'upload_time' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                    'nullable'  => false,
                    'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
                    'comment'   => 'Upload Timestamp'
                ),
                'parent_id' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'unsigned'  => true,
                    'default'   => null,
                    'comment'   => 'Parent Directory Id'
                )
            ),
            'comment' => 'Directory Storage'
        )
    );
    $tables = array_merge($tables, $storageTables);
}

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->dropColumn(
    $installer->getTable('core/session'),
    'website_id'
);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('core/variable'),
    $installer->getIdxName(
        'core/variable',
        array('code'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/variable_value'),
    $installer->getIdxName(
        'core/variable_value',
        array('variable_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('variable_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/variable_value'),
    $installer->getIdxName('core/variable_value', array('variable_id')),
    array('variable_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/variable_value'),
    $installer->getIdxName('core/variable_value', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/cache'),
    $installer->getIdxName('core/cache', array('expire_time')),
    array('expire_time')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/cache_tag'),
    $installer->getIdxName('core/cache_tag', array('cache_id')),
    array('cache_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/config_data'),
    $installer->getIdxName(
        'core/config_data',
        array('scope', 'scope_id', 'path'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('scope', 'scope_id', 'path'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/email_template'),
    $installer->getIdxName(
        'core/email_template',
        array('template_code'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('template_code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/email_template'),
    $installer->getIdxName('core/email_template', array('added_at')),
    array('added_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/email_template'),
    $installer->getIdxName('core/email_template', array('modified_at')),
    array('modified_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/flag'),
    $installer->getIdxName('core/flag', array('last_update')),
    array('last_update')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/layout_link'),
    $installer->getIdxName(
        'core/layout_link',
        array('store_id', 'package', 'theme', 'layout_update_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('store_id', 'package', 'theme', 'layout_update_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/layout_link'),
    $installer->getIdxName('core/layout_link', array('layout_update_id')),
    array('layout_update_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/layout_update'),
    $installer->getIdxName('core/layout_update', array('handle')),
    array('handle')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store'),
    $installer->getIdxName(
        'core/store',
        array('code'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store'),
    $installer->getIdxName('core/store', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store'),
    $installer->getIdxName('core/store', array('is_active', 'sort_order')),
    array('is_active', 'sort_order')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store'),
    $installer->getIdxName('core/store', array('group_id')),
    array('group_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store_group'),
    $installer->getIdxName('core/store_group', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store_group'),
    $installer->getIdxName('core/store_group', array('default_store_id')),
    array('default_store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/translate'),
    $installer->getIdxName(
        'core/translate',
        array('store_id', 'locale', 'string'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('store_id', 'locale', 'string'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/translate'),
    $installer->getIdxName('core/translate', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/url_rewrite'),
    $installer->getIdxName(
        'core/url_rewrite',
        array('request_path', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('request_path', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/url_rewrite'),
    $installer->getIdxName(
        'core/url_rewrite',
        array('id_path', 'is_system', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('id_path', 'is_system', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/url_rewrite'),
    $installer->getIdxName('core/url_rewrite', array('target_path', 'store_id')),
    array('target_path', 'store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/url_rewrite'),
    $installer->getIdxName('core/url_rewrite', array('id_path')),
    array('id_path')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/url_rewrite'),
    $installer->getIdxName('core/url_rewrite', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/website'),
    $installer->getIdxName(
        'core/website',
        array('code'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/website'),
    $installer->getIdxName('core/website', array('sort_order')),
    array('sort_order')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/website'),
    $installer->getIdxName('core/website', array('default_group_id')),
    array('default_group_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/design_change'),
    $installer->getIdxName('core/design_change', array('store_id')),
    array('store_id')
);

if ($usedDatabaseStorage) {
    $installer->getConnection()->addIndex(
        $installer->getTable('core/file_storage'),
        $installer->getIdxName(
            'core/file_storage',
            array('filename', 'directory_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('filename', 'directory_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );

    $installer->getConnection()->addIndex(
        $installer->getTable('core/file_storage'),
        $installer->getIdxName('core/file_storage', array('directory_id')),
        array('directory_id')
    );

    $installer->getConnection()->addIndex(
        $installer->getTable('core/directory_storage'),
        $installer->getIdxName(
            'core/directory_storage',
            array('name', 'parent_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('name', 'parent_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );

    $installer->getConnection()->addIndex(
        $installer->getTable('core/directory_storage'),
        $installer->getIdxName('core/directory_storage', array('parent_id')),
        array('parent_id')
    );
}


/**
 * Add foreign keys
 */

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/layout_link', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('core/layout_link'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/layout_link', 'layout_update_id', 'core/layout_update', 'layout_update_id'),
    $installer->getTable('core/layout_link'),
    'layout_update_id',
    $installer->getTable('core/layout_update'),
    'layout_update_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/store', 'group_id', 'core/store_group', 'group_id'),
    $installer->getTable('core/store'),
    'group_id',
    $installer->getTable('core/store_group'),
    'group_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/store', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('core/store'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/store_group', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('core/store_group'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/translate', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('core/translate'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/url_rewrite', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('core/url_rewrite'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/url_rewrite', 'category_id', 'catalog/category', 'entity_id'),
    $installer->getTable('core/url_rewrite'),
    'category_id',
    $installer->getTable('catalog/category'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/url_rewrite', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('core/url_rewrite'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/variable_value', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('core/variable_value'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/variable_value', 'variable_id', 'core/variable', 'variable_id'),
    $installer->getTable('core/variable_value'),
    'variable_id',
    $installer->getTable('core/variable'),
    'variable_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/design_change', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('core/design_change'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

if ($usedDatabaseStorage) {
    $installer->getConnection()->addForeignKey(
        $installer->getFkName('core/file_storage', 'directory_id', 'core/directory_storage', 'directory_id'),
        $installer->getTable('core/file_storage'),
        'directory_id',
        $installer->getTable('core/directory_storage'),
        'directory_id'
    );

    $installer->getConnection()->addForeignKey(
        $installer->getFkName('core/directory_storage', 'parent_id', 'core/directory_storage', 'directory_id'),
        $installer->getTable('core/directory_storage'),
        'parent_id',
        $installer->getTable('core/directory_storage'),
        'directory_id'
    );
}

$installer->endSetup();
