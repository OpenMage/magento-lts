<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('salesrule/coupon_aggregated'),
    'subtotal_amount_actual',
    "decimal(12,4) NOT NULL default '0.0000'",
);

$installer->getConnection()->addColumn(
    $installer->getTable('salesrule/coupon_aggregated'),
    'discount_amount_actual',
    "decimal(12,4) NOT NULL default '0.0000'",
);

$installer->getConnection()->addColumn(
    $installer->getTable('salesrule/coupon_aggregated'),
    'total_amount_actual',
    "decimal(12,4) NOT NULL default '0.0000'",
);
