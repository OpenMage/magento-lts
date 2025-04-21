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
    $this->getTable('salesrule'),
    'apply_to_shipping',
    "tinyint(1) unsigned not null default '0' after simple_free_shipping",
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
    'rule_id',
);

$installer->getConnection()->addConstraint(
    'SALESRULE_LABEL_STORE',
    $this->getTable('salesrule/label'),
    'store_id',
    $this->getTable('core/store'),
    'store_id',
);
