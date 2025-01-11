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
