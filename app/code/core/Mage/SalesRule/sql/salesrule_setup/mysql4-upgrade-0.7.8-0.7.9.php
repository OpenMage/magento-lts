<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Sales_Model_Mysql4_Setup */
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
