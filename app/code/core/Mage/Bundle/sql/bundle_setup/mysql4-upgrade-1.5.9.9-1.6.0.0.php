<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
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
    $installer->getTable('bundle/option'),
    'FK_CATALOG_PRODUCT_BUNDLE_OPTION_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/option_value'),
    'FK_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE_OPTION'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/price_index'),
    'FK_CATALOG_PRODUCT_BUNDLE_PRICE_INDEX_CUSTOMER_GROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/price_index'),
    'CATALOG_PRODUCT_BUNDLE_PRICE_INDEX_CUSTOMER_GROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/price_index'),
    'FK_CATALOG_PRODUCT_BUNDLE_PRICE_INDEX_PRODUCT_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/price_index'),
    'CATALOG_PRODUCT_BUNDLE_PRICE_INDEX_PRODUCT_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/price_index'),
    'FK_CATALOG_PRODUCT_BUNDLE_PRICE_INDEX_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/price_index'),
    'CATALOG_PRODUCT_BUNDLE_PRICE_INDEX_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/selection'),
    'FK_CATALOG_PRODUCT_BUNDLE_SELECTION_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/selection'),
    'FK_CATALOG_PRODUCT_BUNDLE_SELECTION_OPTION'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/selection_price'),
    'FK_BUNDLE_PRICE_SELECTION_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('bundle/selection_price'),
    'FK_BUNDLE_PRICE_SELECTION_WEBSITE'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('bundle/option'),
    'FK_CATALOG_PRODUCT_BUNDLE_OPTION_PARENT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('bundle/option_value'),
    'UNQ_OPTION_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('bundle/option_value'),
    'FK_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE_OPTION'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('bundle/price_index'),
    'IDX_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('bundle/price_index'),
    'IDX_CUSTOMER_GROUP'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('bundle/selection'),
    'FK_CATALOG_PRODUCT_BUNDLE_SELECTION_OPTION'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('bundle/selection'),
    'FK_CATALOG_PRODUCT_BUNDLE_SELECTION_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('bundle/selection_price'),
    'FK_BUNDLE_PRICE_SELECTION_WEBSITE'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('bundle/option') => [
        'columns' => [
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Id'
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Id'
            ],
            'required' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Required'
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position'
            ],
            'type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Type'
            ]
        ],
        'comment' => 'Catalog Product Bundle Option'
    ],
    $installer->getTable('bundle/option_value') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id'
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Option Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ],
            'title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Title'
            ]
        ],
        'comment' => 'Catalog Product Bundle Option Value'
    ],
    $installer->getTable('bundle/selection') => [
        'columns' => [
            'selection_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Selection Id'
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Option Id'
            ],
            'parent_product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Parent Product Id'
            ],
            'product_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product Id'
            ],
            'position' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position'
            ],
            'is_default' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Default'
            ],
            'selection_price_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Selection Price Type'
            ],
            'selection_price_value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Selection Price Value'
            ],
            'selection_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Selection Qty'
            ],
            'selection_can_change_qty' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Selection Can Change Qty'
            ]
        ],
        'comment' => 'Catalog Product Bundle Selection'
    ],
    $installer->getTable('bundle/selection_price') => [
        'columns' => [
            'selection_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Selection Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ],
            'selection_price_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Selection Price Type'
            ],
            'selection_price_value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Selection Price Value'
            ]
        ],
        'comment' => 'Catalog Product Bundle Selection Price'
    ],
    $installer->getTable('bundle/price_index') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Group Id'
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'comment'   => 'Min Price'
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'comment'   => 'Max Price'
            ]
        ],
        'comment' => 'Catalog Product Bundle Price Index'
    ],
    $installer->getTable('bundle/stock_index') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ],
            'stock_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Stock Id'
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Option Id'
            ],
            'stock_status' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'default'   => '0',
                'comment'   => 'Stock Status'
            ]
        ],
        'comment' => 'Catalog Product Bundle Stock Index'
    ],
    $installer->getTable('bundle/price_indexer_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Group Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ],
            'tax_class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class Id'
            ],
            'price_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Price Type'
            ],
            'special_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Special Price'
            ],
            'tier_percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Percent'
            ],
            'orig_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Orig Price'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            ],
            'base_tier' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tier'
            ]
        ],
        'comment' => 'Catalog Product Index Price Bundle Idx'
    ],
    $installer->getTable('bundle/price_indexer_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Group Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ],
            'tax_class_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class Id'
            ],
            'price_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Price Type'
            ],
            'special_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Special Price'
            ],
            'tier_percent' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Percent'
            ],
            'orig_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Orig Price'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            ],
            'base_tier' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tier'
            ]
        ],
        'comment' => 'Catalog Product Index Price Bundle Tmp',
        'engine'  => 'InnoDB'
    ],
    $installer->getTable('bundle/selection_indexer_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Group Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Option Id'
            ],
            'selection_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Selection Id'
            ],
            'group_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Group Type'
            ],
            'is_required' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Is Required'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            ]
        ],
        'comment' => 'Catalog Product Index Price Bundle Sel Idx'
    ],
    $installer->getTable('bundle/selection_indexer_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Group Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Option Id'
            ],
            'selection_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Selection Id'
            ],
            'group_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Group Type'
            ],
            'is_required' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Is Required'
            ],
            'price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            ]
        ],
        'comment' => 'Catalog Product Index Price Bundle Sel Tmp',
        'engine'  => 'InnoDB'
    ],
    $installer->getTable('bundle/option_indexer_idx') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Group Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Option Id'
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ],
            'alt_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Alt Price'
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            ],
            'alt_tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Alt Tier Price'
            ]
        ],
        'comment' => 'Catalog Product Index Price Bundle Opt Idx'
    ],
    $installer->getTable('bundle/option_indexer_tmp') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Group Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Option Id'
            ],
            'min_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ],
            'alt_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Alt Price'
            ],
            'max_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ],
            'tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            ],
            'alt_tier_price' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Alt Tier Price'
            ]
        ],
        'comment' => 'Catalog Product Index Price Bundle Opt Tmp',
        'engine'  => 'InnoDB'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('bundle/option'),
    $installer->getIdxName('bundle/option', ['parent_id']),
    ['parent_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('bundle/option_value'),
    $installer->getIdxName(
        'bundle/option_value',
        ['option_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['option_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('bundle/price_index'),
    $installer->getIdxName('bundle/price_index', ['website_id']),
    ['website_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('bundle/price_index'),
    $installer->getIdxName('bundle/price_index', ['customer_group_id']),
    ['customer_group_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('bundle/selection'),
    $installer->getIdxName('bundle/selection', ['option_id']),
    ['option_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('bundle/selection'),
    $installer->getIdxName('bundle/selection', ['product_id']),
    ['product_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('bundle/selection_price'),
    $installer->getIdxName('bundle/selection_price', ['website_id']),
    ['website_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('bundle/selection', 'option_id', 'bundle/option', 'option_id'),
    $installer->getTable('bundle/selection'),
    'option_id',
    $installer->getTable('bundle/option'),
    'option_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('bundle/option', 'parent_id', 'catalog/product', 'entity_id'),
    $installer->getTable('bundle/option'),
    'parent_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('bundle/option_value', 'option_id', 'bundle/option', 'option_id'),
    $installer->getTable('bundle/option_value'),
    'option_id',
    $installer->getTable('bundle/option'),
    'option_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('bundle/price_index', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
    $installer->getTable('bundle/price_index'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('bundle/price_index', 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable('bundle/price_index'),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('bundle/price_index', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('bundle/price_index'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('bundle/selection', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('bundle/selection'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('bundle/selection_price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('bundle/selection_price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('bundle/selection_price', 'selection_id', 'bundle/selection', 'selection_id'),
    $installer->getTable('bundle/selection_price'),
    'selection_id',
    $installer->getTable('bundle/selection'),
    'selection_id'
);

$installer->endSetup();
