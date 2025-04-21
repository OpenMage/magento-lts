<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
