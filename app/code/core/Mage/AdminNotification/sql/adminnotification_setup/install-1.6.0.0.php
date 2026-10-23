<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_AdminNotification
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();
/**
 * Create table 'adminnotification/inbox'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('adminnotification/inbox'))
    ->addColumn('notification_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Notification id')
    ->addColumn('severity', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Problem type')
    ->addColumn('date_added', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Create date')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Title')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Description')
    ->addColumn('url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Url')
    ->addColumn('is_read', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Flag if notification read')
    ->addColumn('is_remove', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Flag if notification might be removed')
    ->addIndex(
        $installer->getIdxName('adminnotification/inbox', ['severity']),
        ['severity'],
    )
    ->addIndex(
        $installer->getIdxName('adminnotification/inbox', ['is_read']),
        ['is_read'],
    )
    ->addIndex(
        $installer->getIdxName('adminnotification/inbox', ['is_remove']),
        ['is_remove'],
    )
    ->setComment('Adminnotification Inbox');
$installer->getConnection()->createTable($table);

$installer->endSetup();
