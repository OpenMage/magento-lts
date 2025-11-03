<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/coupon'),
        'created_at',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'comment'  => 'Coupon Code Creation Date',
            'nullable' => false,
            'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ],
    );

$installer->getConnection()->addColumn(
    $installer->getTable('salesrule/coupon'),
    'type',
    [
        'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'comment'  => 'Coupon Code Type',
        'default'  => 0,
    ],
);

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/rule'),
        'use_auto_generation',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'comment'  => 'Use Auto Generation',
            'nullable' => false,
            'default'  => 0,
        ],
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/rule'),
        'uses_per_coupon',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'comment'  => 'Uses Per Coupon',
            'nullable' => false,
            'default'  => 0,
        ],
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/coupon_aggregated'),
        'rule_name',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'   => 255,
            'comment'  => 'Rule Name',
        ],
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/coupon_aggregated_order'),
        'rule_name',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'   => 255,
            'comment'  => 'Rule Name',
        ],
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/coupon_aggregated_updated'),
        'rule_name',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'   => 255,
            'comment'  => 'Rule Name',
        ],
    );

$installer->getConnection()
    ->addIndex(
        $installer->getTable('salesrule/coupon_aggregated'),
        $installer->getIdxName(
            'salesrule/coupon_aggregated',
            ['rule_name'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
        ),
        ['rule_name'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
    );

$installer->getConnection()
    ->addIndex(
        $installer->getTable('salesrule/coupon_aggregated_order'),
        $installer->getIdxName(
            'salesrule/coupon_aggregated_order',
            ['rule_name'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
        ),
        ['rule_name'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
    );

$installer->getConnection()
    ->addIndex(
        $installer->getTable('salesrule/coupon_aggregated_updated'),
        $installer->getIdxName(
            'salesrule/coupon_aggregated_updated',
            ['rule_name'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
        ),
        ['rule_name'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
    );
