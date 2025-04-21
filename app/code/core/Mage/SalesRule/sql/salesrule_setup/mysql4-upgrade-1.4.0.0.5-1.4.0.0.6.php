<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
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
