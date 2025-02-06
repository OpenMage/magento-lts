<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$connection = $installer->getConnection();
$connection->createTable($connection->createTableByDdl(
    $installer->getTable('salesrule/coupon_aggregated'),
    $installer->getTable('salesrule/coupon_aggregated_updated'),
));
