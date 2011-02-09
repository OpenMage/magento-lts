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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$appTable      = $installer->getTable('xmlconnect/application');
$historyTable  = $installer->getTable('xmlconnect/history');
$templateTable = $installer->getTable('xmlconnect/template');
$queueTable    = $installer->getTable('xmlconnect/queue');

foreach (array($appTable, $historyTable, $templateTable, $queueTable) as $table) {
    $installer->run(sprintf('ALTER TABLE `%s` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci', $table));
}

$connection = $installer->getConnection();

$connection->modifyColumn($appTable, 'name', 'VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($appTable, 'code', 'VARCHAR(32) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($appTable, 'type', 'VARCHAR(32) NOT NULL COLLATE utf8_general_ci');

$connection->modifyColumn($historyTable, 'title', 'VARCHAR(200) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($historyTable, 'activation_key', 'VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($historyTable, 'name', 'VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($historyTable, 'code', 'VARCHAR(255) NOT NULL COLLATE utf8_general_ci');

$connection->modifyColumn($templateTable, 'app_code', 'VARCHAR(32) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($templateTable, 'name', 'VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($templateTable, 'push_title', 'VARCHAR(141) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($templateTable, 'message_title', 'VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($templateTable, 'content', 'TEXT NOT NULL COLLATE utf8_general_ci');

$connection->modifyColumn($queueTable, 'push_title', 'VARCHAR(140) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($queueTable, 'message_title', 'VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($queueTable, 'content', 'TEXT NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($queueTable, 'type', 'VARCHAR(12) NOT NULL COLLATE utf8_general_ci');
$connection->modifyColumn($queueTable, 'app_code', 'VARCHAR(32) NOT NULL COLLATE utf8_general_ci');

$connection->addKey($appTable, 'UNQ_XMLCONNECT_APPLICATION_CODE', 'code', 'unique');
$connection->addConstraint('FK_APP_CODE', $templateTable, 'app_code', $appTable, 'code');

$installer->endSetup();
