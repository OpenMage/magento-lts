<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$usedDatabaseStorage = $installer->getConnection()->isTableExists(
    $installer->getTable('core/file_storage'),
);

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/layout_link'),
    'FK_CORE_LAYOUT_LINK_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/layout_link'),
    'FK_CORE_LAYOUT_LINK_UPDATE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/store'),
    'FK_STORE_GROUP_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/store'),
    'FK_STORE_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/session'),
    'FK_SESSION_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/store_group'),
    'FK_STORE_GROUP_WEBSITE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/translate'),
    'FK_CORE_TRANSLATE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/url_rewrite'),
    'CORE_URL_REWRITE_IBFK_1',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_CATEGORY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_PRODUCT',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/variable_value'),
    'FK_CORE_VARIABLE_VALUE_STORE_ID',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/variable_value'),
    'FK_CORE_VARIABLE_VALUE_VARIABLE_ID',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/design_change'),
    'FK_DESIGN_CHANGE_STORE',
);

if ($usedDatabaseStorage) {
    $installer->getConnection()->dropForeignKey(
        $installer->getTable('core/file_storage'),
        'FK_FILE_DIRECTORY',
    );

    $installer->getConnection()->dropForeignKey(
        $installer->getTable('core/directory_storage'),
        'FK_DIRECTORY_PARENT_ID',
    );
}

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('core/cache'),
    'IDX_EXPIRE_TIME',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/cache_tag'),
    'IDX_CACHE_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/config_data'),
    'CONFIG_SCOPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/email_template'),
    'TEMPLATE_CODE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/email_template'),
    'ADDED_AT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/email_template'),
    'MODIFIED_AT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/flag'),
    'LAST_UPDATE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/layout_link'),
    'STORE_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/layout_link'),
    'FK_CORE_LAYOUT_LINK_UPDATE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/layout_update'),
    'HANDLE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store'),
    'CODE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store'),
    'FK_STORE_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store'),
    'IS_ACTIVE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store'),
    'FK_STORE_GROUP',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store_group'),
    'FK_STORE_GROUP_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/store_group'),
    'DEFAULT_STORE_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/translate'),
    'IDX_CODE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/translate'),
    'FK_CORE_TRANSLATE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/session'),
    'FK_SESSION_WEBSITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'UNQ_REQUEST_PATH',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'UNQ_PATH',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_CATEGORY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'FK_CORE_URL_REWRITE_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'IDX_ID_PATH',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'IDX_CATEGORY_REWRITE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/url_rewrite'),
    'IDX_TARGET_PATH',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/variable'),
    'IDX_CODE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/variable_value'),
    'IDX_VARIABLE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/variable_value'),
    'IDX_VARIABLE_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/variable_value'),
    'IDX_STORE_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/website'),
    'CODE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/website'),
    'SORT_ORDER',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/website'),
    'DEFAULT_GROUP_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('core/design_change'),
    'FK_DESIGN_CHANGE_STORE',
);

if ($usedDatabaseStorage) {
    $installer->getConnection()->dropIndex(
        $installer->getTable('core/file_storage'),
        'IDX_FILENAME',
    );

    $installer->getConnection()->dropIndex(
        $installer->getTable('core/file_storage'),
        'directory_id',
    );

    $installer->getConnection()->dropIndex(
        $installer->getTable('core/directory_storage'),
        'IDX_DIRECTORY_PATH',
    );

    $installer->getConnection()->dropIndex(
        $installer->getTable('core/directory_storage'),
        'parent_id',
    );
}

/*
 * Change columns
 */
