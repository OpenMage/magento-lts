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
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review'),
    'FK_REVIEW_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review'),
    'FK_REVIEW_STATUS'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_REVIEW'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_aggregate'),
    'FK_REVIEW_ENTITY_SUMMARY_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_store'),
    'FK_REVIEW_STORE_REVIEW'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_store'),
    'FK_REVIEW_STORE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('review/review_store'),
    'REVIEW_STORE_IBFK_1'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('review/review'),
    'FK_REVIEW_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review'),
    'FK_REVIEW_STATUS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review'),
    'FK_REVIEW_PARENT_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_REVIEW'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review_detail'),
    'FK_REVIEW_DETAIL_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review_store'),
    'FK_REVIEW_STORE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('review/review_aggregate'),
    'FK_REVIEW_ENTITY_SUMMARY_STORE'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('review/review') => array(
        'columns' => array(
            'review_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Review id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Review create date'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity id'
            ),
            'entity_pk_value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product id'
            ),
            'status_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Status code'
            )
        ),
        'comment' => 'Review base information'
    ),
    $installer->getTable('review/review_detail') => array(
        'columns' => array(
            'detail_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Review detail id'
            ),
            'review_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Review id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Store id'
            ),
            'title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Title'
            ),
            'detail' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Detail description'
            ),
            'nickname' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 128,
                'nullable'  => false,
                'comment'   => 'User nickname'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            )
        ),
        'comment' => 'Review detail information'
    ),
    $installer->getTable('review/review_status') => array(
        'columns' => array(
            'status_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Status id'
            ),
            'status_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Status code'
            )
        ),
        'comment' => 'Review statuses'
    ),
    $installer->getTable('review/review_entity') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Review entity id'
            ),
            'entity_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Review entity code'
            )
        ),
        'comment' => 'Review entities'
    ),
    $installer->getTable('review/review_aggregate') => array(
        'columns' => array(
            'primary_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Summary review entity id'
            ),
            'entity_pk_value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product id'
            ),
            'entity_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity type id'
            ),
            'reviews_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Qty of reviews'
            ),
            'rating_summary' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Summarized rating'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store id'
            )
        ),
        'comment' => 'Review aggregates'
    ),
    $installer->getTable('review/review_store') => array(
        'columns' => array(
            'review_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Review Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store Id'
            )
        ),
        'comment' => 'Review Store'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('review/review'),
    $installer->getIdxName('review/review', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review'),
    $installer->getIdxName('review/review', array('status_id')),
    array('status_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review'),
    $installer->getIdxName('review/review', array('entity_pk_value')),
    array('entity_pk_value')
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review_detail'),
    $installer->getIdxName('review/review_detail', array('review_id')),
    array('review_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review_detail'),
    $installer->getIdxName('review/review_detail', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review_detail'),
    $installer->getIdxName('review/review_detail', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review_aggregate'),
    $installer->getIdxName('review/review_aggregate', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('review/review_store'),
    $installer->getIdxName('review/review_store', array('store_id')),
    array('store_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review', 'entity_id', 'review/review_entity', 'entity_id'),
    $installer->getTable('review/review'),
    'entity_id',
    $installer->getTable('review/review_entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review', 'status_id', 'review/review_status', 'status_id'),
    $installer->getTable('review/review'),
    'status_id',
    $installer->getTable('review/review_status'),
    'status_id',
    Varien_Db_Ddl_Table::ACTION_NO_ACTION,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_detail', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('review/review_detail'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_detail', 'review_id', 'review/review', 'review_id'),
    $installer->getTable('review/review_detail'),
    'review_id',
    $installer->getTable('review/review'),
    'review_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_detail', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('review/review_detail'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_aggregate', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('review/review_aggregate'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_store', 'review_id', 'review/review', 'review_id'),
    $installer->getTable('review/review_store'),
    'review_id',
    $installer->getTable('review/review'),
    'review_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('review/review_store', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('review/review_store'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->endSetup();
