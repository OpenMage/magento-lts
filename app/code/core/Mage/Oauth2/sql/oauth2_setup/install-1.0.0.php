<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('oauth2/client'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ], 'Entity ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 200, [
        'nullable' => true,
    ], 'Name')
    ->addColumn('secret', Varien_Db_Ddl_Table::TYPE_VARCHAR, 80, [
        'nullable' => false,
    ], 'Secret')
    ->addColumn('redirect_uri', Varien_Db_Ddl_Table::TYPE_VARCHAR, 2000, [
        'nullable' => true,
    ], 'Redirect URI')
    ->addColumn('grant_types', Varien_Db_Ddl_Table::TYPE_VARCHAR, 2000, [
        'nullable' => true,
    ], 'Grant Types')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
    ], 'Updated At')
    ->addIndex(
        $installer->getIdxName('oauth2/client', ['created_at']),
        ['created_at']
    )
    ->setComment('OAuth2 Client Table');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('oauth2/auth_code'))
    ->addColumn('authorization_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, [
        'nullable' => false,
        'primary' => true,
    ], 'Authorization Code')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => true,
    ], 'Customer ID')
    ->addColumn('redirect_uri', Varien_Db_Ddl_Table::TYPE_VARCHAR, 2000, [
        'nullable' => true,
    ], 'Redirect URI')
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'unsigned' => true,
        'nullable' => false,
    ], 'Client ID')
    ->addColumn('expires_in', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable' => false,
    ], 'Expires In')
    ->addColumn('used', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, [
        'nullable' => false,
        'default' => false,
    ], 'Used')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName('oauth2/auth_code', ['created_at']),
        ['created_at']
    )
    ->addIndex(
        $installer->getIdxName('oauth2/auth_code', ['client_id']),
        ['client_id']
    )
    ->addIndex(
        $installer->getIdxName('oauth2/auth_code', ['customer_id']),
        ['customer_id']
    )
    ->addForeignKey(
        $installer->getFkName('oauth2/auth_code', 'client_id', 'oauth2/client', 'entity_id'),
        'client_id',
        $installer->getTable('oauth2/client'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->addForeignKey(
        $installer->getFkName('oauth2/auth_code', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('OAuth2 Authorization Code Table');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('oauth2/access_token'))
    ->addColumn('access_token', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, [
        'nullable' => false,
        'primary' => true,
    ], 'Access Token')
    ->addColumn('refresh_token', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, [
        'nullable' => true,
    ], 'Refresh Token')
    ->addColumn('admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => true,
    ], 'Admin ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => true,
    ], 'Customer ID')
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'unsigned' => true,
        'nullable' => false,
    ], 'Client ID')
    ->addColumn('expires_in', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable' => false,
    ], 'Expires In')
    ->addColumn('revoked', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, [
        'nullable' => false,
        'default' => false,
    ], 'Revoked')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName('oauth2/access_token', ['created_at']),
        ['created_at']
    )
    ->addIndex(
        $installer->getIdxName('oauth2/access_token', ['client_id']),
        ['client_id']
    )
    ->addIndex(
        $installer->getIdxName('oauth2/access_token', ['admin_id']),
        ['admin_id']
    )
    ->addIndex(
        $installer->getIdxName('oauth2/access_token', ['customer_id']),
        ['customer_id']
    )
    ->addForeignKey(
        $installer->getFkName('oauth2/access_token', 'client_id', 'oauth2/client', 'entity_id'),
        'client_id',
        $installer->getTable('oauth2/client'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->addForeignKey(
        $installer->getFkName('oauth2/access_token', 'admin_id', 'admin/user', 'user_id'),
        'admin_id',
        $installer->getTable('admin/user'),
        'user_id',
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->addForeignKey(
        $installer->getFkName('oauth2/access_token', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('OAuth2 Access Token Table');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('oauth2/device_code'))
    ->addColumn('device_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, [
        'nullable' => false,
        'primary' => true,
    ], 'Device Code')
    ->addColumn('admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => true,
    ], 'Admin ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => true,
    ], 'Customer ID')
    ->addColumn('user_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 8, [
        'nullable' => false,
    ], 'User Code')
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'unsigned' => true,
        'nullable' => false,
    ], 'Client ID')
    ->addColumn('expires_in', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable' => false,
    ], 'Expires In')
    ->addColumn('authorized', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, [
        'nullable' => false,
        'default' => false,
    ], 'Authorized')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName('oauth2/device_code', ['user_code']),
        ['user_code']
    )
    ->addForeignKey(
        $installer->getFkName('oauth2/device_code', 'admin_id', 'admin/user', 'user_id'),
        'admin_id',
        $installer->getTable('admin/user'),
        'user_id',
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->addForeignKey(
        $installer->getFkName('oauth2/device_code', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->addForeignKey(
        $installer->getFkName('oauth2/device_code', 'client_id', 'oauth2/client', 'entity_id'),
        'client_id',
        $installer->getTable('oauth2/client'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('OAuth2 Device Code Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
