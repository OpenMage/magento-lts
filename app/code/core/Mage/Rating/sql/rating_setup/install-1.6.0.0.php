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
 * @package     Mage_Rating
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating install
 *
 * @category    Mage
 * @package     Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var Mage_Core_Model_Resource_Setup $installer */

$installer->startSetup();

/**
 * Create table 'rating/rating_entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_entity'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('entity_code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false
        ), 'Entity Code')
    ->addIndex(
        $installer->getIdxName('rating/rating_entity', array('entity_code'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('entity_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Rating entities');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating'))
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rating Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Entity Id')
    ->addColumn('rating_code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false
        ), 'Rating Code')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Rating Position On Frontend')
    ->addIndex(
        $installer->getIdxName('rating/rating', array('rating_code'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('rating_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('rating/rating', array('entity_id')),
        array('entity_id')
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating', 'entity_id', 'rating/rating_entity', 'entity_id'),
        'entity_id',
        $installer->getTable('rating/rating_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Ratings');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_option'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rating Option Id')
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Rating Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false
        ), 'Rating Option Code')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Rating Option Value')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Ration option position on frontend')
    ->addIndex(
        $installer->getIdxName('rating/rating_option', array('rating_id')),
        array('rating_id')
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_option', 'rating_id', 'rating/rating', 'rating_id'),
        'rating_id',
        $installer->getTable('rating/rating'),
        'rating_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Rating options');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating_option_vote'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_option_vote'))
    ->addColumn('vote_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Vote id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Vote option id')
    ->addColumn('remote_ip', Varien_Db_Ddl_Table::TYPE_TEXT, 16, array(
        'nullable'  => false
        ), 'Customer IP')
    ->addColumn('remote_ip_long', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Customer IP converted to long integer format')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'default'   => 0
        ), 'Customer Id')
    ->addColumn('entity_pk_value', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Product id')
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Rating id')
    ->addColumn('review_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        ), 'Review id')
    ->addColumn('percent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Percent amount')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Vote option value')
    ->addIndex(
        $installer->getIdxName('rating/rating_option_vote', array('option_id')),
        array('option_id')
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_option_vote', 'option_id', 'rating/rating_option', 'option_id'),
        'option_id',
        $installer->getTable('rating/rating_option'),
        'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Rating option values');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating_vote_aggregated'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_vote_aggregated'))
    ->addColumn('primary_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Vote aggregation id')
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Rating id')
    ->addColumn('entity_pk_value', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Product id')
    ->addColumn('vote_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Vote dty')
    ->addColumn('vote_value_sum', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'General vote sum')
    ->addColumn('percent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => 0
        ), 'Vote percent')
    ->addColumn('percent_approved', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'default'   => '0',
        ), 'Vote percent approved by admin')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0
        ), 'Store Id')
    ->addIndex(
        $installer->getIdxName('rating/rating_vote_aggregated', array('rating_id')),
        array('rating_id')
    )
    ->addIndex(
        $installer->getIdxName('rating/rating_vote_aggregated', array('store_id')),
        array('store_id')
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_vote_aggregated', 'rating_id', 'rating/rating', 'rating_id'),
        'rating_id',
        $installer->getTable('rating/rating'),
        'rating_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_vote_aggregated', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Rating vote aggregated');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_store'))
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
        'primary'   => true,
        ), 'Rating id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
        'primary'   => true
        ), 'Store id')
    ->addIndex(
        $installer->getIdxName('rating/rating_store', array('store_id')),
        array('store_id')
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_store', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_store', 'rating_id', 'rating/rating', 'rating_id'),
        'rating_id',
        $installer->getTable('rating/rating'),
        'rating_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('Rating Store');
$installer->getConnection()->createTable($table);

/**
 * Create table 'rating/rating_title'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rating/rating_title'))
    ->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
        'primary'   => true,
        ), 'Rating Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
        'primary'   => true
        ), 'Store Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
        ), 'Rating Label')
    ->addIndex(
        $installer->getIdxName('rating/rating_title', array('store_id')),
        array('store_id')
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_title', 'rating_id', 'rating/rating', 'rating_id'),
        'rating_id',
        $installer->getTable('rating/rating'),
        'rating_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('rating/rating_title', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
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
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->endSetup();
