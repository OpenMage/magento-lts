<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Tax_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('tax/tax_calculation'),
    'FK_TAX_CALCULATION_CTC'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tax/tax_calculation'),
    'FK_TAX_CALCULATION_PTC'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tax/tax_calculation'),
    'FK_TAX_CALCULATION_RATE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tax/tax_calculation'),
    'FK_TAX_CALCULATION_RULE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tax/tax_calculation_rate_title'),
    'FK_TAX_CALCULATION_RATE_TITLE_RATE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tax/tax_calculation_rate_title'),
    'FK_TAX_CALCULATION_RATE_TITLE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tax/tax_order_aggregated_created'),
    'FK_TAX_ORDER_AGGREGATED_CREATED_STORE'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation'),
    'FK_TAX_CALCULATION_RULE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation'),
    'FK_TAX_CALCULATION_RATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation'),
    'FK_TAX_CALCULATION_CTC'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation'),
    'FK_TAX_CALCULATION_PTC'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation'),
    'IDX_TAX_CALCULATION'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation_rate'),
    'IDX_TAX_CALCULATION_RATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation_rate'),
    'IDX_TAX_CALCULATION_RATE_CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation_rate'),
    'IDX_TAX_CALCULATION_RATE_RANGE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation_rate_title'),
    'IDX_TAX_CALCULATION_RATE_TITLE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation_rate_title'),
    'FK_TAX_CALCULATION_RATE_TITLE_RATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation_rate_title'),
    'FK_TAX_CALCULATION_RATE_TITLE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation_rule'),
    'IDX_TAX_CALCULATION_RULE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_calculation_rule'),
    'IDX_TAX_CALCULATION_RULE_CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_order_aggregated_created'),
    'UNQ_PERIOD_STORE_CODE_ORDER_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tax/tax_order_aggregated_created'),
    'IDX_STORE_ID'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('tax/tax_class') => [
        'columns' => [
            'class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Class Id'
            ],
            'class_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Class Name'
            ],
            'class_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 8,
                'nullable'  => false,
                'default'   => Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER,
                'comment'   => 'Class Type'
            ]
        ],
        'comment' => 'Tax Class'
    ],
    $installer->getTable('tax/tax_calculation') => [
        'columns' => [
            'tax_calculation_rate_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Tax Calculation Rate Id'
            ],
            'tax_calculation_rule_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Tax Calculation Rule Id'
            ],
            'customer_tax_class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'comment'   => 'Customer Tax Class Id'
            ],
            'product_tax_class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'comment'   => 'Product Tax Class Id'
            ]
        ],
        'comment' => 'Tax Calculation'
    ],
    $installer->getTable('tax/tax_calculation_rate') => [
        'columns' => [
            'tax_calculation_rate_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tax Calculation Rate Id'
            ],
            'tax_country_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'nullable'  => false,
                'comment'   => 'Tax Country Id'
            ],
            'tax_region_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Tax Region Id'
            ],
            'tax_postcode' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 21,
                'comment'   => 'Tax Postcode'
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Code'
            ],
            'rate' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Rate'
            ],
            'zip_is_range' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'comment'   => 'Zip Is Range'
            ],
            'zip_from' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Zip From'
            ],
            'zip_to' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Zip To'
            ]
        ],
        'comment' => 'Tax Calculation Rate'
    ],
    $installer->getTable('tax/tax_calculation_rate_title') => [
        'columns' => [
            'tax_calculation_rate_title_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tax Calculation Rate Title Id'
            ],
            'tax_calculation_rate_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Tax Calculation Rate Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Value'
            ]
        ],
        'comment' => 'Tax Calculation Rate Title'
    ],
    $installer->getTable('tax/tax_calculation_rule') => [
        'columns' => [
            'tax_calculation_rule_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tax Calculation Rule Id'
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Code'
            ],
            'priority' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Priority'
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Position'
            ]
        ],
        'comment' => 'Tax Calculation Rule'
    ],
    $installer->getTable('tax/tax_order_aggregated_created') => [
        'columns' => [
            'id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ],
            'period' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Period'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Code'
            ],
            'order_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Order Status'
            ],
            'percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_FLOAT,
                'comment'   => 'Percent'
            ],
            'orders_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Orders Count'
            ],
            'tax_base_amount_sum' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_FLOAT,
                'comment'   => 'Tax Base Amount Sum'
            ]
        ],
        'comment' => 'Tax Order Aggregation'
    ]
];

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->addColumn(
    $installer->getTable('tax/tax_calculation'),
    'tax_calculation_id',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        'comment'   => 'Tax Calculation Id'
    ]
);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation'),
    $installer->getIdxName('tax/tax_calculation', ['tax_calculation_rule_id']),
    ['tax_calculation_rule_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation'),
    $installer->getIdxName('tax/tax_calculation', ['tax_calculation_rate_id']),
    ['tax_calculation_rate_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation'),
    $installer->getIdxName('tax/tax_calculation', ['customer_tax_class_id']),
    ['customer_tax_class_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation'),
    $installer->getIdxName('tax/tax_calculation', ['product_tax_class_id']),
    ['product_tax_class_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation'),
    $installer->getIdxName('tax/tax_calculation', ['tax_calculation_rate_id', 'customer_tax_class_id', 'product_tax_class_id']),
    ['tax_calculation_rate_id', 'customer_tax_class_id', 'product_tax_class_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate'),
    $installer->getIdxName('tax/tax_calculation_rate', ['tax_country_id', 'tax_region_id', 'tax_postcode']),
    ['tax_country_id', 'tax_region_id', 'tax_postcode']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate'),
    $installer->getIdxName('tax/tax_calculation_rate', ['code']),
    ['code']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate'),
    $installer->getIdxName('tax/tax_calculation_rate', ['tax_calculation_rate_id', 'tax_country_id', 'tax_region_id', 'zip_is_range', 'tax_postcode']),
    ['tax_calculation_rate_id', 'tax_country_id', 'tax_region_id', 'zip_is_range', 'tax_postcode']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate_title'),
    $installer->getIdxName('tax/tax_calculation_rate_title', ['tax_calculation_rate_id', 'store_id']),
    ['tax_calculation_rate_id', 'store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate_title'),
    $installer->getIdxName('tax/tax_calculation_rate_title', ['tax_calculation_rate_id']),
    ['tax_calculation_rate_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate_title'),
    $installer->getIdxName('tax/tax_calculation_rate_title', ['store_id']),
    ['store_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rule'),
    $installer->getIdxName('tax/tax_calculation_rule', ['priority', 'position', 'tax_calculation_rule_id']),
    ['priority', 'position', 'tax_calculation_rule_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rule'),
    $installer->getIdxName('tax/tax_calculation_rule', ['code']),
    ['code']
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_order_aggregated_created'),
    $installer->getIdxName(
        'tax/tax_order_aggregated_created',
        ['period', 'store_id', 'code', 'percent', 'order_status'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['period', 'store_id', 'code', 'percent', 'order_status'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_order_aggregated_created'),
    $installer->getIdxName('tax/tax_order_aggregated_created', ['store_id']),
    ['store_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('tax/tax_calculation', 'product_tax_class_id', 'tax/tax_class', 'class_id'),
    $installer->getTable('tax/tax_calculation'),
    'product_tax_class_id',
    $installer->getTable('tax/tax_class'),
    'class_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tax/tax_calculation', 'customer_tax_class_id', 'tax/tax_class', 'class_id'),
    $installer->getTable('tax/tax_calculation'),
    'customer_tax_class_id',
    $installer->getTable('tax/tax_class'),
    'class_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tax/tax_calculation', 'tax_calculation_rate_id', 'tax/tax_calculation_rate', 'tax_calculation_rate_id'),
    $installer->getTable('tax/tax_calculation'),
    'tax_calculation_rate_id',
    $installer->getTable('tax/tax_calculation_rate'),
    'tax_calculation_rate_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tax/tax_calculation', 'tax_calculation_rule_id', 'tax/tax_calculation_rule', 'tax_calculation_rule_id'),
    $installer->getTable('tax/tax_calculation'),
    'tax_calculation_rule_id',
    $installer->getTable('tax/tax_calculation_rule'),
    'tax_calculation_rule_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tax/tax_calculation_rate_title', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('tax/tax_calculation_rate_title'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tax/tax_calculation_rate_title', 'tax_calculation_rate_id', 'tax/tax_calculation_rate', 'tax_calculation_rate_id'),
    $installer->getTable('tax/tax_calculation_rate_title'),
    'tax_calculation_rate_id',
    $installer->getTable('tax/tax_calculation_rate'),
    'tax_calculation_rate_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tax/tax_order_aggregated_created', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('tax/tax_order_aggregated_created'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);
$installer->endSetup();
