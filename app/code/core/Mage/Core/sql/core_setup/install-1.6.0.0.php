<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'core/resource'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/resource'))
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Resource Code')
    ->addColumn('version', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Resource Version')
    ->addColumn('data_version', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Data Version')
    ->setComment('Resources');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/website'))
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Code')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
    ], 'Website Name')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addColumn('default_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Default Group Id')
    ->addColumn('is_default', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Defines Is Website Default')
    ->addIndex(
        $installer->getIdxName('core/website', ['code'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('core/website', ['sort_order']),
        ['sort_order'],
    )
    ->addIndex(
        $installer->getIdxName('core/website', ['default_group_id']),
        ['default_group_id'],
    )
    ->setComment('Websites');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/store_group'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/store_group'))
    ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Group Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Website Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Store Group Name')
    ->addColumn('root_category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Root Category Id')
    ->addColumn('default_store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Default Store Id')
    ->addIndex(
        $installer->getIdxName('core/store_group', ['website_id']),
        ['website_id'],
    )
    ->addIndex(
        $installer->getIdxName('core/store_group', ['default_store_id']),
        ['default_store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('core/store_group', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Store Groups');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/store'))
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Code')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Website Id')
    ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Group Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Store Name')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Sort Order')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Activity')
    ->addIndex(
        $installer->getIdxName('core/store', ['code'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('core/store', ['website_id']),
        ['website_id'],
    )
    ->addIndex(
        $installer->getIdxName('core/store', ['is_active', 'sort_order']),
        ['is_active', 'sort_order'],
    )
    ->addIndex(
        $installer->getIdxName('core/store', ['group_id']),
        ['group_id'],
    )
    ->addForeignKey(
        $installer->getFkName('core/store', 'group_id', 'core/store_group', 'group_id'),
        'group_id',
        $installer->getTable('core/store_group'),
        'group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('core/store', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Stores');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/config_data'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/config_data'))
    ->addColumn('config_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Config Id')
    ->addColumn('scope', Varien_Db_Ddl_Table::TYPE_TEXT, 8, [
        'nullable'  => false,
        'default'   => 'default',
    ], 'Config Scope')
    ->addColumn('scope_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Config Scope Id')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
        'default'   => 'general',
    ], 'Config Path')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [], 'Config Value')
    ->addIndex(
        $installer->getIdxName(
            'core/config_data',
            ['scope', 'scope_id', 'path'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['scope', 'scope_id', 'path'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->setComment('Config Data');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/email_template'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/email_template'))
    ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Template Id')
    ->addColumn('template_code', Varien_Db_Ddl_Table::TYPE_TEXT, 150, [
        'nullable' => false,
    ], 'Template Name')
    ->addColumn('template_text', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable' => false,
    ], 'Template Content')
    ->addColumn('template_styles', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Templste Styles')
    ->addColumn('template_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Template Type')
    ->addColumn('template_subject', Varien_Db_Ddl_Table::TYPE_TEXT, 200, [
        'nullable' => false,
    ], 'Template Subject')
    ->addColumn('template_sender_name', Varien_Db_Ddl_Table::TYPE_TEXT, 200, [
    ], 'Template Sender Name')
    ->addColumn('template_sender_email', Varien_Db_Ddl_Table::TYPE_TEXT, 200, [
    ], 'Template Sender Email')
    ->addColumn('added_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Date of Template Creation')
    ->addColumn('modified_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Date of Template Modification')
    ->addColumn('orig_template_code', Varien_Db_Ddl_Table::TYPE_TEXT, 200, [
    ], 'Original Template Code')
    ->addColumn('orig_template_variables', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Original Template Variables')
    ->addIndex(
        $installer->getIdxName(
            'core/email_template',
            ['template_code'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['template_code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('core/email_template', ['added_at']),
        ['added_at'],
    )
    ->addIndex(
        $installer->getIdxName('core/email_template', ['modified_at']),
        ['modified_at'],
    )
    ->setComment('Email Templates');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/layout_update'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/layout_update'))
    ->addColumn('layout_update_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Layout Update Id')
    ->addColumn('handle', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Handle')
    ->addColumn('xml', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Xml')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addIndex(
        $installer->getIdxName('core/layout_update', ['handle']),
        ['handle'],
    )
    ->setComment('Layout Updates');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/layout_link'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/layout_link'))
    ->addColumn('layout_link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Link Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('area', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
    ], 'Area')
    ->addColumn('package', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
    ], 'Package')
    ->addColumn('theme', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
    ], 'Theme')
    ->addColumn('layout_update_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Layout Update Id')
    ->addIndex(
        $installer->getIdxName(
            'core/layout_link',
            ['store_id', 'package', 'theme', 'layout_update_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['store_id', 'package', 'theme', 'layout_update_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('core/layout_link', ['layout_update_id']),
        ['layout_update_id'],
    )
    ->addForeignKey(
        $installer->getFkName('core/layout_link', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('core/layout_link', 'layout_update_id', 'core/layout_update', 'layout_update_id'),
        'layout_update_id',
        $installer->getTable('core/layout_update'),
        'layout_update_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Layout Link');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/session'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/session'))
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Session Id')
    ->addColumn('session_expires', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Date of Session Expiration')
    ->addColumn('session_data', Varien_Db_Ddl_Table::TYPE_BLOB, '2M', [
        'nullable'  => false,
    ], 'Session Data')
    ->setComment('Database Sessions Storage');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/translate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/translate'))
    ->addColumn('key_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Key Id of Translation')
    ->addColumn('string', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
        'default'   => Mage_Core_Model_Translate::DEFAULT_STRING,
    ], 'Translation String')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('translate', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Translate')
    ->addColumn('locale', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
        'nullable'  => false,
        'default'   => 'en_US',
    ], 'Locale')
    ->addIndex(
        $installer->getIdxName(
            'core/translate',
            ['store_id', 'locale', 'string'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['store_id', 'locale', 'string'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('core/translate', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('core/translate', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Translations');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/url_rewrite'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/url_rewrite'))
    ->addColumn('url_rewrite_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Rewrite Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('id_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Id Path')
    ->addColumn('request_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Request Path')
    ->addColumn('target_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Target Path')
    ->addColumn('is_system', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '1',
    ], 'Defines is Rewrite System')
    ->addColumn('options', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
    ], 'Options')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Deascription')
    ->addIndex(
        $installer->getIdxName(
            'core/url_rewrite',
            ['request_path', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['request_path', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName(
            'core/url_rewrite',
            ['id_path', 'is_system', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['id_path', 'is_system', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('core/url_rewrite', ['target_path', 'store_id']),
        ['target_path', 'store_id'],
    )
    ->addIndex(
        $installer->getIdxName('core/url_rewrite', ['id_path']),
        ['id_path'],
    )
    ->addIndex(
        $installer->getIdxName('core/url_rewrite', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('core/url_rewrite', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Url Rewrites');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/design_change'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/design_change'))
    ->addColumn('design_change_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Design Change Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('design', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Design')
    ->addColumn('date_from', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'First Date of Design Activity')
    ->addColumn('date_to', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Last Date of Design Activity')
    ->addIndex(
        $installer->getIdxName('core/design_change', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('core/design_change', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Design Changes');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/variable'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/variable'))
    ->addColumn('variable_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Variable Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Variable Code')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Variable Name')
    ->addIndex(
        $installer->getIdxName('core/variable', ['code'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->setComment('Variables');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/variable_value'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/variable_value'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Variable Value Id')
    ->addColumn('variable_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Variable Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('plain_value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Plain Text Value')
    ->addColumn('html_value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Html Value')
    ->addIndex(
        $installer->getIdxName(
            'core/variable_value',
            ['variable_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['variable_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('core/variable_value', ['variable_id']),
        ['variable_id'],
    )
    ->addIndex(
        $installer->getIdxName('core/variable_value', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('core/variable_value', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('core/variable_value', 'variable_id', 'core/variable', 'variable_id'),
        'variable_id',
        $installer->getTable('core/variable'),
        'variable_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Variable Value');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/cache'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/cache'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_TEXT, 200, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Cache Id')
    ->addColumn('data', Varien_Db_Ddl_Table::TYPE_BLOB, '2M', [
    ], 'Cache Data')
    ->addColumn('create_time', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Cache Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Time of Cache Updating')
    ->addColumn('expire_time', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Cache Expiration Time')
    ->addIndex(
        $installer->getIdxName('core/cache', ['expire_time']),
        ['expire_time'],
    )
    ->setComment('Caches');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/cache_tag'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/cache_tag'))
    ->addColumn('tag', Varien_Db_Ddl_Table::TYPE_TEXT, 100, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Tag')
    ->addColumn('cache_id', Varien_Db_Ddl_Table::TYPE_TEXT, 200, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Cache Id')
    ->addIndex(
        $installer->getIdxName('core/cache_tag', ['cache_id']),
        ['cache_id'],
    )
    ->setComment('Tag Caches');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/cache_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/cache_option'))
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Code')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
    ], 'Value')
    ->setComment('Cache Options');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/flag'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/flag'))
    ->addColumn('flag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Flag Id')
    ->addColumn('flag_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Flag Code')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Flag State')
    ->addColumn('flag_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Flag Data')
    ->addColumn('last_update', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
        'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
    ], 'Date of Last Flag Update')
    ->addIndex(
        $installer->getIdxName('core/flag', ['last_update']),
        ['last_update'],
    )
    ->setComment('Flag');
$installer->getConnection()->createTable($table);

/**
 * Insert core websites
 */
$installer->getConnection()->insertForce($installer->getTable('core/website'), [
    'website_id'        => 0,
    'code'              => 'admin',
    'name'              => 'Admin',
    'sort_order'        => 0,
    'default_group_id'  => 0,
    'is_default'        => 0,
]);
$installer->getConnection()->insertForce($installer->getTable('core/website'), [
    'website_id'        => 1,
    'code'              => 'base',
    'name'              => 'Main Website',
    'sort_order'        => 0,
    'default_group_id'  => 1,
    'is_default'        => 1,
]);

/**
 * Insert core store groups
 */
$installer->getConnection()->insertForce($installer->getTable('core/store_group'), [
    'group_id'          => 0,
    'website_id'        => 0,
    'name'              => 'Default',
    'root_category_id'  => 0,
    'default_store_id'  => 0,
]);
$installer->getConnection()->insertForce($installer->getTable('core/store_group'), [
    'group_id'          => 1,
    'website_id'        => 1,
    'name'              => 'Main Website Store',
    'root_category_id'  => 2,
    'default_store_id'  => 1,
]);

/**
 * Insert core stores
 */
$installer->getConnection()->insertForce($installer->getTable('core/store'), [
    'store_id'      => 0,
    'code'          => 'admin',
    'website_id'    => 0,
    'group_id'      => 0,
    'name'          => 'Admin',
    'sort_order'    => 0,
    'is_active'     => 1,
]);
$installer->getConnection()->insertForce($installer->getTable('core/store'), [
    'store_id'      => 1,
    'code'          => 'default',
    'website_id'    => 1,
    'group_id'      => 1,
    'name'          => 'Default Store View',
    'sort_order'    => 0,
    'is_active'     => 1,
]);

$installer->endSetup();
