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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/coupon_aggregated'),
    'FK_SALESTRULE_COUPON_AGGREGATED_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/coupon_aggregated_order'),
    'FK_SALESTRULE_COUPON_AGGREGATED_ORDER_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/coupon'),
    'FK_SALESRULE_COUPON_RULE_ID_SALESRULE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/coupon_usage'),
    'FK_SALESRULE_CPN_CUST_CPN_ID_CUST_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/coupon_usage'),
    'FK_SALESRULE_CPN_CUST_CUST_ID_CUST_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/rule_customer'),
    'FK_SALESRULE_CUSTOMER_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/rule_customer'),
    'FK_SALESRULE_CUSTOMER_RULE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/label'),
    'FK_SALESRULE_LABEL_RULE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/label'),
    'FK_SALESRULE_LABEL_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/product_attribute'),
    'FK_SALESRULE_PRODUCT_ATTRIBUTE_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/product_attribute'),
    'FK_SALESRULE_PRODUCT_ATTRIBUTE_CUSTOMER_GROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/product_attribute'),
    'FK_SALESRULE_PRODUCT_ATTRIBUTE_RULE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/product_attribute'),
    'FK_SALESRULE_PRODUCT_ATTRIBUTE_WEBSITE'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/coupon_aggregated'),
    'UNQ_COUPON_AGGREGATED_PSOC'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/coupon_aggregated'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/coupon_aggregated_order'),
    'UNQ_COUPON_AGGREGATED_ORDER_PSOC'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/coupon_aggregated_order'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/rule'),
    'SORT_ORDER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/coupon'),
    'UNQ_COUPON_CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/coupon'),
    'UNQ_RULE_MAIN_COUPON'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/coupon'),
    'FK_SALESRULE_COUPON_RULE_ID_SALESRULE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/coupon_usage'),
    'FK_SALESRULE_COUPON_CUSTOMER_COUPON_ID_CUSTOMER_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/coupon_usage'),
    'FK_SALESRULE_COUPON_CUSTOMER_CUSTOMER_ID_CUSTOMER_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/rule_customer'),
    'RULE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/rule_customer'),
    'CUSTOMER_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/label'),
    'IDX_RULE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/label'),
    'IDX_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/label'),
    'IDX_RULE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/label'),
    'UNQ_RULE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/label'),
    'FK_SALESRULE_LABEL_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/label'),
    'FK_SALESRULE_LABEL_RULE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/product_attribute'),
    'IDX_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/product_attribute'),
    'IDX_CUSTOMER_GROUP'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('salesrule/product_attribute'),
    'IDX_ATTRIBUTE'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('salesrule/rule') => array(
        'columns' => array(
            'rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rule Id'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ),
            'from_date' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'From Date'
            ),
            'to_date' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'To Date'
            ),
            'uses_per_customer' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Uses Per Customer'
            ),
            'customer_group_ids' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Customer Group Ids'
            ),
            'is_active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Active'
            ),
            'conditions_serialized' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'Conditions Serialized'
            ),
            'actions_serialized' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '2M',
                'comment'   => 'Actions Serialized'
            ),
            'stop_rules_processing' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Stop Rules Processing'
            ),
            'is_advanced' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Advanced'
            ),
            'product_ids' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Product Ids'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            ),
            'simple_action' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Simple Action'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ),
            'discount_qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Discount Qty'
            ),
            'discount_step' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Discount Step'
            ),
            'simple_free_shipping' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Simple Free Shipping'
            ),
            'apply_to_shipping' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Apply To Shipping'
            ),
            'times_used' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Times Used'
            ),
            'is_rss' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Rss'
            ),
            'website_ids' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Website Ids'
            ),
            'coupon_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Coupon Type'
            )
        ),
        'comment' => 'Salesrule'
    ),
    $installer->getTable('salesrule/rule_customer') => array(
        'columns' => array(
            'rule_customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rule Customer Id'
            ),
            'rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Rule Id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer Id'
            ),
            'times_used' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Times Used'
            )
        ),
        'comment' => 'Salesrule Customer'
    ),
    $installer->getTable('salesrule/label') => array(
        'columns' => array(
            'label_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Label Id'
            ),
            'rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Rule Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ),
            'label' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Label'
            )
        ),
        'comment' => 'Salesrule Label'
    ),
    $installer->getTable('salesrule/coupon') => array(
        'columns' => array(
            'coupon_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Coupon Id'
            ),
            'rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Rule Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Code'
            ),
            'usage_limit' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Usage Limit'
            ),
            'usage_per_customer' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Usage Per Customer'
            ),
            'times_used' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Times Used'
            ),
            'expiration_date' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Expiration Date'
            ),
            'is_primary' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Is Primary'
            )
        ),
        'comment' => 'Salesrule Coupon'
    ),
    $installer->getTable('salesrule/coupon_usage') => array(
        'columns' => array(
            'coupon_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Coupon Id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Id'
            ),
            'times_used' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Times Used'
            )
        ),
        'comment' => 'Salesrule Coupon Usage'
    ),
    $installer->getTable('salesrule/coupon_aggregated') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'nullable'  => false,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'order_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Status'
            ),
            'coupon_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Coupon Code'
            ),
            'coupon_uses' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Coupon Uses'
            ),
            'subtotal_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Subtotal Amount'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ),
            'total_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Amount'
            ),
            'subtotal_amount_actual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Subtotal Amount Actual'
            ),
            'discount_amount_actual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount Actual'
            ),
            'total_amount_actual' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Amount Actual'
            )
        ),
        'comment' => 'Coupon Aggregated'
    ),
    $installer->getTable('salesrule/coupon_aggregated_order') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'period' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'nullable'  => false,
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'order_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Order Status'
            ),
            'coupon_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Coupon Code'
            ),
            'coupon_uses' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Coupon Uses'
            ),
            'subtotal_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Subtotal Amount'
            ),
            'discount_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Discount Amount'
            ),
            'total_amount' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Total Amount'
            )
        ),
        'comment' => 'Coupon Aggregated Order'
    ),
    $installer->getTable('salesrule/product_attribute') => array(
        'columns' => array(
            'rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Rule Id'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Website Id'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Customer Group Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Attribute Id'
            )
        ),
        'comment' => 'Salesrule Product Attribute'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/coupon_aggregated'),
    $installer->getIdxName(
        'salesrule/coupon_aggregated',
        array('period', 'store_id', 'order_status', 'coupon_code'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'order_status', 'coupon_code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/coupon_aggregated'),
    $installer->getIdxName('salesrule/coupon_aggregated', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/coupon_aggregated_order'),
    $installer->getIdxName(
        'salesrule/coupon_aggregated_order',
        array('period', 'store_id', 'order_status', 'coupon_code'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'order_status', 'coupon_code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/coupon_aggregated_order'),
    $installer->getIdxName('salesrule/coupon_aggregated_order', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/rule'),
    $installer->getIdxName('salesrule/rule', array('is_active', 'sort_order', 'to_date', 'from_date')),
    array('is_active', 'sort_order', 'to_date', 'from_date')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/coupon'),
    $installer->getIdxName(
        'salesrule/coupon',
        array('code'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/coupon'),
    $installer->getIdxName(
        'salesrule/coupon',
        array('rule_id', 'is_primary'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('rule_id', 'is_primary'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/coupon'),
    $installer->getIdxName('salesrule/coupon', array('rule_id')),
    array('rule_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/coupon_usage'),
    $installer->getIdxName('salesrule/coupon_usage', array('coupon_id')),
    array('coupon_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/coupon_usage'),
    $installer->getIdxName('salesrule/coupon_usage', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/rule_customer'),
    $installer->getIdxName('salesrule/rule_customer', array('rule_id', 'customer_id')),
    array('rule_id', 'customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/rule_customer'),
    $installer->getIdxName('salesrule/rule_customer', array('customer_id', 'rule_id')),
    array('customer_id', 'rule_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/label'),
    $installer->getIdxName(
        'salesrule/label',
        array('rule_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('rule_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/label'),
    $installer->getIdxName('salesrule/label', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/label'),
    $installer->getIdxName('salesrule/label', array('rule_id')),
    array('rule_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/product_attribute'),
    $installer->getIdxName('salesrule/product_attribute', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/product_attribute'),
    $installer->getIdxName('salesrule/product_attribute', array('customer_group_id')),
    array('customer_group_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/product_attribute'),
    $installer->getIdxName('salesrule/product_attribute', array('attribute_id')),
    array('attribute_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/coupon_aggregated', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('salesrule/coupon_aggregated'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/coupon_aggregated_order', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('salesrule/coupon_aggregated_order'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/coupon', 'rule_id', 'salesrule/rule', 'rule_id'),
    $installer->getTable('salesrule/coupon'),
    'rule_id',
    $installer->getTable('salesrule/rule'),
    'rule_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/coupon_usage', 'coupon_id', 'salesrule/coupon', 'coupon_id'),
    $installer->getTable('salesrule/coupon_usage'),
    'coupon_id',
    $installer->getTable('salesrule/coupon'),
    'coupon_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/coupon_usage', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('salesrule/coupon_usage'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/rule_customer', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('salesrule/rule_customer'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/rule_customer', 'rule_id', 'salesrule/rule', 'rule_id'),
    $installer->getTable('salesrule/rule_customer'),
    'rule_id',
    $installer->getTable('salesrule/rule'),
    'rule_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/label', 'rule_id', 'salesrule/rule', 'rule_id'),
    $installer->getTable('salesrule/label'),
    'rule_id',
    $installer->getTable('salesrule/rule'),
    'rule_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/label', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('salesrule/label'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/product_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('salesrule/product_attribute'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/product_attribute', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
    $installer->getTable('salesrule/product_attribute'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/product_attribute', 'rule_id', 'salesrule/rule', 'rule_id'),
    $installer->getTable('salesrule/product_attribute'),
    'rule_id',
    $installer->getTable('salesrule/rule'),
    'rule_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('salesrule/product_attribute', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('salesrule/product_attribute'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->endSetup();
