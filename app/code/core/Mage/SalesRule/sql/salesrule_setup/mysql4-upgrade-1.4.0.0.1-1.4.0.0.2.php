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

$connection = $installer->getConnection();
/** @var Varien_Db_Adapter_Pdo_Mysql $connection */

$tableSalesrule = $installer->getTable('salesrule/rule');
$tableSalesruleCoupon = $installer->getTable('salesrule/coupon');

$connection->addColumn(
    $tableSalesrule,
    'coupon_type',
    'smallint unsigned NOT NULL DEFAULT "' . Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON . '"',
);

/**
 * UPDATE coupon_type to specific in rules with primary coupon
 * Always come to ON DUPLICATE KEY UPDATE section of INSERT statement
 */
$installer->run("
INSERT `{$tableSalesrule}`(
  rule_id, /* PRIMARY KEY */
  /* columns with no default value to prevent warnings */
  description, conditions_serialized, actions_serialized, discount_step
)
SELECT DISTINCT
  rule_id, /* make sure PRIMARY KEY is DUPLICATED */
  '', '', '', 0
FROM `{$tableSalesruleCoupon}`
WHERE
  is_primary IS NOT NULL /* is_primary = 1 */
ON DUPLICATE KEY UPDATE
  coupon_type = '" . Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC . "';
");
