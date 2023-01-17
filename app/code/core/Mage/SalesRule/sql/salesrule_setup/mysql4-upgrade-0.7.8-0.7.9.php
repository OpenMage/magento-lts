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
    $this->getTable('salesrule'),
    'apply_to_shipping',
    "tinyint(1) unsigned not null default '0' after simple_free_shipping"
);

$installer->run("
CREATE TABLE `{$this->getTable('salesrule/label')}` (
   `label_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `rule_id` int(10) unsigned NOT NULL,
   `store_id` smallint(5) unsigned NOT NULL,
   `label` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`label_id`),
   UNIQUE KEY `UNQ_RULE_STORE` (`rule_id`,`store_id`),
   KEY `IDX_STORE_ID` (`store_id`),
   KEY `IDX_RULE_ID` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'SALESRULE_LABEL_RULE',
    $this->getTable('salesrule/label'),
    'rule_id',
    $this->getTable('salesrule'),
    'rule_id'
);

$installer->getConnection()->addConstraint(
    'SALESRULE_LABEL_STORE',
    $this->getTable('salesrule/label'),
    'store_id',
    $this->getTable('core/store'),
    'store_id'
);
