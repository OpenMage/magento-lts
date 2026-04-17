<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rating
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'rating/rating_entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_entity'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('entity_code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
        'nullable'  => false,
    ], 'Entity Code')
    ->addIndex(
        $installer->getIdxName('rating/rating_entity', ['entity_code'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['entity_code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->setComment('Rating entities');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating'))
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Rating Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Entity Id')
    ->addColumn('rating_code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
        'nullable'  => false,
    ], 'Rating Code')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Rating Position On Frontend')
    ->addIndex(
        $installer->getIdxName('rating/rating', ['rating_code'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['rating_code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('rating/rating', ['entity_id']),
        ['entity_id'],
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating', 'entity_id', 'rating/rating_entity', 'entity_id'),
        'entity_id',
        $installer->getTable('rating/rating_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Ratings');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_option'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Rating Option Id')
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Rating Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
    ], 'Rating Option Code')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Rating Option Value')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Ration option position on frontend')
    ->addIndex(
        $installer->getIdxName('rating/rating_option', ['rating_id']),
        ['rating_id'],
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_option', 'rating_id', 'rating/rating', 'rating_id'),
        'rating_id',
        $installer->getTable('rating/rating'),
        'rating_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Rating options');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating_option_vote'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_option_vote'))
    ->addColumn('vote_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Vote id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Vote option id')
    ->addColumn('remote_ip', Varien_Db_Ddl_Table::TYPE_TEXT, 16, [
        'nullable'  => false,
    ], 'Customer IP')
    ->addColumn('remote_ip_long', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'nullable'  => false,
        'default'   => 0,
    ], 'Customer IP converted to long integer format')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'default'   => 0,
    ], 'Customer Id')
    ->addColumn('entity_pk_value', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Product id')
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Rating id')
    ->addColumn('review_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'unsigned'  => true,
    ], 'Review id')
    ->addColumn('percent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => 0,
    ], 'Percent amount')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => 0,
    ], 'Vote option value')
    ->addIndex(
        $installer->getIdxName('rating/rating_option_vote', ['option_id']),
        ['option_id'],
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_option_vote', 'option_id', 'rating/rating_option', 'option_id'),
        'option_id',
        $installer->getTable('rating/rating_option'),
        'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Rating option values');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating_vote_aggregated'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_vote_aggregated'))
    ->addColumn('primary_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Vote aggregation id')
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Rating id')
    ->addColumn('entity_pk_value', Varien_Db_Ddl_Table::TYPE_BIGINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Product id')
    ->addColumn('vote_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Vote dty')
    ->addColumn('vote_value_sum', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'General vote sum')
    ->addColumn('percent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => 0,
    ], 'Vote percent')
    ->addColumn('percent_approved', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'default'   => '0',
    ], 'Vote percent approved by admin')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
    ], 'Store Id')
    ->addIndex(
        $installer->getIdxName('rating/rating_vote_aggregated', ['rating_id']),
        ['rating_id'],
    )
    ->addIndex(
        $installer->getIdxName('rating/rating_vote_aggregated', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_vote_aggregated', 'rating_id', 'rating/rating', 'rating_id'),
        'rating_id',
        $installer->getTable('rating/rating'),
        'rating_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_vote_aggregated', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Rating vote aggregated');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_store'))
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
        'primary'   => true,
    ], 'Rating id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
        'primary'   => true,
    ], 'Store id')
    ->addIndex(
        $installer->getIdxName('rating/rating_store', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_store', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_store', 'rating_id', 'rating/rating', 'rating_id'),
        'rating_id',
        $installer->getTable('rating/rating'),
        'rating_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION,
    )
    ->setComment('Rating Store');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating_title'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_title'))
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
        'primary'   => true,
    ], 'Rating Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
        'primary'   => true,
    ], 'Store Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Rating Label')
    ->addIndex(
        $installer->getIdxName('rating/rating_title', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_title', 'rating_id', 'rating/rating', 'rating_id'),
        'rating_id',
        $installer->getTable('rating/rating'),
        'rating_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_title', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Rating Title');
$installer->getConnection()->createTable($table);

/**
 * Review/Rating module upgrade.
 * Create FK for 'rating/rating_option_vote'
 */
$table = $installer->getConnection()->addForeignKey(
    $installer->getFkName('rating/rating_option_vote', 'review_id', 'review/review', 'review_id'),
    $installer->getTable('rating/rating_option_vote'),
    'review_id',
    $installer->getTable('review/review'),
    'review_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE,
);

$installer->endSetup();
