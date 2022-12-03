<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'core/email_queue'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/email_queue'))
    ->addColumn('message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
         'identity'  => true,
         'unsigned'  => true,
         'nullable'  => false,
         'primary'   => true,
    ], 'Message Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
    ], 'Entity ID')
    ->addColumn('entity_type', Varien_Db_Ddl_Table::TYPE_TEXT, 128, [
    ], 'Entity Type')
    ->addColumn('event_type', Varien_Db_Ddl_Table::TYPE_TEXT, 128, [
    ], 'Event Type')
    ->addColumn('message_body_hash', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
            'nullable'  => false,
    ], 'Message Body Hash')
    ->addColumn('message_body', Varien_Db_Ddl_Table::TYPE_TEXT, '1024k', [
           'nullable'  => false,
    ], 'Message Body')
    ->addColumn('message_parameters', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
           'nullable'  => false,
    ], 'Message Parameters')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Creation Time')
    ->addColumn('processed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Finish Time')
    ->addIndex(
        $installer->getIdxName(
            'core/email_queue',
            ['entity_id', 'entity_type', 'event_type', 'message_body_hash']
        ),
        ['entity_id', 'entity_type', 'event_type', 'message_body_hash']
    )
    ->setComment('Email Queue');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core/email_recipients'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core/email_recipients'))
    ->addColumn('recipient_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
    ], 'Recipient Id')
    ->addColumn('message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
           'unsigned'  => true,
           'nullable'  => false,
    ], 'Message ID')
    ->addColumn('recipient_email', Varien_Db_Ddl_Table::TYPE_TEXT, 128, [
           'nullable'  => false,
    ], 'Recipient Email')
    ->addColumn('recipient_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
             'nullable'  => false,
    ], 'Recipient Name')
    ->addColumn('email_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
            'nullable'  => false,
            'default'   => '0',
    ], 'Email Type')
    ->addIndex(
        $installer->getIdxName('core/email_recipients', ['recipient_email']),
        ['recipient_email']
    )
    ->addIndex(
        $installer->getIdxName('core/email_recipients', ['email_type']),
        ['email_type']
    )
    ->addIndex(
        $installer->getIdxName(
            'core/email_recipients',
            ['message_id', 'recipient_email', 'email_type'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['message_id', 'recipient_email', 'email_type'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $installer->getFkName('core/email_recipients', 'message_id', 'core/email_queue', 'message_id'),
        'message_id',
        $installer->getTable('core/email_queue'),
        'message_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Email Queue');
$installer->getConnection()->createTable($table);

$installer->endSetup();
