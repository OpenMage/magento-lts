<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Weee_Model_Resource_Setup */
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
$tables = array(
    $installer->getTable('weee/tax') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website Id'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Id'
            ),
            'country' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'comment'   => 'Country'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Value'
            ),
            'state' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'default'   => '*',
                'comment'   => 'State'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id'
            )
        ),
        'comment' => 'Weee Tax'
    ),
    $installer->getTable('weee/discount') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Id'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website Id'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Customer Group Id'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Weee Discount'
    ),
    $installer->getTable('sales/order_item') => array(
        'columns' => array(
            'weee_tax_applied' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Weee Tax Applied'
            ),
            'weee_tax_applied_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Amount'
            ),
            'weee_tax_applied_row_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Row Amount'
            ),
            'base_weee_tax_applied_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Applied Amount'
            ),
            'weee_tax_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Disposition'
            ),
            'weee_tax_row_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Row Disposition'
            ),
            'base_weee_tax_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Disposition'
            ),
            'base_weee_tax_row_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Row Disposition'
            )
        )
    ),
    $installer->getTable('sales/quote_item') => array(
        'columns' => array(
            'weee_tax_applied' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Weee Tax Applied'
            ),
            'weee_tax_applied_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Amount'
            ),
            'weee_tax_applied_row_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Row Amount'
            ),
            'base_weee_tax_applied_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Applied Amount'
            ),
            'weee_tax_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Disposition'
            ),
            'weee_tax_row_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Row Disposition'
            ),
            'base_weee_tax_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Disposition'
            ),
            'base_weee_tax_row_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Row Disposition'
            )
        )
    ),
    $installer->getTable('sales/invoice_item') => array(
        'columns' => array(
            'weee_tax_applied' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Weee Tax Applied'
            ),
            'weee_tax_applied_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Amount'
            ),
            'weee_tax_applied_row_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Row Amount'
            ),
            'base_weee_tax_applied_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Applied Amount'
            ),
            'weee_tax_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Disposition'
            ),
            'weee_tax_row_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Row Disposition'
            ),
            'base_weee_tax_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Disposition'
            ),
            'base_weee_tax_row_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Row Disposition'
            )
        )
    ),
    $installer->getTable('sales/creditmemo_item') => array(
        'columns' => array(
            'weee_tax_applied' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Weee Tax Applied'
            ),
            'weee_tax_applied_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Amount'
            ),
            'weee_tax_applied_row_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Applied Row Amount'
            ),
            'base_weee_tax_applied_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Applied Amount'
            ),
            'weee_tax_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Disposition'
            ),
            'weee_tax_row_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Weee Tax Row Disposition'
            ),
            'base_weee_tax_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Disposition'
            ),
            'base_weee_tax_row_disposition' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Weee Tax Row Disposition'
            )
        )
    )
);

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/order_item'),
    'base_weee_tax_applied_row_amount',
    'base_weee_tax_applied_row_amnt',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Weee Tax Applied Row Amnt'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/quote_item'),
    'base_weee_tax_applied_row_amount',
    'base_weee_tax_applied_row_amnt',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Weee Tax Applied Row Amnt'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/invoice_item'),
    'base_weee_tax_applied_row_amount',
    'base_weee_tax_applied_row_amnt',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Weee Tax Applied Row Amnt'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/creditmemo_item'),
    'base_weee_tax_applied_row_amount',
    'base_weee_tax_applied_row_amnt',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'scale'     => 4,
        'precision' => 12,
        'comment'   => 'Base Weee Tax Applied Row Amnt'
    )
);
/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('weee/discount'),
    $installer->getIdxName('weee/discount', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/discount'),
    $installer->getIdxName('weee/discount', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/discount'),
    $installer->getIdxName('weee/discount', array('customer_group_id')),
    array('customer_group_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/tax'),
    $installer->getIdxName('weee/tax', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/tax'),
    $installer->getIdxName('weee/tax', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/tax'),
    $installer->getIdxName('weee/tax', array('country')),
    array('country')
);

$installer->getConnection()->addIndex(
    $installer->getTable('weee/tax'),
    $installer->getIdxName('weee/tax', array('attribute_id')),
    array('attribute_id')
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
