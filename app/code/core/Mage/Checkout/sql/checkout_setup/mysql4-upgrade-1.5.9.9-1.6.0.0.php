<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
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
    $installer->getTable('checkout/agreement_store'),
    'FK_CHECKOUT_AGREEMENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('checkout/agreement_store'),
    'FK_CHECKOUT_AGREEMENT_STORE'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('checkout/agreement_store'),
    'AGREEMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('checkout/agreement_store'),
    'FK_CHECKOUT_AGREEMENT_STORE'
);

/*
 * Change columns
 */
$tables = [
    $installer->getTable('checkout/agreement') => [
        'columns' => [
            'agreement_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Agreement Id'
            ],
            'name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ],
            'content' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Content'
            ],
            'content_height' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 25,
                'comment'   => 'Content Height'
            ],
            'checkbox_text' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Checkbox Text'
            ],
            'is_active' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Active'
            ],
            'is_html' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Html'
            ]
        ],
        'comment' => 'Checkout Agreement'
    ],
    $installer->getTable('checkout/agreement_store') => [
        'columns' => [
            'agreement_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Agreement Id'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Store Id'
            ]
        ],
        'comment' => 'Checkout Agreement Store'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('checkout/agreement_store'),
    'PRIMARY',
    ['agreement_id','store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('checkout/agreement_store', 'agreement_id', 'checkout/agreement', 'agreement_id'),
    $installer->getTable('checkout/agreement_store'),
    'agreement_id',
    $installer->getTable('checkout/agreement'),
    'agreement_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('checkout/agreement_store', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('checkout/agreement_store'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->endSetup();