$tables = [
    $installer->getTable('core/config_data') => [
        'columns' => [
            'config_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Config Id',
            ],
            'scope' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 8,
                'nullable'  => false,
                'default'   => 'default',
                'comment'   => 'Config Scope',
            ],
            'scope_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Config Scope Id',
            ],
            'path' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'default'   => 'general',
                'comment'   => 'Config Path',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Config Value',
            ],
        ],
        'comment' => 'Config Data',
    ],
    $installer->getTable('core/website') => [
        'columns' => [
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id',
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Code',
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Website Name',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
            'default_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Default Group Id',
            ],
            'is_default' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Defines Is Website Default',
            ],
        ],
        'comment' => 'Websites',
    ],
    $installer->getTable('core/store') => [
        'columns' => [
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Store Id',
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Code',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website Id',
            ],
            'group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Group Id',
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Store Name',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Sort Order',
            ],
            'is_active' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Activity',
            ],
        ],
        'comment' => 'Stores',
    ],
    $installer->getTable('core/resource') => [
        'columns' => [
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Resource Code',
            ],
            'version' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Resource Version',
            ],
            'data_version' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Data Version',
            ],
        ],
        'comment' => 'Resources',
    ],
    $installer->getTable('core/cache') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Cache Id',
            ],
            'data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BLOB,
                'length'    => '2M',
                'comment'   => 'Cache Data',
            ],
            'create_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Cache Creation Time',
            ],
            'update_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Time of Cache Updating',
            ],
            'expire_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Cache Expiration Time',
            ],
        ],
        'comment' => 'Caches',
    ],
    $installer->getTable('core/cache_tag') => [
        'columns' => [
            'tag' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tag',
            ],
            'cache_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Cache Id',
            ],
        ],
        'comment' => 'Tag Caches',
    ],
    $installer->getTable('core/cache_option') => [
        'columns' => [
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Code',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Cache Options',
    ],
    $installer->getTable('core/store_group') => [
        'columns' => [
            'group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Group Id',
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website Id',
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Store Group Name',
            ],
            'root_category_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Root Category Id',
            ],
            'default_store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Default Store Id',
            ],
        ],
        'comment' => 'Store Groups',
    ],
    $installer->getTable('core/email_template') => [
        'columns' => [
            'template_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Template Id',
            ],
            'template_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 150,
                'nullable'  => false,
                'comment'   => 'Template Name',
            ],
            'template_text' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Template Content',
            ],
            'template_styles' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Templste Styles',
            ],
            'template_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Template Type',
            ],
            'template_subject' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'nullable'  => false,
                'comment'   => 'Template Subject',
            ],
            'template_sender_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'comment'   => 'Template Sender Name',
            ],
            'template_sender_email' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'comment'   => 'Template Sender Email',
            ],
            'added_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Date of Template Creation',
            ],
            'modified_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Date of Template Modification',
            ],
            'orig_template_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'comment'   => 'Original Template Code',
            ],
            'orig_template_variables' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Original Template Variables',
            ],
        ],
        'comment' => 'Email Templates',
    ],
    $installer->getTable('core/variable') => [
        'columns' => [
            'variable_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Variable Id',
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Variable Code',
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Variable Name',
            ],
        ],
        'comment' => 'Variables',
    ],
    $installer->getTable('core/variable_value') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Variable Value Id',
            ],
            'variable_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Variable Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
            'plain_value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Plain Text Value',
            ],
            'html_value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Html Value',
            ],
        ],
        'comment' => 'Variable Value',
    ],
    $installer->getTable('core/translate') => [
        'columns' => [
            'key_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Key Id of Translation',
            ],
            'string' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'default'   => Mage_Core_Model_Translate::DEFAULT_STRING,
                'comment'   => 'Translation String',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
            'translate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Translate',
            ],
            'locale' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'default'   => 'en_US',
                'comment'   => 'Locale',
            ],
        ],
        'comment' => 'Translations',
    ],
    $installer->getTable('core/session') => [
        'columns' => [
            'session_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Session Id',
            ],
            'session_expires' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Date of Session Expiration',
            ],
            'session_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BLOB,
                'length'    => '2M',
                'nullable'  => false,
                'comment'   => 'Session Data',
            ],
        ],
        'comment' => 'Database Sessions Storage',
    ],
    $installer->getTable('core/layout_update') => [
        'columns' => [
            'layout_update_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Layout Update Id',
            ],
            'handle' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Handle',
            ],
            'xml' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Xml',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
        ],
        'comment' => 'Layout Updates',
    ],
    $installer->getTable('core/layout_link') => [
        'columns' => [
            'layout_link_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Link Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
            'area' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Area',
            ],
            'package' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Package',
            ],
            'theme' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Theme',
            ],
            'layout_update_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Layout Update Id',
            ],
        ],
        'comment' => 'Layout Link',
    ],
    $installer->getTable('core/url_rewrite') => [
        'columns' => [
            'url_rewrite_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rewrite Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
            'id_path' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Id Path',
            ],
            'request_path' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Request Path',
            ],
            'target_path' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Target Path',
            ],
            'is_system' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '1',
                'comment'   => 'Defines is Rewrite System',
            ],
            'options' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Options',
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Deascription',
            ],
        ],
        'comment' => 'Url Rewrites',
    ],
    $installer->getTable('core/design_change') => [
        'columns' => [
            'design_change_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Design Change Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
            'design' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Design',
            ],
            'date_from' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'First Date of Design Activity',
            ],
            'date_to' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Last Date of Design Activity',
            ],
        ],
        'comment' => 'Design Changes',
    ],
    $installer->getTable('core/flag') => [
        'columns' => [
            'flag_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Flag Id',
            ],
            'flag_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Flag Code',
            ],
            'state' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Flag State',
            ],
            'flag_data' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Flag Data',
            ],
            'last_update' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
                'comment'   => 'Date of Last Flag Update',
            ],
        ],
        'comment' => 'Flag',
    ],
];

