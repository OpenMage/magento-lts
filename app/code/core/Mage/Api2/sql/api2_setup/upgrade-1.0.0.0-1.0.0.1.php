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
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$connection = $installer->getConnection();
$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');

$tableName = $installer->getTable('api2_acl_attribute');
$engine = $connection->fetchOne("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_NAME=?", [
    $dbname,
    $tableName
]);
if (strtolower($engine) == "innodb") {
    $db->query("ALTER TABLE `$tableName` DROP INDEX `IDX_API2_ACL_ATTRIBUTE_USER_TYPE`");
}
