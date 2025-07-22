<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$installer->getTable('api/session')}` (
        `user_id` mediumint(9) UNSIGNED NOT NULL,
        `logdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        `sessid` varchar(40) NOT NULL DEFAULT '',
        KEY `API_SESSION_USER` (`user_id`),
        KEY `API_SESSION_SESSID` (`sessid`),
        CONSTRAINT `FK_API_SESSION_USER` FOREIGN KEY (`user_id`) REFERENCES `{$installer->getTable('api/user')}` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Api Sessions';
");

$insertStmt = "INSERT INTO
    `{$installer->getTable('api/session')}` (`user_id`, `logdate`, `sessid`)
    SELECT `user_id`, `logdate`, `sessid` FROM `{$installer->getTable('api/user')}`";
$installer->run($insertStmt);
$installer->getConnection()->dropColumn($installer->getTable('api/user'), 'logdate');
$installer->getConnection()->dropColumn($installer->getTable('api/user'), 'sessid');

$installer->endSetup();
