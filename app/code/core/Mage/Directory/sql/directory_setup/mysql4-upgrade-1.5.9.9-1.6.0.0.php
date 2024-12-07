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
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('directory/country_region_name'),
    'FK_DIRECTORY_REGION_NAME_REGION'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('directory/country_format'),
    'COUNTRY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('directory/country_region'),
    'FK_REGION_COUNTRY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('directory/country_region_name'),
    'FK_DIRECTORY_REGION_NAME_REGION'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('directory/currency_rate'),
    'FK_CURRENCY_RATE_TO'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('directory/country') => [
        'columns' => [
            'country_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Country Id in ISO-2'
            ],
            'iso2_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'nullable'  => false,
                'comment'   => 'Country ISO-2 format'
            ],
            'iso3_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'nullable'  => false,
                'comment'   => 'Country ISO-3'
            ]
        ],
        'comment' => 'Directory Country'
    ],
    $installer->getTable('directory/country_format') => [
        'columns' => [
            'country_format_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Country Format Id'
            ],
            'country_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'nullable'  => false,
                'comment'   => 'Country Id in ISO-2'
            ],
            'type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 30,
                'nullable'  => false,
                'comment'   => 'Country Format Type'
            ],
            'format' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Country Format'
            ]
        ],
        'comment' => 'Directory Country Format'
    ],
    $installer->getTable('directory/country_region') => [
        'columns' => [
            'region_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Region Id'
            ],
            'country_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 4,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Country Id in ISO-2'
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Region code'
            ],
            'default_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Region Name'
            ]
        ],
        'comment' => 'Directory Country Region'
    ],
    $installer->getTable('directory/country_region_name') => [
        'columns' => [
            'locale' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 8,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Locale'
            ],
            'region_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Region Id'
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Region Name'
            ]
        ],
        'comment' => 'Directory Country Region Name'
    ],
    $installer->getTable('directory/currency_rate') => [
        'columns' => [
            'currency_from' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Currency Code Convert From'
            ],
            'currency_to' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 3,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Currency Code Convert To'
            ],
            'rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 12,
                'precision' => 24,
                'nullable'  => false,
                'default'   => '0.000000000000',
                'comment'   => 'Currency Conversion Rate'
            ]
        ],
        'comment' => 'Directory Currency Rate'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('directory/country_format'),
    $installer->getIdxName(
        'directory/country_format',
        ['country_id', 'type'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['country_id', 'type'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('directory/country_region'),
    $installer->getIdxName('directory/country_region', ['country_id']),
    ['country_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('directory/country_region_name'),
    $installer->getIdxName('directory/country_region_name', ['region_id']),
    ['region_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('directory/currency_rate'),
    $installer->getIdxName('directory/currency_rate', ['currency_to']),
    ['currency_to']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('directory/country_region_name', 'region_id', 'directory/country_region', 'region_id'),
    $installer->getTable('directory/country_region_name'),
    'region_id',
    $installer->getTable('directory/country_region'),
    'region_id'
);

$installer->endSetup();
