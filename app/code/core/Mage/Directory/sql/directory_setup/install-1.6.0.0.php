<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'directory/country'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('directory/country'))
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 2, [
        'nullable'  => false,
        'primary'   => true,
        'default'   => '',
    ], 'Country Id in ISO-2')
    ->addColumn('iso2_code', Varien_Db_Ddl_Table::TYPE_TEXT, 2, [
        'nullable'  => true,
        'default'   => null,
    ], 'Country ISO-2 format')
    ->addColumn('iso3_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
        'nullable'  => true,
        'default'   => null,
    ], 'Country ISO-3')
    ->setComment('Directory Country');
$installer->getConnection()->createTable($table);

/**
 * Create table 'directory/country_format'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('directory/country_format'))
    ->addColumn('country_format_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Country Format Id')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 2, [
        'nullable'  => true,
        'default'   => null,
    ], 'Country Id in ISO-2')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 30, [
        'nullable'  => true,
        'default'   => null,
    ], 'Country Format Type')
    ->addColumn('format', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => false,
    ], 'Country Format')
    ->addIndex(
        $installer->getIdxName(
            'directory/country_format',
            ['country_id', 'type'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['country_id', 'type'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
     ->setComment('Directory Country Format');
$installer->getConnection()->createTable($table);

/**
 * Create table 'directory/country_region'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('directory/country_region'))
    ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Region Id')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 4, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Country Id in ISO-2')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => true,
        'default'   => null,
    ], 'Region code')
    ->addColumn('default_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Region Name')
    ->addIndex(
        $installer->getIdxName('directory/country_region', ['country_id']),
        ['country_id']
    )
    ->setComment('Directory Country Region');
$installer->getConnection()->createTable($table);

/**
 * Create table 'directory/country_region_name'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('directory/country_region_name'))
    ->addColumn('locale', Varien_Db_Ddl_Table::TYPE_TEXT, 8, [
        'nullable'  => false,
        'primary'   => true,
        'default'   => '',
    ], 'Locale')
    ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Region Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Region Name')
    ->addIndex(
        $installer->getIdxName('directory/country_region_name', ['region_id']),
        ['region_id']
    )
    ->addForeignKey(
        $installer->getFkName('directory/country_region_name', 'region_id', 'directory/country_region', 'region_id'),
        'region_id',
        $installer->getTable('directory/country_region'),
        'region_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Directory Country Region Name');
$installer->getConnection()->createTable($table);

/**
 * Create table 'directory/currency_rate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('directory/currency_rate'))
    ->addColumn('currency_from', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
        'nullable'  => false,
        'primary'   => true,
        'default'   => '',
    ], 'Currency Code Convert From')
    ->addColumn('currency_to', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
        'nullable'  => false,
        'primary'   => true,
        'default'   => '',
    ], 'Currency Code Convert To')
    ->addColumn('rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '24,12', [
        'nullable'  => false,
        'default'   => '0.000000000000',
    ], 'Currency Conversion Rate')
    ->addIndex(
        $installer->getIdxName('directory/currency_rate', ['currency_to']),
        ['currency_to']
    )
    ->setComment('Directory Currency Rate');
$installer->getConnection()->createTable($table);

$installer->endSetup();
