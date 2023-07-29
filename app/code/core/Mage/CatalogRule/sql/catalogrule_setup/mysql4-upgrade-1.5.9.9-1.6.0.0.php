<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogrule/rule_group_website'),
    'FK_CATALOGRULE_GROUP_WEBSITE_GROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogrule/rule_group_website'),
    'FK_CATALOGRULE_GROUP_WEBSITE_RULE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogrule/rule_group_website'),
    'FK_CATALOGRULE_GROUP_WEBSITE_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogrule/rule_product'),
    'FK_CATALOGRULE_PRODUCT_CUSTOMERGROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogrule/rule_product'),
    'FK_CATALOGRULE_PRODUCT_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogrule/rule_product'),
    'FK_CATALOGRULE_PRODUCT_RULE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogrule/rule_product'),
    'FK_CATALOGRULE_PRODUCT_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogrule/rule_product_price'),
    'FK_CATALOGRULE_PRODUCT_PRICE_CUSTOMERGROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogrule/rule_product_price'),
    'FK_CATALOGRULE_PRODUCT_PRICE_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalogrule/rule_product_price'),
    'FK_CATALOGRULE_PRODUCT_PRICE_WEBSITE'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule'),
    'SORT_ORDER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_group_website'),
    'RULE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_group_website'),
    'CUSTOMER_GROUP_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_group_website'),
    'WEBSITE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product'),
    'SORT_ORDER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product'),
    'FK_CATALOGRULE_PRODUCT_RULE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product'),
    'FK_CATALOGRULE_PRODUCT_CUSTOMERGROUP'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product'),
    'FK_CATALOGRULE_PRODUCT_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product'),
    'FK_CATALOGRULE_PRODUCT_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product'),
    'IDX_FROM_TIME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product'),
    'IDX_TO_TIME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product_price'),
    'RULE_DATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product_price'),
    'FK_CATALOGRULE_PRODUCT_PRICE_CUSTOMERGROUP'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product_price'),
    'FK_CATALOGRULE_PRODUCT_PRICE_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalogrule/rule_product_price'),
    'FK_CATALOGRULE_PRODUCT_PRICE_PRODUCT'
);

/*
 * Change columns
 */
$tables = [
    $installer->getTable('catalogrule/rule') => [
        'columns' => [
            'rule_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rule Id'
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ],
            'description' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ],
            'from_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'From Date'
            ],
            'to_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'To Date'
            ],
            'customer_group_ids' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Customer Group Ids'
            ],
            'is_active' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Active'
            ],
            'conditions_serialized' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'Conditions Serialized'
            ],
            'actions_serialized' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'Actions Serialized'
            ],
            'stop_rules_processing' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Stop Rules Processing'
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            ],
            'simple_action' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Simple Action'
            ],
            'discount_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ],
            'website_ids' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Website Ids'
            ]
        ],
        'comment' => 'CatalogRule'
    ],
    $installer->getTable('catalogrule/rule_product') => [
        'columns' => [
            'rule_product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rule Product Id'
            ],
            'rule_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Rule Id'
            ],
            'from_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'From Time'
            ],
            'to_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'To time'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer Group Id'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product Id'
            ],
            'action_operator' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 10,
                'default'   => 'to_fixed',
                'comment'   => 'Action Operator'
            ],
            'action_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Action Amount'
            ],
            'action_stop' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Action Stop'
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Website Id'
            ]
        ],
        'comment' => 'CatalogRule Product'
    ],
    $installer->getTable('catalogrule/rule_product_price') => [
        'columns' => [
            'rule_product_price_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rule Product PriceId'
            ],
            'rule_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'nullable'  => false,
                'default'   => '0000-00-00',
                'comment'   => 'Rule Date'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer Group Id'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product Id'
            ],
            'rule_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Rule Price'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Website Id'
            ],
            'latest_start_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Latest StartDate'
            ],
            'earliest_end_date' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Earliest EndDate'
            ]
        ],
        'comment' => 'CatalogRule Product Price'
    ],
    $installer->getTable('catalogrule/affected_product') => [
        'columns' => [
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product Id'
            ]
        ],
        'comment' => 'CatalogRule Affected Product'
    ],
    $installer->getTable('catalogrule/rule_group_website') => [
        'columns' => [
            'rule_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Rule Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website Id'
            ]
        ],
        'comment' => 'CatalogRule Group Website'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule'),
    $installer->getIdxName('catalogrule/rule', ['is_active', 'sort_order', 'to_date', 'from_date']),
    ['is_active', 'sort_order', 'to_date', 'from_date']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_group_website'),
    $installer->getIdxName('catalogrule/rule_group_website', ['rule_id']),
    ['rule_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_group_website'),
    $installer->getIdxName('catalogrule/rule_group_website', ['customer_group_id']),
    ['customer_group_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_group_website'),
    $installer->getIdxName('catalogrule/rule_group_website', ['website_id']),
    ['website_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product'),
    $installer->getIdxName(
        'catalogrule/rule_product',
        ['rule_id', 'from_time', 'to_time', 'website_id', 'customer_group_id', 'product_id', 'sort_order'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['rule_id', 'from_time', 'to_time', 'website_id', 'customer_group_id', 'product_id', 'sort_order'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product'),
    $installer->getIdxName('catalogrule/rule_product', ['rule_id']),
    ['rule_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product'),
    $installer->getIdxName('catalogrule/rule_product', ['customer_group_id']),
    ['customer_group_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product'),
    $installer->getIdxName('catalogrule/rule_product', ['website_id']),
    ['website_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product'),
    $installer->getIdxName('catalogrule/rule_product', ['from_time']),
    ['from_time']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product'),
    $installer->getIdxName('catalogrule/rule_product', ['to_time']),
    ['to_time']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product'),
    $installer->getIdxName('catalogrule/rule_product', ['product_id']),
    ['product_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product_price'),
    $installer->getIdxName(
        'catalogrule/rule_product_price',
        ['rule_date', 'website_id', 'customer_group_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['rule_date', 'website_id', 'customer_group_id', 'product_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product_price'),
    $installer->getIdxName('catalogrule/rule_product_price', ['customer_group_id']),
    ['customer_group_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product_price'),
    $installer->getIdxName('catalogrule/rule_product_price', ['website_id']),
    ['website_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('catalogrule/rule_product_price'),
    $installer->getIdxName('catalogrule/rule_product_price', ['product_id']),
    ['product_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogrule/rule_group_website', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
    $installer->getTable('catalogrule/rule_group_website'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogrule/rule_group_website', 'rule_id', 'catalogrule/rule', 'rule_id'),
    $installer->getTable('catalogrule/rule_group_website'),
    'rule_id',
    $installer->getTable('catalogrule/rule'),
    'rule_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogrule/rule_group_website', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('catalogrule/rule_group_website'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogrule/rule_product', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalogrule/rule_product'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogrule/rule_product', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
    $installer->getTable('catalogrule/rule_product'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogrule/rule_product', 'rule_id', 'catalogrule/rule', 'rule_id'),
    $installer->getTable('catalogrule/rule_product'),
    'rule_id',
    $installer->getTable('catalogrule/rule'),
    'rule_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogrule/rule_product', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('catalogrule/rule_product'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogrule/rule_product_price', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('catalogrule/rule_product_price'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogrule/rule_product_price', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
    $installer->getTable('catalogrule/rule_product_price'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('catalogrule/rule_product_price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('catalogrule/rule_product_price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->endSetup();
