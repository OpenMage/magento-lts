<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;

$connection = $installer->getConnection();
$connection->createTable($connection->createTableByDdl(
    $installer->getTable('salesrule/coupon_aggregated'),
    $installer->getTable('salesrule/coupon_aggregated_updated'),
));
