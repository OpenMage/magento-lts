<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/coupon_usage'),
    'FK_SALESRULE_COUPON_CUSTOMER_COUPON_ID_CUSTOMER_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('salesrule/coupon_usage'),
    'FK_SALESRULE_COUPON_CUSTOMER_CUSTOMER_ID_CUSTOMER_ENTITY',
);

$installer->getConnection()->addConstraint(
    'FK_SALESRULE_CPN_CUST_CPN_ID_CUST_ENTITY',
    $installer->getTable('salesrule/coupon_usage'),
    'coupon_id',
    $installer->getTable('salesrule/coupon'),
    'coupon_id',
);

$installer->getConnection()->addConstraint(
    'FK_SALESRULE_CPN_CUST_CUST_ID_CUST_ENTITY',
    $installer->getTable('salesrule/coupon_usage'),
    'customer_id',
    $installer->getTable('customer_entity'),
    'entity_id',
);

$installer->endSetup();
