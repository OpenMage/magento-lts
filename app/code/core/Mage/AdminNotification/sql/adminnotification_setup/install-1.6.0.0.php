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
 * @package     Mage_AdminNotification
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AdminNotification install
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
/**
 * Create table 'adminnotification/inbox'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('adminnotification/inbox'))
    ->addColumn('notification_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Notification id')
    ->addColumn('severity', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Problem type')
    ->addColumn('date_added', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Create date')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Title')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Url')
    ->addColumn('is_read', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Flag if notification read')
    ->addColumn('is_remove', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Flag if notification might be removed')
    ->addIndex($installer->getIdxName('adminnotification/inbox', array('severity')),
        array('severity'))
    ->addIndex($installer->getIdxName('adminnotification/inbox', array('is_read')),
        array('is_read'))
    ->addIndex($installer->getIdxName('adminnotification/inbox', array('is_remove')),
        array('is_remove'))
    ->setComment('Adminnotification Inbox');
$installer->getConnection()->createTable($table);

$installer->endSetup();
