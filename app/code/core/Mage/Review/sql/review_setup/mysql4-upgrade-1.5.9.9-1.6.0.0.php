<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Review
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
    $installer->getTable('review/review'),
    'FK_REVIEW_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review'),
    'FK_REVIEW_STATUS',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_CUSTOMER',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_REVIEW',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_aggregate'),
    'FK_REVIEW_ENTITY_SUMMARY_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_store'),
    'FK_REVIEW_STORE_REVIEW',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_store'),
    'FK_REVIEW_STORE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_store'),
    'REVIEW_STORE_IBFK_1',
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('review/review'),
    'FK_REVIEW_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review'),
    'FK_REVIEW_STATUS',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review'),
    'FK_REVIEW_PARENT_PRODUCT',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_REVIEW',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_CUSTOMER',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review_store'),
    'FK_REVIEW_STORE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review_aggregate'),
    'FK_REVIEW_ENTITY_SUMMARY_STORE',
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('review/review') => [
        'columns' => [
            'review_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Review id',
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Review create date',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity id',
            ],
            'entity_pk_value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product id',
            ],
            'status_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Status code',
            ],
        ],
        'comment' => 'Review base information',
    ],
    $installer->getTable('review/review_detail') => [
        'columns' => [
            'detail_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Review detail id',
            ],
            'review_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Review id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Store id',
            ],
            'title' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Title',
            ],
            'detail' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Detail description',
            ],
            'nickname' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'nullable'  => false,
                'comment'   => 'User nickname',
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id',
            ],
        ],
        'comment' => 'Review detail information',
    ],
    $installer->getTable('review/review_status') => [
        'columns' => [
            'status_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Status id',
            ],
            'status_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Status code',
            ],
        ],
        'comment' => 'Review statuses',
    ],
    $installer->getTable('review/review_entity') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Review entity id',
            ],
            'entity_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Review entity code',
            ],
        ],
        'comment' => 'Review entities',
    ],
    $installer->getTable('review/review_aggregate') => [
        'columns' => [
            'primary_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Summary review entity id',
            ],
            'entity_pk_value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product id',
            ],
            'entity_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity type id',
            ],
            'reviews_count' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Qty of reviews',
            ],
            'rating_summary' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Summarized rating',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store id',
            ],
        ],
        'comment' => 'Review aggregates',
    ],
    $installer->getTable('review/review_store') => [
        'columns' => [
            'review_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Review Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
        ],
        'comment' => 'Review Store',
    ],
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('review/review'),
    $installer->getIdxName('review/review', ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review'),
    $installer->getIdxName('review/review', ['status_id']),
    ['status_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review'),
    $installer->getIdxName('review/review', ['entity_pk_value']),
    ['entity_pk_value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review_detail'),
    $installer->getIdxName('review/review_detail', ['review_id']),
    ['review_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review_detail'),
    $installer->getIdxName('review/review_detail', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review_detail'),
    $installer->getIdxName('review/review_detail', ['customer_id']),
    ['customer_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review_aggregate'),
    $installer->getIdxName('review/review_aggregate', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review_store'),
    $installer->getIdxName('review/review_store', ['store_id']),
    ['store_id'],
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review', 'entity_id', 'review/review_entity', 'entity_id'),
    $installer->getTable('review/review'),
    'entity_id',
    $installer->getTable('review/review_entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review', 'status_id', 'review/review_status', 'status_id'),
    $installer->getTable('review/review'),
    'status_id',
    $installer->getTable('review/review_status'),
    'status_id',
    Varien_Db_Ddl_Table::ACTION_NO_ACTION,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION,
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_detail', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('review/review_detail'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_detail', 'review_id', 'review/review', 'review_id'),
    $installer->getTable('review/review_detail'),
    'review_id',
    $installer->getTable('review/review'),
    'review_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_detail', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('review/review_detail'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_aggregate', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('review/review_aggregate'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_store', 'review_id', 'review/review', 'review_id'),
    $installer->getTable('review/review_store'),
    'review_id',
    $installer->getTable('review/review'),
    'review_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_store', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('review/review_store'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->endSetup();
