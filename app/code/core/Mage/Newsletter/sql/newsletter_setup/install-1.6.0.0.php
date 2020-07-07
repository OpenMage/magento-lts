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
 * @package     Mage_Newsletter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Newsletter install
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author     Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var Mage_Core_Model_Resource_Setup $installer */

$installer->startSetup();

/**
 * Create table 'newsletter/subscriber'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('newsletter/subscriber'))
    ->addColumn('subscriber_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Subscriber Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('change_status_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Change Status At')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Id')
    ->addColumn('subscriber_email', Varien_Db_Ddl_Table::TYPE_TEXT, 150, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Subscriber Email')
    ->addColumn('subscriber_status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Subscriber Status')
    ->addColumn('subscriber_confirm_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'default'   => 'NULL',
        ), 'Subscriber Confirm Code')
    ->addIndex(
        $installer->getIdxName('newsletter/subscriber', array('customer_id')),
        array('customer_id')
    )
    ->addIndex(
        $installer->getIdxName('newsletter/subscriber', array('store_id')),
        array('store_id')
    )
    ->addForeignKey(
        $installer->getFkName('newsletter/subscriber', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Newsletter Subscriber');
$installer->getConnection()->createTable($table);

/**
 * Create table 'newsletter/template'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('newsletter/template'))
    ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Template Id')
    ->addColumn('template_code', Varien_Db_Ddl_Table::TYPE_TEXT, 150, array(
        ), 'Template Code')
    ->addColumn('template_text', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Template Text')
    ->addColumn('template_text_preprocessed', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Template Text Preprocessed')
    ->addColumn('template_styles', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Template Styles')
    ->addColumn('template_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Template Type')
    ->addColumn('template_subject', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        ), 'Template Subject')
    ->addColumn('template_sender_name', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        ), 'Template Sender Name')
    ->addColumn('template_sender_email', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        ), 'Template Sender Email')
    ->addColumn('template_actual', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '1',
        ), 'Template Actual')
    ->addColumn('added_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Added At')
    ->addColumn('modified_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Modified At')
    ->addIndex(
        $installer->getIdxName('newsletter/template', array('template_actual')),
        array('template_actual')
    )
    ->addIndex(
        $installer->getIdxName('newsletter/template', array('added_at')),
        array('added_at')
    )
    ->addIndex(
        $installer->getIdxName('newsletter/template', array('modified_at')),
        array('modified_at')
    )
    ->setComment('Newsletter Template');
$installer->getConnection()->createTable($table);

/**
 * Create table 'newsletter/queue'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('newsletter/queue'))
    ->addColumn('queue_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Queue Id')
    ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Template Id')
    ->addColumn('newsletter_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Newsletter Type')
    ->addColumn('newsletter_text', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Newsletter Text')
    ->addColumn('newsletter_styles', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Newsletter Styles')
    ->addColumn('newsletter_subject', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        ), 'Newsletter Subject')
    ->addColumn('newsletter_sender_name', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        ), 'Newsletter Sender Name')
    ->addColumn('newsletter_sender_email', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        ), 'Newsletter Sender Email')
    ->addColumn('queue_status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Queue Status')
    ->addColumn('queue_start_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Queue Start At')
    ->addColumn('queue_finish_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Queue Finish At')
    ->addIndex(
        $installer->getIdxName('newsletter/queue', array('template_id')),
        array('template_id')
    )
    ->addForeignKey(
        $installer->getFkName('newsletter/queue', 'template_id', 'newsletter/template', 'template_id'),
        'template_id',
        $installer->getTable('newsletter/template'),
        'template_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Newsletter Queue');
$installer->getConnection()->createTable($table);

/**
 * Create table 'newsletter/queue_link'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('newsletter/queue_link'))
    ->addColumn('queue_link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Queue Link Id')
    ->addColumn('queue_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Queue Id')
    ->addColumn('subscriber_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Subscriber Id')
    ->addColumn('letter_sent_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Letter Sent At')
    ->addIndex(
        $installer->getIdxName('newsletter/queue_link', array('subscriber_id')),
        array('subscriber_id')
    )
    ->addIndex(
        $installer->getIdxName('newsletter/queue_link', array('queue_id')),
        array('queue_id')
    )
    ->addIndex(
        $installer->getIdxName('newsletter/queue_link', array('queue_id', 'letter_sent_at')),
        array('queue_id', 'letter_sent_at')
    )
    ->addForeignKey(
        $installer->getFkName('newsletter/queue_link', 'queue_id', 'newsletter/queue', 'queue_id'),
        'queue_id',
        $installer->getTable('newsletter/queue'),
        'queue_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('newsletter/queue_link', 'subscriber_id', 'newsletter/subscriber', 'subscriber_id'),
        'subscriber_id',
        $installer->getTable('newsletter/subscriber'),
        'subscriber_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Newsletter Queue Link');
$installer->getConnection()->createTable($table);

/**
 * Create table 'newsletter/queue_store_link'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('newsletter/queue_store_link'))
    ->addColumn('queue_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Queue Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Store Id')
    ->addIndex(
        $installer->getIdxName('newsletter/queue_store_link', array('store_id')),
        array('store_id')
    )
    ->addForeignKey(
        $installer->getFkName('newsletter/queue_store_link', 'queue_id', 'newsletter/queue', 'queue_id'),
        'queue_id',
        $installer->getTable('newsletter/queue'),
        'queue_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('newsletter/queue_store_link', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Newsletter Queue Store Link');
$installer->getConnection()->createTable($table);

/**
 * Create table 'newsletter/problem'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('newsletter/problem'))
    ->addColumn('problem_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Problem Id')
    ->addColumn('subscriber_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Subscriber Id')
    ->addColumn('queue_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Queue Id')
    ->addColumn('problem_error_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Problem Error Code')
    ->addColumn('problem_error_text', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        ), 'Problem Error Text')
    ->addIndex(
        $installer->getIdxName('newsletter/problem', array('subscriber_id')),
        array('subscriber_id')
    )
    ->addIndex(
        $installer->getIdxName('newsletter/problem', array('queue_id')),
        array('queue_id')
    )
    ->addForeignKey(
        $installer->getFkName('newsletter/problem', 'queue_id', 'newsletter/queue', 'queue_id'),
        'queue_id',
        $installer->getTable('newsletter/queue'),
        'queue_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('newsletter/problem', 'subscriber_id', 'newsletter/subscriber', 'subscriber_id'),
        'subscriber_id',
        $installer->getTable('newsletter/subscriber'),
        'subscriber_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Newsletter Problems');
$installer->getConnection()->createTable($table);

$installer->endSetup();
