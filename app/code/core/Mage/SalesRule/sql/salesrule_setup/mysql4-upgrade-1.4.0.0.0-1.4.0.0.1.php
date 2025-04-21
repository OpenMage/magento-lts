<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();

$tableSalesrule = $installer->getTable('salesrule/rule');
$tableSalesruleCustomer = $installer->getTable('salesrule/rule_customer');
$tableSalesruleCoupon = $installer->getTable('salesrule/coupon');
$tableSalesruleCouponUsage = $installer->getTable('salesrule/coupon_usage');
$tableCustomerEntity = $installer->getTable('customer/entity');

$installer->run("
CREATE TABLE `{$tableSalesruleCoupon}` (
  `coupon_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(10) unsigned NOT NULL,
  `code` varchar(255) NOT NULL,
  `usage_limit` int(10) unsigned DEFAULT NULL,
  `usage_per_customer` int(10) unsigned DEFAULT NULL,
  `times_used` int(10) unsigned NOT NULL DEFAULT '0',
  `expiration_date` datetime DEFAULT NULL,
  `is_primary` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`coupon_id`),
  UNIQUE KEY `UNQ_COUPON_CODE` (`code`),
  UNIQUE KEY `UNQ_RULE_MAIN_COUPON` (`rule_id`, `is_primary`),
  KEY `FK_SALESRULE_COUPON_RULE_ID_SALESRULE` (`rule_id`),
  CONSTRAINT `FK_SALESRULE_COUPON_RULE_ID_SALESRULE` FOREIGN KEY (`rule_id`) REFERENCES `{$tableSalesrule}` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$tableSalesruleCouponUsage}` (
  `coupon_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `times_used` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`coupon_id`, `customer_id`),
  KEY `FK_SALESRULE_COUPON_CUSTOMER_COUPON_ID_CUSTOMER_ENTITY` (`coupon_id`),
  KEY `FK_SALESRULE_COUPON_CUSTOMER_CUSTOMER_ID_CUSTOMER_ENTITY` (`customer_id`),
  CONSTRAINT `FK_SALESRULE_COUPON_CUSTOMER_COUPON_ID_CUSTOMER_ENTITY` FOREIGN KEY (`coupon_id`) REFERENCES `{$tableSalesruleCoupon}` (`coupon_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALESRULE_COUPON_CUSTOMER_CUSTOMER_ID_CUSTOMER_ENTITY` FOREIGN KEY (`customer_id`) REFERENCES `{$tableCustomerEntity}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$tableSalesruleCoupon}` (
  rule_id
, code
, usage_limit
, usage_per_customer
, times_used
, is_primary
)
SELECT
  rule_id
, coupon_code
, uses_per_coupon
, uses_per_customer
, times_used
, 1
FROM `{$tableSalesrule}`
WHERE
  coupon_code <> '';

INSERT INTO `{$tableSalesruleCouponUsage}` (
  coupon_id
, customer_id
, times_used
)
SELECT
  coupon.coupon_id
, customer.customer_id
, customer.times_used
FROM `{$tableSalesruleCoupon}` coupon
JOIN `{$tableSalesruleCustomer}` customer ON(
  customer.rule_id = coupon.rule_id
);

ALTER TABLE `{$tableSalesrule}`
  DROP COLUMN `coupon_code`,
  DROP COLUMN `uses_per_coupon`;
");
