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
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('salesrule/coupon_aggregated'),
    'subtotal_amount_actual',
    "decimal(12,4) NOT NULL default '0.0000'"
);

$installer->getConnection()->addColumn(
    $installer->getTable('salesrule/coupon_aggregated'),
    'discount_amount_actual',
    "decimal(12,4) NOT NULL default '0.0000'"
);

$installer->getConnection()->addColumn(
    $installer->getTable('salesrule/coupon_aggregated'),
    'total_amount_actual',
    "decimal(12,4) NOT NULL default '0.0000'"
);
