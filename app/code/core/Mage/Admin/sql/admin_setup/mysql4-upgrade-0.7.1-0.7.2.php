<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var Mage_Core_Model_Resource_Setup $installer */

$installer->startSetup();

$tableAdmins = $installer->getTable('admin/user');

// delete admin username duplicates
$duplicatedUsers = $installer->getConnection()->fetchPairs("
SELECT user_id, username FROM {$tableAdmins} GROUP by username HAVING COUNT(user_id) > 1
");
$installer->run("DELETE FROM {$tableAdmins} WHERE username "
    . $installer->getConnection()->quoteInto('IN (?) ', array_values($duplicatedUsers))
    . 'AND user_id ' . $installer->getConnection()->quoteInto('NOT IN (?) ', array_keys($duplicatedUsers)));

// add unique key to username field
$installer->getConnection()->addKey($tableAdmins, 'UNQ_ADMIN_USER_USERNAME', 'username', 'unique');

$installer->endSetup();