if ($usedDatabaseStorage) {
    $storageTables = [
        $installer->getTable('core/file_storage') => [
            'columns' => [
                'file_id' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'identity'  => true,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'comment'   => 'File Id',
                ],
                'content' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_VARBINARY,
                    'length'    => Varien_Db_Ddl_Table::MAX_VARBINARY_SIZE,
                    'nullable'  => false,
                    'comment'   => 'File Content',
                ],
                'upload_time' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                    'nullable'  => false,
                    'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
                    'comment'   => 'Upload Timestamp',
                ],
                'filename' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => 100,
                    'nullable'  => false,
                    'comment'   => 'Filename',
                ],
                'directory_id' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'unsigned'  => true,
                    'default'   => null,
                    'comment'   => 'Identifier of Directory where File is Located',
                ],
                'directory' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => 255,
                    'default'   => null,
                    'comment'   => 'Directory Path',
                ],
            ],
            'comment' => 'File Storage',
        ],
        $installer->getTable('core/directory_storage') => [
            'columns' => [
                'directory_id' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'identity'  => true,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'comment'   => 'Directory Id',
                ],
                'name' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => 100,
                    'nullable'  => false,
                    'comment'   => 'Directory Name',
                ],
                'path' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => 255,
                    'default'   => null,
                    'comment'   => 'Path to the Directory',
                ],
                'upload_time' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                    'nullable'  => false,
                    'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
                    'comment'   => 'Upload Timestamp',
                ],
                'parent_id' => [
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'unsigned'  => true,
                    'default'   => null,
                    'comment'   => 'Parent Directory Id',
                ],
            ],
            'comment' => 'Directory Storage',
        ],
    ];
    $tables = array_merge($tables, $storageTables);
}

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->dropColumn(
    $installer->getTable('core/session'),
    'website_id',
);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('core/variable'),
    $installer->getIdxName(
        'core/variable',
        ['code'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['code'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/variable_value'),
    $installer->getIdxName(
        'core/variable_value',
        ['variable_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['variable_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/variable_value'),
    $installer->getIdxName('core/variable_value', ['variable_id']),
    ['variable_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/variable_value'),
    $installer->getIdxName('core/variable_value', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/cache'),
    $installer->getIdxName('core/cache', ['expire_time']),
    ['expire_time'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/cache_tag'),
    $installer->getIdxName('core/cache_tag', ['cache_id']),
    ['cache_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/config_data'),
    $installer->getIdxName(
        'core/config_data',
        ['scope', 'scope_id', 'path'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['scope', 'scope_id', 'path'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/email_template'),
    $installer->getIdxName(
        'core/email_template',
        ['template_code'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['template_code'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/email_template'),
    $installer->getIdxName('core/email_template', ['added_at']),
    ['added_at'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/email_template'),
    $installer->getIdxName('core/email_template', ['modified_at']),
    ['modified_at'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/flag'),
    $installer->getIdxName('core/flag', ['last_update']),
    ['last_update'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/layout_link'),
    $installer->getIdxName(
        'core/layout_link',
        ['store_id', 'package', 'theme', 'layout_update_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['store_id', 'package', 'theme', 'layout_update_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/layout_link'),
    $installer->getIdxName('core/layout_link', ['layout_update_id']),
    ['layout_update_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/layout_update'),
    $installer->getIdxName('core/layout_update', ['handle']),
    ['handle'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store'),
    $installer->getIdxName(
        'core/store',
        ['code'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['code'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store'),
    $installer->getIdxName('core/store', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store'),
    $installer->getIdxName('core/store', ['is_active', 'sort_order']),
    ['is_active', 'sort_order'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store'),
    $installer->getIdxName('core/store', ['group_id']),
    ['group_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store_group'),
    $installer->getIdxName('core/store_group', ['website_id']),
    ['website_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/store_group'),
    $installer->getIdxName('core/store_group', ['default_store_id']),
    ['default_store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/translate'),
    $installer->getIdxName(
        'core/translate',
        ['store_id', 'locale', 'string'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['store_id', 'locale', 'string'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/translate'),
    $installer->getIdxName('core/translate', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/url_rewrite'),
    $installer->getIdxName(
        'core/url_rewrite',
        ['request_path', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['request_path', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/url_rewrite'),
    $installer->getIdxName(
        'core/url_rewrite',
        ['id_path', 'is_system', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['id_path', 'is_system', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/url_rewrite'),
    $installer->getIdxName('core/url_rewrite', ['target_path', 'store_id']),
    ['target_path', 'store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/url_rewrite'),
    $installer->getIdxName('core/url_rewrite', ['id_path']),
    ['id_path'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/url_rewrite'),
    $installer->getIdxName('core/url_rewrite', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/website'),
    $installer->getIdxName(
        'core/website',
        ['code'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['code'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/website'),
    $installer->getIdxName('core/website', ['sort_order']),
    ['sort_order'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/website'),
    $installer->getIdxName('core/website', ['default_group_id']),
    ['default_group_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('core/design_change'),
    $installer->getIdxName('core/design_change', ['store_id']),
    ['store_id'],
);

if ($usedDatabaseStorage) {
    $installer->getConnection()->addIndex(
        $installer->getTable('core/file_storage'),
        $installer->getIdxName(
            'core/file_storage',
            ['filename', 'directory_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['filename', 'directory_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    );

    $installer->getConnection()->addIndex(
        $installer->getTable('core/file_storage'),
        $installer->getIdxName('core/file_storage', ['directory_id']),
        ['directory_id'],
    );

    $installer->getConnection()->addIndex(
        $installer->getTable('core/directory_storage'),
        $installer->getIdxName(
            'core/directory_storage',
            ['name', 'parent_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['name', 'parent_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    );

    $installer->getConnection()->addIndex(
        $installer->getTable('core/directory_storage'),
        $installer->getIdxName('core/directory_storage', ['parent_id']),
        ['parent_id'],
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
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/layout_link', 'layout_update_id', 'core/layout_update', 'layout_update_id'),
    $installer->getTable('core/layout_link'),
    'layout_update_id',
    $installer->getTable('core/layout_update'),
    'layout_update_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/store', 'group_id', 'core/store_group', 'group_id'),
    $installer->getTable('core/store'),
    'group_id',
    $installer->getTable('core/store_group'),
    'group_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/store', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('core/store'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/store_group', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('core/store_group'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/translate', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('core/translate'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/url_rewrite', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('core/url_rewrite'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/url_rewrite', 'category_id', 'catalog/category', 'entity_id'),
    $installer->getTable('core/url_rewrite'),
    'category_id',
    $installer->getTable('catalog/category'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/url_rewrite', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('core/url_rewrite'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/variable_value', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('core/variable_value'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/variable_value', 'variable_id', 'core/variable', 'variable_id'),
    $installer->getTable('core/variable_value'),
    'variable_id',
    $installer->getTable('core/variable'),
    'variable_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/design_change', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('core/design_change'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

if ($usedDatabaseStorage) {
    $installer->getConnection()->addForeignKey(
        $installer->getFkName('core/file_storage', 'directory_id', 'core/directory_storage', 'directory_id'),
        $installer->getTable('core/file_storage'),
        'directory_id',
        $installer->getTable('core/directory_storage'),
        'directory_id',
    );

    $installer->getConnection()->addForeignKey(
        $installer->getFkName('core/directory_storage', 'parent_id', 'core/directory_storage', 'directory_id'),
        $installer->getTable('core/directory_storage'),
        'parent_id',
        $installer->getTable('core/directory_storage'),
        'directory_id',
    );
}

$installer->endSetup();
