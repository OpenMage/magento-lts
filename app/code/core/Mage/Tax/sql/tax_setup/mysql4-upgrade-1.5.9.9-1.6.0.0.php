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
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Tax_Model_Resource_Setup */
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
$tables = array(
    $installer->getTable('tax/tax_class') => array(
        'columns' => array(
            'class_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Class Id'
            ),
            'class_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Class Name'
            ),
            'class_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 8,
                'nullable'  => false,
                'default'   => Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER,
                'comment'   => 'Class Type'
            )
        ),
        'comment' => 'Tax Class'
    ),
    $installer->getTable('tax/tax_calculation') => array(
        'columns' => array(
            'tax_calculation_rate_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Tax Calculation Rate Id'
            ),
            'tax_calculation_rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Tax Calculation Rule Id'
            ),
            'customer_tax_class_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'comment'   => 'Customer Tax Class Id'
            ),
            'product_tax_class_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'comment'   => 'Product Tax Class Id'
            )
        ),
        'comment' => 'Tax Calculation'
    ),
    $installer->getTable('tax/tax_calculation_rate') => array(
        'columns' => array(
            'tax_calculation_rate_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tax Calculation Rate Id'
            ),
            'tax_country_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'nullable'  => false,
                'comment'   => 'Tax Country Id'
            ),
            'tax_region_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Tax Region Id'
            ),
            'tax_postcode' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 21,
                'comment'   => 'Tax Postcode'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Code'
            ),
            'rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Rate'
            ),
            'zip_is_range' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'comment'   => 'Zip Is Range'
            ),
            'zip_from' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Zip From'
            ),
            'zip_to' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Zip To'
            )
        ),
        'comment' => 'Tax Calculation Rate'
    ),
    $installer->getTable('tax/tax_calculation_rate_title') => array(
        'columns' => array(
            'tax_calculation_rate_title_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tax Calculation Rate Title Id'
            ),
            'tax_calculation_rate_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Tax Calculation Rate Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Tax Calculation Rate Title'
    ),
    $installer->getTable('tax/tax_calculation_rule') => array(
        'columns' => array(
            'tax_calculation_rule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tax Calculation Rule Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Code'
            ),
            'priority' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Priority'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Position'
            )
        ),
        'comment' => 'Tax Calculation Rule'
    ),
    $installer->getTable('tax/tax_order_aggregated_created') => array(
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
                'comment'   => 'Period'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Code'
            ),
            'order_status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Order Status'
            ),
            'percent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_FLOAT,
                'comment'   => 'Percent'
            ),
            'orders_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Orders Count'
            ),
            'tax_base_amount_sum' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_FLOAT,
                'comment'   => 'Tax Base Amount Sum'
            )
        ),
        'comment' => 'Tax Order Aggregation'
    )
);

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->addColumn(
    $installer->getTable('tax/tax_calculation'),
    'tax_calculation_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        'comment'   => 'Tax Calculation Id'
    )
);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation'),
    $installer->getIdxName('tax/tax_calculation', array('tax_calculation_rule_id')),
    array('tax_calculation_rule_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation'),
    $installer->getIdxName('tax/tax_calculation', array('tax_calculation_rate_id')),
    array('tax_calculation_rate_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation'),
    $installer->getIdxName('tax/tax_calculation', array('customer_tax_class_id')),
    array('customer_tax_class_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation'),
    $installer->getIdxName('tax/tax_calculation', array('product_tax_class_id')),
    array('product_tax_class_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation'),
    $installer->getIdxName('tax/tax_calculation', array('tax_calculation_rate_id', 'customer_tax_class_id', 'product_tax_class_id')),
    array('tax_calculation_rate_id', 'customer_tax_class_id', 'product_tax_class_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate'),
    $installer->getIdxName('tax/tax_calculation_rate', array('tax_country_id', 'tax_region_id', 'tax_postcode')),
    array('tax_country_id', 'tax_region_id', 'tax_postcode')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate'),
    $installer->getIdxName('tax/tax_calculation_rate', array('code')),
    array('code')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate'),
    $installer->getIdxName('tax/tax_calculation_rate', array('tax_calculation_rate_id', 'tax_country_id', 'tax_region_id', 'zip_is_range', 'tax_postcode')),
    array('tax_calculation_rate_id', 'tax_country_id', 'tax_region_id', 'zip_is_range', 'tax_postcode')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate_title'),
    $installer->getIdxName('tax/tax_calculation_rate_title', array('tax_calculation_rate_id', 'store_id')),
    array('tax_calculation_rate_id', 'store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate_title'),
    $installer->getIdxName('tax/tax_calculation_rate_title', array('tax_calculation_rate_id')),
    array('tax_calculation_rate_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rate_title'),
    $installer->getIdxName('tax/tax_calculation_rate_title', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rule'),
    $installer->getIdxName('tax/tax_calculation_rule', array('priority', 'position', 'tax_calculation_rule_id')),
    array('priority', 'position', 'tax_calculation_rule_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_calculation_rule'),
    $installer->getIdxName('tax/tax_calculation_rule', array('code')),
    array('code')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_order_aggregated_created'),
    $installer->getIdxName(
        'tax/tax_order_aggregated_created',
        array('period', 'store_id', 'code', 'percent', 'order_status'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('period', 'store_id', 'code', 'percent', 'order_status'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('tax/tax_order_aggregated_created'),
    $installer->getIdxName('tax/tax_order_aggregated_created', array('store_id')),
    array('store_id')
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
