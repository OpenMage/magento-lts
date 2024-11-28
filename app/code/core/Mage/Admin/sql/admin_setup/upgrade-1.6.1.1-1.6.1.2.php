<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('admin/permission_variable'))
    ->addColumn('variable_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Variable ID')
    ->addColumn('variable_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'primary'   => true,
        'nullable'  => false,
        'default'   => '',
    ], 'Config Path')
    ->addColumn('is_allowed', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, [
        'nullable'  => false,
        'default'   => 0,
    ], 'Mark that config can be processed by filters')
    ->addIndex(
        $installer->getIdxName('admin/permission_variable', ['variable_name'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['variable_name'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('System variables that can be processed via content filter');
$installer->getConnection()->createTable($table);

$installer->getConnection()->insertMultiple(
    $installer->getTable('admin/permission_variable'),
    [
        ['variable_name' => 'trans_email/ident_support/name', 'is_allowed' => 1],
        ['variable_name' => 'trans_email/ident_support/email','is_allowed' =>  1],
        ['variable_name' => 'web/unsecure/base_url','is_allowed' =>  1],
        ['variable_name' => 'web/secure/base_url','is_allowed' =>  1],
        ['variable_name' => 'trans_email/ident_general/name','is_allowed' =>  1],
        ['variable_name' => 'trans_email/ident_general/email', 'is_allowed' => 1],
        ['variable_name' => 'trans_email/ident_sales/name','is_allowed' =>  1],
        ['variable_name' => 'trans_email/ident_sales/email','is_allowed' =>  1],
        ['variable_name' => 'trans_email/ident_custom1/name','is_allowed' =>  1],
        ['variable_name' => 'trans_email/ident_custom1/email','is_allowed' =>  1],
        ['variable_name' => 'trans_email/ident_custom2/name','is_allowed' =>  1],
        ['variable_name' => 'trans_email/ident_custom2/email','is_allowed' =>  1],
        ['variable_name' => 'general/store_information/name', 'is_allowed' => 1],
        ['variable_name' => 'general/store_information/phone','is_allowed'  => 1],
        ['variable_name' => 'general/store_information/address', 'is_allowed' => 1],
    ]
);

$table = $installer->getConnection()
    ->newTable($installer->getTable('admin/permission_block'))
    ->addColumn('block_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Block ID')
    ->addColumn('block_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable'  => false,
        'default'   => '',
    ], 'Block Name')
    ->addColumn('is_allowed', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, [
        'nullable'  => false,
        'default'   => 0,
    ], 'Mark that block can be processed by filters')
    ->addIndex(
        $installer->getIdxName('admin/permission_block', ['block_name'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['block_name'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('System blocks that can be processed via content filter');
$installer->getConnection()->createTable($table);

$installer->getConnection()->insertMultiple(
    $installer->getTable('admin/permission_block'),
    [
        ['block_name' => 'core/template', 'is_allowed' => 1],
        ['block_name' => 'catalog/product_new', 'is_allowed' => 1],
    ]
);

$installer->endSetup();
