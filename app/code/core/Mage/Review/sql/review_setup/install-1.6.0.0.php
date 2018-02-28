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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review install
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
/**
 * Create table 'review/review_entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('review/review_entity'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Review entity id')
    ->addColumn('entity_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false
        ), 'Review entity code')
    ->setComment('Review entities');
$installer->getConnection()->createTable($table);

/**
 * Create table 'review/review_status'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('review/review_status'))
    ->addColumn('status_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Status id')
    ->addColumn('status_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        ), 'Status code')
    ->setComment('Review statuses');
$installer->getConnection()->createTable($table);

/**
 * Create table 'review/review'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('review/review'))
    ->addColumn('review_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Review id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Review create date')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity id')
    ->addColumn('entity_pk_value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product id')
    ->addColumn('status_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Status code')
    ->addIndex($installer->getIdxName('review/review', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('review/review', array('status_id')),
        array('status_id'))
    ->addIndex($installer->getIdxName('review/review', array('entity_pk_value')),
        array('entity_pk_value'))
    ->addForeignKey($installer->getFkName('review/review', 'entity_id', 'review/review_entity', 'entity_id'),
        'entity_id', $installer->getTable('review/review_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('review/review', 'status_id', 'review/review_status', 'status_id'),
        'status_id', $installer->getTable('review/review_status'), 'status_id',
        Varien_Db_Ddl_Table::ACTION_NO_ACTION, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->setComment('Review base information');
$installer->getConnection()->createTable($table);

/**
 * Create table 'review/review_detail'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('review/review_detail'))
    ->addColumn('detail_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Review detail id')
    ->addColumn('review_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Review id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Store id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Title')
    ->addColumn('detail', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => false,
        ), 'Detail description')
    ->addColumn('nickname', Varien_Db_Ddl_Table::TYPE_TEXT, 128, array(
        'nullable'  => false,
        ), 'User nickname')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Customer Id')
    ->addIndex($installer->getIdxName('review/review_detail', array('review_id')),
        array('review_id'))
    ->addIndex($installer->getIdxName('review/review_detail', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('review/review_detail', array('customer_id')),
        array('customer_id'))
    ->addForeignKey($installer->getFkName('review/review_detail', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('review/review_detail', 'review_id', 'review/review', 'review_id'),
        'review_id', $installer->getTable('review/review'), 'review_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('review/review_detail', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Review detail information');
$installer->getConnection()->createTable($table);

/**
 * Create table 'review/review_aggregate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('review/review_aggregate'))
    ->addColumn('primary_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Summary review entity id')
    ->addColumn('entity_pk_value', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Product id')
    ->addColumn('entity_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity type id')
    ->addColumn('reviews_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Qty of reviews')
    ->addColumn('rating_summary', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Summarized rating')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store id')
    ->addIndex($installer->getIdxName('review/review_aggregate', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('review/review_aggregate', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Review aggregates');
$installer->getConnection()->createTable($table);

/**
 * Create table 'review/review_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('review/review_store'))
    ->addColumn('review_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Review Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store Id')
    ->addIndex($installer->getIdxName('review/review_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('review/review_store', 'review_id', 'review/review', 'review_id'),
        'review_id', $installer->getTable('review/review'), 'review_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('review/review_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Review Store');
$installer->getConnection()->createTable($table);

$this->endSetup();
