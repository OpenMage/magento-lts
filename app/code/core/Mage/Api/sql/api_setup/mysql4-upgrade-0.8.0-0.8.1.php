<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
