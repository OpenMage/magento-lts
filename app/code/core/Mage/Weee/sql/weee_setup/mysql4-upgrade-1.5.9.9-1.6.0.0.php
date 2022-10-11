<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Weee_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('weee/discount'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_DISCOUNT_GROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('weee/discount'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_DISCOUNT_PRODUCT_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('weee/discount'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_DISCOUNT_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('weee/tax'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_COUNTRY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('weee/tax'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_PRODUCT_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('weee/tax'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('weee/tax'),
    'FK_WEEE_TAX_ATTRIBUTE_ID'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('weee/discount'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_DISCOUNT_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('weee/discount'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_DISCOUNT_PRODUCT_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('weee/discount'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_DISCOUNT_GROUP'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('weee/tax'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('weee/tax'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_PRODUCT_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('weee/tax'),
    'FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_COUNTRY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('weee/tax'),
    'FK_WEEE_TAX_ATTRIBUTE_ID'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('weee/tax') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website Id'
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Id'
            ],
            'country' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'comment'   => 'Country'
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Value'
            ],
            'state' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'default'   => '*',
                'comment'   => 'State'
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id'
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id'
            ]
        ],
        'comment' => 'Weee Tax'
    ],
    $installer->getTable('weee/discount') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Id'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website Id'
            ],
            'customer_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Customer Group Id'
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Value'
            ]
        ],
        'comment' => 'Weee Discount'
    ],
    $installer->getTable('sales/order_item') => [
        'columns' => [
            'weee_tax_applied' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Weee Tax Applied'
            ],
            'weee_tax_applied_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Amount'
            ],
            'weee_tax_applied_row_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Row Amount'
            ],
            'base_weee_tax_applied_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Applied Amount'
            ],
            'weee_tax_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Disposition'
            ],
            'weee_tax_row_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Row Disposition'
            ],
            'base_weee_tax_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Disposition'
            ],
            'base_weee_tax_row_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Row Disposition'
            ]
        ]
    ],
    $installer->getTable('sales/quote_item') => [
        'columns' => [
            'weee_tax_applied' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Weee Tax Applied'
            ],
            'weee_tax_applied_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Amount'
            ],
            'weee_tax_applied_row_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Row Amount'
            ],
            'base_weee_tax_applied_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Applied Amount'
            ],
            'weee_tax_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Disposition'
            ],
            'weee_tax_row_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Row Disposition'
            ],
            'base_weee_tax_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Disposition'
            ],
            'base_weee_tax_row_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Row Disposition'
            ]
        ]
    ],
    $installer->getTable('sales/invoice_item') => [
        'columns' => [
            'weee_tax_applied' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Weee Tax Applied'
            ],
            'weee_tax_applied_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Amount'
            ],
            'weee_tax_applied_row_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Row Amount'
            ],
            'base_weee_tax_applied_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Applied Amount'
            ],
            'weee_tax_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Disposition'
            ],
            'weee_tax_row_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Row Disposition'
            ],
            'base_weee_tax_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Disposition'
            ],
            'base_weee_tax_row_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Row Disposition'
            ]
        ]
    ],
    $installer->getTable('sales/creditmemo_item') => [
        'columns' => [
            'weee_tax_applied' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Weee Tax Applied'
            ],
            'weee_tax_applied_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Amount'
            ],
            'weee_tax_applied_row_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Row Amount'
            ],
            'base_weee_tax_applied_amount' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Applied Amount'
            ],
            'weee_tax_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Disposition'
            ],
            'weee_tax_row_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Row Disposition'
            ],
            'base_weee_tax_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Disposition'
            ],
            'base_weee_tax_row_disposition' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Row Disposition'
            ]
        ]
    ]
];

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/order_item'),
    'base_weee_tax_applied_row_amount',
    'base_weee_tax_applied_row_amnt',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Weee Tax Applied Row Amnt'
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/quote_item'),
    'base_weee_tax_applied_row_amount',
    'base_weee_tax_applied_row_amnt',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Weee Tax Applied Row Amnt'
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/invoice_item'),
    'base_weee_tax_applied_row_amount',
    'base_weee_tax_applied_row_amnt',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Weee Tax Applied Row Amnt'
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/creditmemo_item'),
    'base_weee_tax_applied_row_amount',
    'base_weee_tax_applied_row_amnt',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Weee Tax Applied Row Amnt'
    ]
);
/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('weee/discount'),
    $installer->getIdxName('weee/discount', ['website_id']),
    ['website_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/discount'),
    $installer->getIdxName('weee/discount', ['entity_id']),
    ['entity_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/discount'),
    $installer->getIdxName('weee/discount', ['customer_group_id']),
    ['customer_group_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/tax'),
    $installer->getIdxName('weee/tax', ['website_id']),
    ['website_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/tax'),
    $installer->getIdxName('weee/tax', ['entity_id']),
    ['entity_id']
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/tax'),
    $installer->getIdxName('weee/tax', ['country']),
    ['country']
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/tax'),
    $installer->getIdxName('weee/tax', ['attribute_id']),
    ['attribute_id']
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('weee/discount', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
    $installer->getTable('weee/discount'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('weee/discount', 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable('weee/discount'),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('weee/discount', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('weee/discount'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('weee/tax', 'country', 'directory/country', 'country_id'),
    $installer->getTable('weee/tax'),
    'country',
    $installer->getTable('directory/country'),
    'country_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('weee/tax', 'entity_id', 'catalog/product', 'entity_id'),
    $installer->getTable('weee/tax'),
    'entity_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('weee/tax', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('weee/tax'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('weee/tax', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('weee/tax'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->endSetup();
