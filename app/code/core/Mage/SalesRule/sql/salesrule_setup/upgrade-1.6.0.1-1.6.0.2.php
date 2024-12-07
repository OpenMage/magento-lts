<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/coupon'),
        'created_at',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'comment'  => 'Coupon Code Creation Date',
            'nullable' => false,
            'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ]
    );

$installer->getConnection()->addColumn(
    $installer->getTable('salesrule/coupon'),
    'type',
    [
            'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'comment'  => 'Coupon Code Type',
            'default'  => 0
    ]
);

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/rule'),
        'use_auto_generation',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'comment'  => 'Use Auto Generation',
            'nullable' => false,
            'default'  => 0
        ]
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/rule'),
        'uses_per_coupon',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'comment'  => 'Uses Per Coupon',
            'nullable' => false,
            'default'  => 0
        ]
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/coupon_aggregated'),
        'rule_name',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'   => 255,
            'comment'  => 'Rule Name',
        ]
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/coupon_aggregated_order'),
        'rule_name',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'   => 255,
            'comment'  => 'Rule Name',
        ]
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule/coupon_aggregated_updated'),
        'rule_name',
        [
            'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'   => 255,
            'comment'  => 'Rule Name',
        ]
    );

$installer->getConnection()
    ->addIndex(
        $installer->getTable('salesrule/coupon_aggregated'),
        $installer->getIdxName(
            'salesrule/coupon_aggregated',
            ['rule_name'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        ['rule_name'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );

$installer->getConnection()
    ->addIndex(
        $installer->getTable('salesrule/coupon_aggregated_order'),
        $installer->getIdxName(
            'salesrule/coupon_aggregated_order',
            ['rule_name'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        ['rule_name'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );

$installer->getConnection()
    ->addIndex(
        $installer->getTable('salesrule/coupon_aggregated_updated'),
        $installer->getIdxName(
            'salesrule/coupon_aggregated_updated',
            ['rule_name'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        ['rule_name'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );
